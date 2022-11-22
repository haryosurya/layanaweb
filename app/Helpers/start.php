<?php

/*
|--------------------------------------------------------------------------
| Register Namespaces And Routes
|--------------------------------------------------------------------------
|
| When a module starting, this file will executed automatically. This helps
| to register some namespaces like translator or view. Also this file
| will load the routes file for each module. You may also modify
| this file as you want.
|
*/
 
use App\Models\Permission;
use App\Models\UserPermission;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Froiden\RestAPI\Exceptions\UnauthorizedException;


if (!function_exists('user')) {

    /**
     * Return current logged in user
     */
    function user()
    {
        if (session()->has('user')) {
            return session('user');
        }

        $user = auth()->user();

        if ($user) {
            session(['user' => $user]);
            return session('user');
        }

        return null;
    }

}

if (!function_exists('user_roles')) {

    /**
     * Return current logged in user
     */
    // @codingStandardsIgnoreLine
    function user_roles()
    {
        if (session()->has('user_roles')) {
            return session('user_roles');
        }

        $user = user();

        if ($user) {
            session(['user_roles' => user()->roles->pluck('name')->toArray()]);
            return session('user_roles');
        }

        return null;
    }

}

if (!function_exists('admin_theme')) {

    // @codingStandardsIgnoreLine
    function admin_theme()
    {
        if (!session()->has('admin_theme')) {
            session(['admin_theme' => \App\Models\ThemeSetting::where('panel', 'admin')->first()]);
        }

        return session('admin_theme');
    }

}
 
if (!function_exists('employee_theme')) {

    // @codingStandardsIgnoreLine
    function employee_theme()
    {
        if (!session()->has('employee_theme')) {
            session(['employee_theme' => \App\Models\ThemeSetting::where('panel', 'employee')->first()]);
        }

        return session('employee_theme');
    }

}

if (!function_exists('global_setting')) {

    // @codingStandardsIgnoreLine
    function global_setting()
    {
        if (!session()->has('global_setting')) {
            $setting = \App\Models\Setting::first();
            session(['global_setting' => $setting]);
        }

        return session('global_setting');
    }

}

if (!function_exists('email_notification_setting')) {

    // @codingStandardsIgnoreLine
    function email_notification_setting()
    {

        if (in_array('client', user_roles()) || in_array('employee', user_roles())) {
            return \App\Models\EmailNotificationSetting::all();
        }

        if (!session()->has('email_notification_setting')) {
            session(['email_notification_setting' => \App\Models\EmailNotificationSetting::all()]);
        }

        return session('email_notification_setting');
    }

} 

if (!function_exists('language_setting')) {

    // @codingStandardsIgnoreLine
    function language_setting()
    {
        if (!session()->has('language_setting')) {
            session(['language_setting' => \App\Models\LanguageSetting::where('status', 'enabled')->get()]);
        }

        return session('language_setting');
    }

}
 
if (!function_exists('storage_setting')) {

    // @codingStandardsIgnoreLine
    function storage_setting()
    {
        if (!session()->has('storage_setting')) {
            $setting = \App\Models\StorageSetting::where('status', 'enabled')->first();

            session(['storage_setting' => $setting]);
        }

        return session('storage_setting');
    }

}
 

if (!function_exists('asset_url')) {

    // @codingStandardsIgnoreLine
    function asset_url($path)
    {
        $path = 'user-uploads/' . $path;
        $storageUrl = $path;

        if (!Str::startsWith($storageUrl, 'http')) {
            return url($storageUrl);
        }

        return $storageUrl;
    }

}

if (!function_exists('user_modules')) {

    // @codingStandardsIgnoreLine
    function user_modules()
    {
        if (!session()->has('user_modules') && user()) {
            $user = auth()->user();

            $module = new \App\Models\ModuleSetting();

            if (in_array('admin', user_roles())) {
                $module = $module->where('type', 'admin');
            } 
            elseif (in_array('employee', user_roles())) {
                $module = $module->where('type', 'employee');
            }

            $module = $module->where('status', 'active');
            $module->select('module_name');

            $module = $module->get();
            $moduleArray = [];

            foreach ($module->toArray() as $item) {
                array_push($moduleArray, array_values($item)[0]);
            }

            session(['user_modules' => $moduleArray]);
        }

        return session('user_modules');
    }

}
  
if (!function_exists('pusher_settings')) {

    // @codingStandardsIgnoreLine
    function pusher_settings()
    {
        if (!session()->has('pusher_settings')) {
            session(['pusher_settings' => \App\Models\PusherSetting::first()]);
        }

        return session('pusher_settings');
    }

}

// if (!function_exists('main_menu_settings')) {

//     // @codingStandardsIgnoreLine
//     function main_menu_settings()
//     {
//         if (!session()->has('main_menu_settings')) {
//             session(['main_menu_settings' => \App\Models\MenuSetting::first()->main_menu]);
//         }

//         return session('main_menu_settings');
//     }

// }

// if (!function_exists('sub_menu_settings')) {

//     // @codingStandardsIgnoreLine
//     function sub_menu_settings()
//     {
//         if (!session()->has('sub_menu_settings')) {
//             session(['sub_menu_settings' => \App\Models\MenuSetting::first()->setting_menu]);
//         }

//         return session('sub_menu_settings');
//     }

// }

// if (!function_exists('isSeedingData')) {

//     /**
//      * Check if app is seeding data
//      * @return boolean
//      */
//     function isSeedingData()
//     {
//         // We set config(['app.seeding' => true]) at the beginning of each seeder. And check here
//         return config('app.seeding');
//     }

// }

if (!function_exists('isRunningInConsoleOrSeeding')) {

    /**
     * Check if app is seeding data
     * @return boolean
     */
    function isRunningInConsoleOrSeeding()
    {
        // We set config(['app.seeding' => true]) at the beginning of each seeder. And check here
        return app()->runningInConsole() || isSeedingData();
    }

}

if (!function_exists('asset_url_local_s3')) {

    // @codingStandardsIgnoreLine
    function asset_url_local_s3($path)
    {
        if (config('filesystems.default') == 's3') {
            /** @phpstan-ignore-next-line */
            $client = Storage::disk('s3')->getDriver()->getAdapter()->getClient();

            $command = $client->getCommand('GetObject', [
                'Bucket' => config('filesystems.disks.s3.bucket'),
                'Key' => $path
            ]);

            $request = $client->createPresignedRequest($command, '+30 minutes');

            return (string)$request->getUri();
        }

        $path = 'user-uploads/' . $path;
        $storageUrl = $path;

        if (!Str::startsWith($storageUrl, 'http')) {
            return url($storageUrl);
        }

        return $storageUrl;
    }

}

if (!function_exists('download_local_s3')) {

    // @codingStandardsIgnoreLine
    function download_local_s3($file, $path)
    {
        if (config('filesystems.default') == 's3') {
            $ext = pathinfo($file->filename, PATHINFO_EXTENSION);
            $fs = Storage::getDriver();
            $stream = $fs->readStream($path);

            return Response::stream(function () use ($stream) {
                fpassthru($stream);
            }, 200, [
                'Content-Type' => $ext,
                'Content-Length' => $file->size,
                'Content-disposition' => 'attachment; filename='.basename($file->filename),
            ]);
        }

        $path = 'user-uploads/' . $path;
        return response()->download($path, $file->filename);
    }

}
 

if (!function_exists('abort_403')) {

    /**
     * @param mixed $condition
     */

    // @codingStandardsIgnoreLine
    function abort_403($condition)
    {
        abort_if($condition, 403, __('messages.permissionDenied'));
    }

}
   /* API HERE */
if (!function_exists('parseUser')) {

    function parseUser()
    {
        config(['auth.defaults.guard'=>'api']);
        config(['jwt.secret' => config('restapi.jwt_secret')]);

        try {

            if (isRunningInBrowser()) {

                $user = auth()->user();

                if ($user === false) {
                    $exception =  new \Froiden\RestAPI\Exceptions\UnauthorizedException('User not found', null, 403, 403, 2006);
                    return \Froiden\RestAPI\ApiResponse::exception($exception);
                }
                return $user;

            }

            return null;

        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            if (isRunningInBrowser()) {
                throw new \Froiden\RestAPI\Exceptions\UnauthorizedException('Token has expired', null, 403, 403, 2007);
            }

            return null;

        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            if (isRunningInBrowser()) {
                $exception =  new \Froiden\RestAPI\Exceptions\UnauthorizedException('Token is invalid', null, 403, 403, 2008);
                return \Froiden\RestAPI\ApiResponse::exception($exception);
            }

            return null;

        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return null;
        }
    }

}

if (!function_exists('objetToArray')) {

    function objetToArray($adminBar){
        $reflector = new ReflectionObject($adminBar);
        $nodes = $reflector->getProperties();
        $out = [];
        foreach ($nodes as $node) {
            $nod = $reflector->getProperty($node->getName());
            $nod->setAccessible(true);
            $out[$node->getName()] = $nod->getValue($adminBar);
        }
        return $out;
    }

} 
if (!function_exists('api_user')) {

    /**
     * @return \Illuminate\Contracts\Auth\Authenticatable|\Illuminate\Http\Response|null
     * @throws UnauthorizedException
     */
    function api_user()
    {
        $user = parseUser();
        return is_a($user, '\App\Models\User')  ? $user : null;
    }

}
if (!function_exists('showDateString')) {

    /**
     * @return \Illuminate\Contracts\Auth\Authenticatable|\Illuminate\Http\Response|null
     * @throws UnauthorizedException
     */
 function showDateString($timestamp)
    {
      if ($timestamp !== NULL) {
        $date = new DateTime();
        $date->setTimestamp(intval($timestamp));
        return $date->format("d-m-Y");
      }
      return '';
    }

}

if (!function_exists('isRunningInBrowser')) {

    /**
     * Check if app is running in browser
     * @return boolean
     */
    function isRunningInBrowser()
    {
        // App should not be running in console, or if it is, then it should
        // be running unit tests. We need to check browser running because
        // we want to prevent some parts getting executed in seeders, migrations, etc.
        // which are run in console.
        return !App::runningInConsole() || (App::runningInConsole() && App::runningUnitTests());
    }

}

if (!function_exists('isSeedingData')) {

    /**
     * Check if app is seeding data
     * @return boolean
     */
    function isSeedingData()
    {
        // We set $_ENV['SEEDING'] at the begining of each seeder. And check here
        return isset($_ENV['SEEDING']) && $_ENV['SEEDING'] == true;
    }

}

if (!function_exists('isRunningTests')) {

    /**
     * Check if app is running unit tests
     * @return boolean
     */
    function isRunningTests()
    {
        // If app env is testing
        return env('APP_ENV') == 'testing';
    }

}

if (!function_exists('abort_403')) {

    /**
     * @param mixed $condition
     */

    // @codingStandardsIgnoreLine
    function abort_403($condition)
    {
        abort_if($condition, 403, __('messages.permissionDenied'));
    }

}

if (!function_exists('sidebar_user_perms')) {

    // @codingStandardsIgnoreLine
    function sidebar_user_perms()
    {
        if (!session()->has('sidebar_user_perms')) {

            $sidebarPermissionsArray = [ 
                'view_rekap_gangguan_pmt', 
                'view_inspeksi_pd', 
                'view_inspeksi_aset', 
                'view_beban_realtime', 
                'view_employees', 
                'view_employees', 
                'view_dcc',
                'view_gardu',
                'view_cubicle',
                'view_incoming_feeder',
                'manage_company_setting',
                'add_employees', 
            ];

            $sidebarPermissions = Permission::whereIn('name', $sidebarPermissionsArray)->select('id', 'name')->orderBy('id', 'asc')->get();

            $sidebarPermissionsId = $sidebarPermissions->pluck('id')->toArray();

            $sidebarUserPermissionType = UserPermission::where('user_id', user()->id)
            ->whereIn('permission_id', $sidebarPermissionsId)
            ->orderBy('id', 'asc')
            ->groupBy(['user_id', 'permission_id', 'permission_type_id'])
            ->get()->pluck('permission_type_id')->toArray();

            $sidebarUserPermissions = [];

            foreach ($sidebarPermissionsArray as $key => $value) {
                $sidebarUserPermissions[$value] = 'none';
            }

            if (count($sidebarUserPermissionType) == count($sidebarPermissions->pluck('name')->toArray())) {
                $sidebarUserPermissions = array_combine($sidebarPermissions->pluck('name')->toArray(), $sidebarUserPermissionType);
            }

            session(['sidebar_user_perms' => $sidebarUserPermissions]);
        }

        return session('sidebar_user_perms');

    }

}
 