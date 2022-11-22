<?php

namespace App\Http\Controllers;

use App\DataTables\EmployeesDataTable;
use App\Helper\Files;
use App\Helper\Reply;
use App\Http\Requests\Admin\Employee\ImportProcessRequest;
use App\Http\Requests\Admin\Employee\ImportRequest;
use App\Http\Requests\Admin\Employee\StoreRequest;
use App\Http\Requests\Admin\Employee\UpdateRequest;
use App\Imports\EmployeeImport;
use App\Jobs\ImportEmployeeJob;
use App\Models\Dc_apj;
use App\Models\Dc_gardu_induk;
use App\Models\Designation;
use App\Models\EmployeeDetails;
use App\Models\Role;
use App\Models\RoleUser;
use App\Models\Team;
use App\Models\User;
use Artisan;
use Bus;
use Carbon\Carbon;
use DB;
use Excel;
// use Request;
use Illuminate\Http\Request;
use Maatwebsite\Excel\HeadingRowImport;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

class EmployeeController extends AccountBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.employees';
        $this->middleware(function ($request, $next) {
            abort_403(!in_array('employees', $this->user->modules));
            return $next($request);
        });
    }

    /**
     * @param EmployeesDataTable $dataTable
     * @return mixed|void
     */
    public function index(EmployeesDataTable $dataTable)
    {
        $viewPermission = user()->permission('view_employees');
        abort_403(!in_array($viewPermission, ['all', 'added', 'owned', 'both']));

        if (!request()->ajax()) {
            $this->employees = User::allEmployees(); 
            $this->designations = Designation::allDesignations();
            $this->totalEmployees = count($this->employees);
            $this->roles = Role::where('name', '<>', 'client')
                ->orderBy('id', 'asc')->get();
        }

        return $dataTable->render('employees.index', $this->data);
    }

    /**
     * XXXXXXXXXXX
     *
     * @return \Illuminate\Http\Response
     */
    public function employeApj(Request $request){
        $data['gi'] =Dc_gardu_induk::where('APJ_ID',$request->id)->get(['GARDU_INDUK_ID as id','GARDU_INDUK_NAMA as nama']); 
        return response()->json($data);
    }
    public function create()
    {
        $this->pageTitle = __('app.add') . ' ' . __('app.employee');

        $addPermission = user()->permission('add_employees');
        abort_403(!in_array($addPermission, ['all', 'added']));

        $this->teams = Team::all();
 
        $this->designations = Designation::allDesignations();
        $this->apj = Dc_apj::select('APJ_ID','APJ_NAMA')->whereNotIn('APJ_ID',  ['12','13'])->get();
        $this->gi = Dc_gardu_induk::select('GARDU_INDUK_ID','GARDU_INDUK_NAMA')->get();
        $this->lastEmployeeID = EmployeeDetails::max('id');

        $employee = new EmployeeDetails();
 
        if (request()->ajax()) {
            $html = view('employees.ajax.create', $this->data)->render();
            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        $this->view = 'employees.ajax.create';

        return view('employees.create', $this->data);

    }

    public function assignRole(Request $request)
    {
        $changeEmployeeRolePermission = user()->permission('change_employee_role');

        abort_403($changeEmployeeRolePermission != 'all');

        $userId = $request->userId;
        $roleId = $request->role;
        $employeeRole = Role::where('name', 'employee')->first();

        $user = User::withoutGlobalScopes(['active'])->findOrFail($userId);

        RoleUser::where('user_id', $user->id)->delete();
        $user->roles()->attach($employeeRole->id);

        if ($employeeRole->id != $roleId) {
            $user->roles()->attach($roleId);
        }

        $user->assignUserRolePermission($roleId);

        return Reply::success(__('messages.roleAssigned'));
    }

    /**
     * @param StoreRequest $request
     * @return array
     * @throws \Froiden\RestAPI\Exceptions\RelatedResourceNotFoundException
     */
    public function store(StoreRequest $request)
    {
        $addPermission = user()->permission('add_employees');
        abort_403(!in_array($addPermission, ['all', 'added']));

        DB::beginTransaction();
        try {
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = bcrypt($request->password);
            $user->mobile = $request->mobile; 
            $user->gender = $request->gender;

            if ($request->has('login')) {
                $user->login = $request->login;
            }
 
            if ($request->hasFile('image')) {
                Files::deleteFile($user->image, 'avatar');
                $user->image = Files::upload($request->image, 'avatar', 300);
            }
 

            $user->save(); 
            if ($user->id) {
                $employee = new EmployeeDetails();
                $employee->user_id = $user->id;
                $this->employeeData($request, $employee);
                $employee->save();
 
            }

            $employeeRole = Role::where('name', 'employee')->first();
            $user->attachRole($employeeRole);
            $user->assignUserRolePermission($employeeRole->id); 

            // Commit Transaction
            DB::commit();

        } catch (\Swift_TransportException $e) {
            // Rollback Transaction
            DB::rollback();
            return Reply::error('Please configure SMTP details to add employee. Visit Settings -> notification setting to set smtp', 'smtp_error');
        } catch (\Exception $e) {
            // Rollback Transaction
            DB::rollback();
            // return Reply::error('Some error occurred when inserting the data. Please try again or contact support');
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }

        return Reply::successWithData(__('messages.employeeAdded'), ['redirectUrl' => route('employees.index')]);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function applyQuickAction(Request $request)
    {
        switch ($request->action_type) {
        case 'delete':
            $this->deleteRecords($request);
                return Reply::success(__('messages.deleteSuccess'));
        case 'change-status':
            $this->changeStatus($request);
                return Reply::success(__('messages.statusUpdatedSuccessfully'));
        default:
                return Reply::error(__('messages.selectAction'));
        }
    }

    protected function deleteRecords($request)
    {
        abort_403(user()->permission('delete_employees') != 'all');

        User::withoutGlobalScope('active')->whereIn('id', explode(',', $request->row_ids))->delete();
    }

    protected function changeStatus($request)
    {
        abort_403(user()->permission('edit_employees') != 'all');

        User::withoutGlobalScope('active')->whereIn('id', explode(',', $request->row_ids))->update(['status' => $request->status]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->employee = User::withoutGlobalScope('active')->with('employeeDetail')->findOrFail($id);

        $this->editPermission = user()->permission('edit_employees');

        abort_403(!($this->editPermission == 'all'
        || ($this->editPermission == 'added' && $this->employee->employeeDetail->added_by == user()->id)
        || ($this->editPermission == 'owned' && $this->employee->id == user()->id)
        || ($this->editPermission == 'both' && ($this->employee->id == user()->id || $this->employee->employeeDetail->added_by == user()->id))
        ));

        $this->pageTitle = __('app.update') . ' ' . __('app.employee'); 
        $this->teams = Team::allDepartments();
        $this->designations = Designation::allDesignations(); 
        // $this->apj = Dc_apj::select('APJ_ID','APJ_NAMA')->whereNotIn('APJ_ID',  ['12','13'])->get(); 
        $this->apj = Dc_apj::select('APJ_ID','APJ_NAMA')->get(); 
        $this->gi = Dc_gardu_induk::select('GARDU_INDUK_ID','GARDU_INDUK_NAMA')->get();

        if (request()->ajax()) {
            $html = view('employees.ajax.edit', $this->data)->render();
            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        $this->view = 'employees.ajax.edit';

        return view('employees.create', $this->data);

    }

    /**
     * @param UpdateRequest $request
     * @param int $id
     * @return array
     * @throws \Froiden\RestAPI\Exceptions\RelatedResourceNotFoundException
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function update(UpdateRequest $request, $id)
    {

        $user = User::withoutGlobalScope('active')->findOrFail($id);
        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->password != '') {
            $user->password = bcrypt($request->password);
        }

        $user->mobile = $request->mobile; 
        $user->gender = $request->gender;

        if (request()->has('status')) {
            $user->status = $request->status;
        }

        if($id != user()->id){
            $user->login = $request->login;
        }
 

        if ($request->image_delete == 'yes') {
            Files::deleteFile($user->image, 'avatar');
            $user->image = null;
        }

        if ($request->hasFile('image')) {

            Files::deleteFile($user->image, 'avatar');
            $user->image = Files::upload($request->image, 'avatar', 300);
        }

        if ($request->has('telegram_user_id')) {
            $user->telegram_user_id = $request->telegram_user_id;
        }

        $user->save();
 

        $employee = EmployeeDetails::where('user_id', '=', $user->id)->first();

        if (empty($employee)) {
            $employee = new EmployeeDetails();
            $employee->user_id = $user->id;
        }

        $this->employeeData($request, $employee);

        $employee->last_date = null;

        if ($request->last_date != '') {
            $employee->last_date = Carbon::createFromFormat($this->global->date_format, $request->last_date)->format('Y-m-d');
        }

        $employee->save();
 

        if (user()->id == $user->id) {
            session()->forget('user');
        }

        return Reply::successWithData(__('messages.updateSuccess'), ['redirectUrl' => route('employees.index')]);
    }

    /**
     * @param int $id
     * @return array
     */
    public function destroy($id)
    {
        $user = User::withoutGlobalScope('active')->findOrFail($id);
        $this->deletePermission = user()->permission('delete_employees');

        abort_403(!($this->deletePermission == 'all' || ($this->deletePermission == 'added' && $user->employeeDetail->added_by == user()->id)));


        if ($user->id == 1) {
            return Reply::error(__('messages.adminCannotDelete'));
        }
  

        User::withoutGlobalScope('active')->where('id', $id)->delete();
        return Reply::success(__('messages.employeeDeleted'));

    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->employee = User::with(['employeeDetail', 'employeeDetail.designation', 'employeeDetail.department'])->withoutGlobalScope('active')->with('employee')->findOrFail($id);

        $this->viewPermission = user()->permission('view_employees');

        if (!$this->employee->hasRole('employee')) {
            abort(404);
        }

        abort_403(!(
            $this->viewPermission == 'all'
            || ($this->viewPermission == 'added' && $this->employee->employeeDetail->added_by == user()->id)
            || ($this->viewPermission == 'owned' && $this->employee->employeeDetail->user_id == user()->id)
            || ($this->viewPermission == 'both' && ($this->employee->employeeDetail->user_id == user()->id || $this->employee->employeeDetail->added_by == user()->id))
        ));

        $this->pageTitle = ucfirst($this->employee->name);
        $this->view = 'employees.ajax.profile';
 
        if (request()->ajax()) {
            $html = view('employees.ajax.profile', $this->data)->render();
            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        return view('employees.create', $this->data);

    }

    /**
     * XXXXXXXXXXX
     *
     * @return array
     */
     

    /**
     * XXXXXXXXXXX
     *
     * @return array
     */
    

    public function byDepartment($id)
    {
        $users = User::join('employee_details', 'employee_details.user_id', '=', 'users.id');

        if ($id != 0) {
            $users = $users->where('employee_details.department_id', $id);
        }

        $users = $users->select('users.*')->get();

        $options = '';

        foreach ($users as $item) {
            $options .= '<option  data-content="<div class=\'d-inline-block mr-1\'><img class=\'taskEmployeeImg rounded-circle\' src=' . $item->image_url . ' ></div>  ' . $item->name . '" value="' . $item->id . '"> ' . $item->name . ' </option>';
        }

        return Reply::dataOnly(['status' => 'success', 'data' => $options]);
    }
 

    /**
     * XXXXXXXXXXX
     *
     * @return \Illuminate\Http\Response
     */
     

    /**
     * @param mixed $request
     * @param mixed $employee
     */
    public function employeeData($request, $employee): void
    {
        $employee->employee_id = $request->employee_id;
        $employee->address = $request->address;  
        $employee->department_id = $request->department;
        $employee->apj_id = $request->employee_apj;
        $employee->gi_id = $request->employee_gi;
        $employee->designation_id = $request->designation;
        $employee->joining_date = Carbon::createFromFormat($this->global->date_format, $request->joining_date)->format('Y-m-d');
        $employee->place_of_birth = $request->place_of_birth;
        $employee->date_of_birth = $request->date_of_birth ? Carbon::createFromFormat($this->global->date_format, $request->date_of_birth)->format('Y-m-d') : null;
    }

    public function importMember()
    {
        $this->pageTitle = __('app.importExcel') . ' ' . __('app.employee');

        $addPermission = user()->permission('add_employees');
        abort_403(!in_array($addPermission, ['all', 'added']));


        if (request()->ajax()) {
            $html = view('employees.ajax.import', $this->data)->render();
            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        $this->view = 'employees.ajax.import';

        return view('employees.create', $this->data);
    }

    public function importStore(ImportRequest $request)
    {
        $this->file = Files::upload($request->import_file, 'import-files', false, false, false);
        $excelData = Excel::toArray(new EmployeeImport, public_path('user-uploads/import-files/' . $this->file))[0];
        $this->hasHeading = $request->has('heading');
        $this->heading = array();

        $this->columns = EmployeeImport::$field;
        $this->importMatchedColumns = array();
        $this->matchedColumns = array();

        if ($this->hasHeading) {
            $this->heading = (new HeadingRowImport)->toArray(public_path('user-uploads/import-files/' . $this->file))[0][0];

            // Excel Format None for get Heading Row Without Format and after change back to config
            HeadingRowFormatter::default('none');
            $this->fileHeading = (new HeadingRowImport)->toArray(public_path('user-uploads/import-files/' . $this->file))[0][0];
            HeadingRowFormatter::default(config('excel.imports.heading_row.formatter'));

            array_shift($excelData);
            $this->matchedColumns = collect($this->columns)->whereIn('id', $this->heading)->pluck('id');
            $importMatchedColumns = array();

            foreach ($this->matchedColumns as $matchedColumn) {
                $importMatchedColumns[$matchedColumn] = 1;
            }

            $this->importMatchedColumns = $importMatchedColumns;
        }

        $this->importSample = array_slice($excelData, 0, 5);

        $view = view('employees.ajax.import_progress', $this->data)->render();

        return Reply::successWithData(__('messages.importUploadSuccess'), ['view' => $view]);
    }

    public function importProcess(ImportProcessRequest $request)
    {
        // clear previous import
        Artisan::call('queue:clear database --queue=import_employee');
        Artisan::call('queue:flush');
        // Get index of an array not null value with key
        $columns = array_filter($request->columns, function ($value) {
            return $value !== null;
        });

        $excelData = Excel::toArray(new EmployeeImport, public_path('user-uploads/import-files/' . $request->file))[0];

        if ($request->has_heading) {
            array_shift($excelData);
        }

        $jobs = [];

        foreach ($excelData as $row) {

            $jobs[] = (new ImportEmployeeJob($row, $columns));
        }

        $batch = Bus::batch($jobs)->onConnection('database')->onQueue('import_employee')->name('import_employee')->dispatch();

        Files::deleteFile($request->file, 'import-files');

        return Reply::successWithData(__('messages.importProcessStart'), ['batch' => $batch]);
    }

}
