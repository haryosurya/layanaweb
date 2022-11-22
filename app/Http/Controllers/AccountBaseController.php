<?php

namespace App\Http\Controllers;

use App\Models\ProjectActivity;
use App\Models\ProjectTimeLog;
use App\Models\TaskHistory;
use App\Models\UniversalSearch;
use App\Models\UserActivity;
use App\Traits\FileSystemSettingTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use Pusher\Pusher;

class AccountBaseController extends Controller
{
    use FileSystemSettingTrait;

    /**
     * UserBaseController constructor.
     */
    
    public function __construct()
    {
        parent::__construct();

        if (!isRunningInConsoleOrSeeding()) {
            $this->currentRouteName = request()->route()->getName();
        }

        $this->middleware(function ($request, $next) {

            abort_403(!user()->admin_approval && request()->ajax());

            if (!user()->admin_approval && Route::currentRouteName() != 'account_unverified') {
                return redirect(route('account_unverified'));
            }

            $this->setFileSystemConfigs();
            $this->languageSettings = language_setting();
            $this->adminTheme = admin_theme();
            // $this->pushSetting = push_setting();
            // $this->smtpSetting = smtp_setting();
            $this->pusherSettings = pusher_settings();
 
            App::setLocale(user()->locale);
            Carbon::setLocale(user()->locale);
            setlocale(LC_TIME, user()->locale . '_' . strtoupper($this->global->locale));

            $this->user = user();
            $this->modules = $this->user->modules;
            $this->unreadNotificationCount = count($this->user->unreadNotifications);

            $this->stickyNotes = $this->user->sticky;

            $this->viewTimelogPermission = user()->permission('view_timelogs');
  
            $this->appTheme = admin_theme();
           

            if (in_array('admin', user_roles())) {
                $this->checkListTotal = 7;
                $checkListCompleted = 2; // Installation and Admin Account setup

                if (!is_null(global_setting()->logo)) {
                    $checkListCompleted++;
                }

                if (!is_null(global_setting()->favicon)) {
                    $checkListCompleted++;
                }
 

                if (!is_null(global_setting()->last_cron_run)) {
                    $checkListCompleted++;
                }

                if (!is_null(user()->image)) {
                    $checkListCompleted++;
                }

                $this->checkListCompleted = $checkListCompleted;
            }

            $this->sidebarUserPermissions = sidebar_user_perms();

            return $next($request);
        });
    }
 

    /**
     * @throws \Froiden\RestAPI\Exceptions\RelatedResourceNotFoundException
     */
    public function logUserActivity($userId, $text)
    {
        $activity = new UserActivity();
        $activity->user_id = $userId;
        $activity->activity = $text;
        $activity->save();
    } 

    public function triggerPusher($channel, $event, $data)
    {
        if ($this->pusherSettings->status) {
            $pusher = new Pusher($this->pusherSettings->pusher_app_key, $this->pusherSettings->pusher_app_secret, $this->pusherSettings->pusher_app_id, array('cluster' => $this->pusherSettings->pusher_cluster, 'useTLS' => $this->pusherSettings->force_tls));
            $pusher->trigger($channel, $event, $data);
        }
    }

    public function innerSettingMenu()
    {
        $route = Route::currentRouteName();
        $data = [];

        foreach ($this->subMenuSettings as $menu) {
            if ($menu['route'] == $route) {
                $data = $menu;
                break;
            }

            if (isset($menu['children'])) {
                foreach ($menu['children'] as $subMenu) {
                    if ($route == $subMenu['route']) {
                        $data = $menu;
                        break;
                    }
                }
            }
        }

        return $data;
    } 
}
