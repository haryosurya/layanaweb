<?php

namespace App\Http\Controllers;

use App\Helper\Reply;
use App\Models\PusherSetting;
use App\Models\SlackSetting;
use App\Models\SmtpSetting;

class NotificationSettingController extends AccountBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.notificationSettings';
        $this->activeSettingMenu = 'notification_settings';
        $this->middleware(function ($request, $next) {
            abort_403(!(user()->permission('manage_notification_setting') == 'all'));
            return $next($request);
        });
    }

    public function index()
    {
        $tab = request('tab');

        $this->emailSettings = email_notification_setting();

        switch ($tab) {  
        case 'pusher-setting':
            $this->pusherSettings = PusherSetting::first();
            $this->view = 'notification-settings.ajax.pusher-setting';
                break;
        default:
            $this->smtpSetting = SmtpSetting::first();
            $this->view = 'notification-settings.ajax.email-setting';
                break;
        }

        ($tab == '') ? $this->activeTab = 'email-setting' : $this->activeTab = $tab;

        if (request()->ajax()) {
            $html = view($this->view, $this->data)->render();
            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle, 'activeTab' => $this->activeTab]);
        }

        return view('notification-settings.index', $this->data);
    }

}
