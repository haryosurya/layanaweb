<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Route::feeds();

Route::prefix('template')->group(function () {
        
    Route::get('/', function () {
        return view('template.dashboard');
    });

    Route::group(['prefix' => 'email'], function(){
        Route::get('inbox', function () { return view('template.pages.email.inbox'); });
        Route::get('read', function () { return view('template.pages.email.read'); });
        Route::get('compose', function () { return view('template.pages.email.compose'); });
    });

    Route::group(['prefix' => 'apps'], function(){
        Route::get('chat', function () { return view('template.pages.apps.chat'); });
        Route::get('calendar', function () { return view('template.pages.apps.calendar'); });
    });

    Route::group(['prefix' => 'ui-components'], function(){
        Route::get('accordion', function () { return view('template.pages.ui-components.accordion'); });
        Route::get('alerts', function () { return view('template.pages.ui-components.alerts'); });
        Route::get('badges', function () { return view('template.pages.ui-components.badges'); });
        Route::get('breadcrumbs', function () { return view('template.pages.ui-components.breadcrumbs'); });
        Route::get('buttons', function () { return view('template.pages.ui-components.buttons'); });
        Route::get('button-group', function () { return view('template.pages.ui-components.button-group'); });
        Route::get('cards', function () { return view('template.pages.ui-components.cards'); });
        Route::get('carousel', function () { return view('template.pages.ui-components.carousel'); });
        Route::get('collapse', function () { return view('template.pages.ui-components.collapse'); });
        Route::get('dropdowns', function () { return view('template.pages.ui-components.dropdowns'); });
        Route::get('list-group', function () { return view('template.pages.ui-components.list-group'); });
        Route::get('media-object', function () { return view('template.pages.ui-components.media-object'); });
        Route::get('modal', function () { return view('template.pages.ui-components.modal'); });
        Route::get('navs', function () { return view('template.pages.ui-components.navs'); });
        Route::get('navbar', function () { return view('template.pages.ui-components.navbar'); });
        Route::get('pagination', function () { return view('template.pages.ui-components.pagination'); });
        Route::get('popovers', function () { return view('template.pages.ui-components.popovers'); });
        Route::get('progress', function () { return view('template.pages.ui-components.progress'); });
        Route::get('scrollbar', function () { return view('template.pages.ui-components.scrollbar'); });
        Route::get('scrollspy', function () { return view('template.pages.ui-components.scrollspy'); });
        Route::get('spinners', function () { return view('template.pages.ui-components.spinners'); });
        Route::get('tabs', function () { return view('template.pages.ui-components.tabs'); });
        Route::get('tooltips', function () { return view('template.pages.ui-components.tooltips'); });
    });

    Route::group(['prefix' => 'advanced-ui'], function(){
        Route::get('cropper', function () { return view('template.pages.advanced-ui.cropper'); });
        Route::get('owl-carousel', function () { return view('template.pages.advanced-ui.owl-carousel'); });
        Route::get('sortablejs', function () { return view('template.pages.advanced-ui.sortablejs'); });
        Route::get('sweet-alert', function () { return view('template.pages.advanced-ui.sweet-alert'); });
    });

    Route::group(['prefix' => 'forms'], function(){
        Route::get('basic-elements', function () { return view('template.pages.forms.basic-elements'); });
        Route::get('advanced-elements', function () { return view('template.pages.forms.advanced-elements'); });
        Route::get('editors', function () { return view('template.pages.forms.editors'); });
        Route::get('wizard', function () { return view('template.pages.forms.wizard'); });
    });

    Route::group(['prefix' => 'charts'], function(){
        Route::get('apex', function () { return view('template.pages.charts.apex'); });
        Route::get('chartjs', function () { return view('template.pages.charts.chartjs'); });
        Route::get('flot', function () { return view('template.pages.charts.flot'); });
        Route::get('morrisjs', function () { return view('template.pages.charts.morrisjs'); });
        Route::get('peity', function () { return view('template.pages.charts.peity'); });
        Route::get('sparkline', function () { return view('template.pages.charts.sparkline'); });
    });

    Route::group(['prefix' => 'tables'], function(){
        Route::get('basic-tables', function () { return view('template.pages.tables.basic-tables'); });
        Route::get('data-table', function () { return view('template.pages.tables.data-table'); });
    });

    Route::group(['prefix' => 'icons'], function(){
        Route::get('feather-icons', function () { return view('template.pages.icons.feather-icons'); });
        Route::get('flag-icons', function () { return view('template.pages.icons.flag-icons'); });
        Route::get('mdi-icons', function () { return view('template.pages.icons.mdi-icons'); });
    });

    Route::group(['prefix' => 'general'], function(){
        Route::get('blank-page', function () { return view('template.pages.general.blank-page'); });
        Route::get('faq', function () { return view('template.pages.general.faq'); });
        Route::get('invoice', function () { return view('template.pages.general.invoice'); });
        Route::get('profile', function () { return view('template.pages.general.profile'); });
        Route::get('pricing', function () { return view('template.pages.general.pricing'); });
        Route::get('timeline', function () { return view('template.pages.general.timeline'); });
    });

    Route::group(['prefix' => 'auth'], function(){
        Route::get('login', function () { return view('template.pages.auth.login'); });
        Route::get('register', function () { return view('template.pages.auth.register'); });
    });

    Route::group(['prefix' => 'error'], function(){
        Route::get('404', function () { return view('template.pages.error.404'); });
        Route::get('500', function () { return view('template.pages.error.500'); });
    });

    Route::get('/clear-cache', function() {
        Artisan::call('cache:clear');
        return "Cache is cleared";
    });

    // 404 for undefined routes
    Route::any('/{page?}',function(){
        return View::make('pages.error.404');
    })->where('page','.*');

});