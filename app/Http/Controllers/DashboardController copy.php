<?php

namespace App\Http\Controllers;

use App\Helper\Reply;
use App\Traits\OverviewDashboard;
use Illuminate\Http\Request;

class DashboardController extends AccountBaseController
{ 
    use  OverviewDashboard;
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.dashboard';
        $this->middleware(function ($request, $next) {
            $this->viewOverviewDashboard = user()->permission('view_overview_dashboard');
            return $next($request);
        });

    }

    /**
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response|mixed|void
     */
    public function index()
    {

        if (in_array('admin', user_roles()) || in_array('dashboards', user_modules())) {
            // $this->isCheckScript();

            // $tab = request('tab'); 
            // if (request()->ajax()) {
            //     $html = view($this->view, $this->data)->render();
            //     return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
            // }

            // $this->activeTab = ($tab == '') ? 'overview' : $tab;
            $this->overviewDashboard(); 
            if (request()->ajax()) {
                $html = view($this->view, $this->data)->render();
                return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
            }
            return view('dashboard.admin', $this->data);
        }

        if (in_array('employee', user_roles())) { 
            return view('dashboard.employee.index', $this->data);

        }
 
    }

     

    public function checklist()
    {
        if (in_array('admin', user_roles())) {
            $this->isCheckScript();
            return view('dashboard.checklist', $this->data);
        }
    }

    /**
     * @return array|\Illuminate\Http\Response
     */
    public function memberDashboard()
    {
        abort_403 (!in_array('employee', user_roles()));
        return $this->employeeDashboard();
    }
 
    public function accountUnverified()
    {
        return view('dashboard.unverified', $this->data);
    }

}
