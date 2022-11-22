<?php

namespace App\Http\Controllers;

use App\Helper\Reply;
use App\Models\Country;
use App\Models\EmergencyContact;

class ProfileSettingController extends AccountBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.profileSettings';
        $this->activeSettingMenu = 'profile_settings';
    }

    public function index()
    {
        $tab = request('tab');
 
        $this->salutations = ['mr', 'mrs', 'miss', 'dr', 'sir', 'madam'];
 
        $this->view = 'profile-settings.ajax.profile';
            

        ($tab == '') ? $this->activeTab = 'profile' : $this->activeTab = $tab;

        if (request()->ajax()) {
            $html = view($this->view, $this->data)->render();
            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle, 'activeTab' => $this->activeTab]);
        }

        return view('profile-settings.index', $this->data);
    }

}
