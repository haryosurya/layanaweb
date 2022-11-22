<?php

namespace App\Http\Controllers;

use App\Events\NewUserRegistrationViaInviteEvent;
use App\Helper\Reply;
use App\Http\Requests\User\AcceptInviteRequest;
use App\Http\Requests\User\AccountSetupRequest;
use App\Models\EmployeeDetails;
use App\Models\Permission;
use App\Models\PermissionRole;
use App\Models\Role;
use App\Models\Setting;
use App\Models\UniversalSearch;
use App\Models\User;
use App\Models\UserDetail;
use App\Models\UserInvitation;
use App\Models\UserPermission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{

     

    /**
     * XXXXXXXXXXX
     *
     * @return \Illuminate\Http\Response
     */
    public function setupAccount(AccountSetupRequest $request)
    {
        // Update company name
        $setting = Setting::first();
        $setting->company_name = $request->company_name;
        $setting->save();

        // Create admin user
        $user = new User();
        $user->name = $request->full_name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->save();

        $employee = new UserDetail();
        $employee->user_id = $user->id;
        $employee->employee_id = 'emp-' . $user->id;
        $employee->save(); 

        // Attach roles
        $adminRole = Role::where('name', 'admin')->first();
        $employeeRole = Role::where('name', 'employee')->first();
        $user->roles()->attach($adminRole->id);
        $user->roles()->attach($employeeRole->id);

        $allPermissions = Permission::orderBy('id', 'asc')->get()->pluck('id')->toArray();

        foreach ($allPermissions as $key => $permission) {
            $user->permissionTypes()->attach([$permission => ['permission_type_id' => 4]]);
        }

        Auth::login($user);
        return Reply::success(__('messages.signupSuccess'));
    }

}
