<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AttendeeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\Auth\SocialLoginController;
use App\Http\Controllers\LinkTypeViewController;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\InstallerController;
use App\Http\Controllers\FoodServiceController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\SendDetailsController;
use App\Http\Controllers\Auth\ActivateProfileController;
use App\Http\Livewire\MultiStepForm;
use App\Http\Livewire\MultiStep;
use App\Mail\DetailsMail;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\GuestController;
use App\Models\Attendee;

use Illuminate\Support\Facades\Mail;
use App\Mail\UserInviteMail;

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

// Prevents section below from being run by 'composer update'
if(file_exists(base_path('storage/app/ISINSTALLED'))){
  // generates new APP KEY if no one is set
  if(EnvEditor::getKey('APP_KEY')==''){try{Artisan::call('key:generate');} catch (exception $e) {}}
 
  // copies template meta config if none is present
  if(!file_exists(base_path("config/advanced-config.php"))){copy(base_path('storage/templates/advanced-config.php'), base_path('config/advanced-config.php'));}
 }

 // Installer
if(file_exists(base_path('INSTALLING')) or file_exists(base_path('INSTALLERLOCK'))){

  Route::get('/', [InstallerController::class, 'showInstaller'])->name('showInstaller');
  Route::post('/create-admin', [InstallerController::class, 'createAdmin'])->name('createAdmin');
  Route::post('/db', [InstallerController::class, 'db'])->name('db');
  Route::post('/mysql', [InstallerController::class, 'mysql'])->name('mysql');
  Route::post('/options', [InstallerController::class, 'options'])->name('options');
  Route::get('/mysql-test', [InstallerController::class, 'mysqlTest'])->name('mysqlTest');
  Route::get('/skip', function () {Artisan::call('db:seed', ['--class' => 'AdminSeeder',]); return redirect(url(''));});
  Route::post('/editConfigInstaller', [InstallerController::class, 'editConfigInstaller'])->name('editConfigInstaller');

  Route::get('{any}', function() {
    if(!DB::table('users')->get()->isEmpty()){
    if(file_exists(base_path("INSTALLING")) and !file_exists(base_path('INSTALLERLOCK'))){unlink(base_path("INSTALLING"));header("Refresh:0");}
    } else {
      return redirect(url(''));
    }
  })->where('any', '.*');

}else{

// Disables routes if in Maintenance Mode
if(env('MAINTENANCE_MODE') != 'true'){

require __DIR__.'/home.php';

//Redirect if no page URL is set
Route::get('/@', function () {
    return redirect('/studio/no_page_name');
});

//Show diagnose page
Route::get('/panel/diagnose', function () {
        return view('panel/diagnose', []);
});

//Public route
$custom_prefix = config('advanced-config.custom_url_prefix');
Route::get('/going/{id?}', [UserController::class, 'clickNumber'])->where('link', '.*')->name('clickNumber')->middleware('disableCookies');
Route::get('/info/{id?}', [AdminController::class, 'redirectInfo'])->name('redirectInfo');
if($custom_prefix != ""){Route::get('/' . $custom_prefix . '{littlelink}', [UserController::class, 'littlelink'])->name('littlelink');}
Route::get('/@{littlelink}', [UserController::class, 'littlelink'])->name('littlelink')->middleware('disableCookies');
Route::get('/pages/'.strtolower(footer('Terms')), [AdminController::class, 'pagesTerms'])->name('pagesTerms')->middleware('disableCookies');
Route::get('/pages/'.strtolower(footer('Privacy')), [AdminController::class, 'pagesPrivacy'])->name('pagesPrivacy')->middleware('disableCookies');
Route::get('/pages/'.strtolower(footer('Contact')), [AdminController::class, 'pagesContact'])->name('pagesContact')->middleware('disableCookies');
Route::get('/theme/@{littlelink}', [UserController::class, 'theme'])->name('theme');
Route::get('/vcard/{id?}', [UserController::class, 'vcard'])->name('vcard');
Route::get('/u/{id?}', [UserController::class, 'userRedirect'])->name('userRedirect');
Route::post('/send-details', [SendDetailsController::class, 'sendDetailsEmail'])->name('send.details');
Route::get('/profile-setup', [MultiStepForm::class, 'render']);
Route::get('/removeLink/{id}', [ActivateProfileController::class, 'deleteLink'])->name('removeLink');
Route::get('/setup-profile/{code}', [ActivateProfileController::class, 'setup'])->name('setupProfile');
Route::get('/get-started/{code}', [ActivateProfileController::class, 'getStarted'])->name('getStarted');
Route::get('/links-setup/{code}', [ActivateProfileController::class, 'links'])->name('linksProfile');
Route::post('/setup-profile/{code}/submit', [ActivateProfileController::class, 'create_profile'])->name('create_profile');
Route::post('/setup-profile/{code}/save-link', [ActivateProfileController::class, 'saveLink'])->name('saveLink');
Route::get('/linkparamform_part/{typeid}/{linkid}', [ActivateProfileController::class, 'getParamForm'])->name('linkparamform.part');
Route::get('/ticket-success', [TicketController::class, 'success_ticket']);
Route::post('/ticket-error', [TicketController::class, 'error_ticket']);



Route::get('/test-mail', function () {
    Mail::to('delavictoria12@gmail.com')->send(new UserInviteMail(
        'Sample Event',
        'John Doe',
        now()->format('Y-m-d H:i'),
        now()->addHour()->format('Y-m-d H:i'),
        '123 Event Street',
        'https://example.com'
    ));

    return 'Mail sent!';
});


Route::post('/xendit/webhook', [TicketController::class, 'handleCallback'])->withoutMiddleware('auth');
Route::post('/paymongo/webhook', [TicketController::class, 'handleCallbackPaymongo'])->withoutMiddleware('auth');
Route::get('/profilesetup', MultiStepForm::class);
Route::get('/emailtest', function(){
  return new DetailsMail();
});
Route::get('/download-vcard', [SendDetailsController::class, 'vcard'])->name('download.vcf');

//Guest
Route::get('/ticket', [GuestController::class, 'index'])->name('index');
Route::get('/ticket/{id}', [GuestController::class, 'order'])->name('order');
Route::get('/ticket/order-details/{invoiceId}', [GuestController::class, 'order_details'])->name('order_details');
Route::post('/ticket/xendit-payment', [GuestController::class, 'xendit'])->name('xendit');
Route::post('/ticket/paymongo-payment', [GuestController::class, 'paymongo'])->name('paymongo');
Route::post('/ticket/bank-transfer', [GuestController::class, 'bank_transfer'])->name('bank_transfer');

Route::get('/report', function () {return view('report');});
Route::post('/report', [UserController::class, 'report'])->name('report');

Route::get('/demo-page', [App\Http\Controllers\HomeController::class, 'demo'])->name('demo')->middleware('disableCookies');

}

Route::middleware(['auth', 'blocked', 'impersonate'])->group(function () {
//User route
Route::group([
    'middleware' => env('REGISTER_AUTH'),
], function () {
if(env('FORCE_ROUTE_HTTPS') == 'true'){URL::forceScheme('https');}
if(isset($_COOKIE['LinkCount'])){if($_COOKIE['LinkCount'] == '20'){$LinkPage = 'showLinks20';}elseif($_COOKIE['LinkCount'] == '30'){$LinkPage = 'showLinks30';}elseif($_COOKIE['LinkCount'] == 'all'){$LinkPage = 'showLinksAll';} else {$LinkPage = 'showLinks';}} else {$LinkPage = 'showLinks';} //Shows correct link number
Route::get('/dashboard', [AdminController::class, 'index'])->name('panelIndex');
Route::get('/studio/index', function(){return redirect(url('dashboard'));});
Route::get('/studio/add-link', [UserController::class, 'AddUpdateLink'])->name('showButtons');
Route::post('/studio/edit-link', [UserController::class, 'saveLink'])->name('addLink');
Route::get('/studio/edit-link/{id}', [UserController::class, 'AddUpdateLink'])->name('showLink')->middleware('link-id');
Route::post('/studio/sort-link', [UserController::class, 'sortLinks'])->name('sortLinks');
Route::get('/studio/links', [UserController::class, $LinkPage])->name($LinkPage);
Route::get('/studio/theme', [UserController::class, 'showTheme'])->name('showTheme');
Route::post('/studio/theme', [UserController::class, 'editTheme'])->name('editTheme');
Route::get('/deleteLink/{id}', [UserController::class, 'deleteLink'])->name('deleteLink')->middleware('link-id');
Route::get('/upLink/{up}/{id}', [UserController::class, 'upLink'])->name('upLink')->middleware('link-id');
Route::post('/studio/edit-link/{id}', [UserController::class, 'editLink'])->name('editLink')->middleware('link-id');
Route::get('/studio/button-editor/{id}', [UserController::class, 'showCSS'])->name('showCSS')->middleware('link-id');
Route::post('/studio/button-editor/{id}', [UserController::class, 'editCSS'])->name('editCSS')->middleware('link-id');
Route::get('/studio/page', [UserController::class, 'showPage'])->name('showPage');
Route::get('/studio/no_page_name', [UserController::class, 'showPage'])->name('showPage');
Route::post('/studio/page', [UserController::class, 'editPage'])->name('editPage');
Route::post('/studio/background', [UserController::class, 'themeBackground'])->name('themeBackground');
Route::get('/studio/rem-background', [UserController::class, 'removeBackground'])->name('removeBackground');
Route::get('/studio/profile', [UserController::class, 'showProfile'])->name('showProfile');
Route::post('/studio/profile', [UserController::class, 'editProfile'])->name('editProfile');
Route::post('/edit-icons', [UserController::class, 'editIcons'])->name('editIcons');
Route::get('/clearIcon/{id}', [UserController::class, 'clearIcon'])->name('clearIcon');
Route::get('/studio/page/delprofilepicture', [UserController::class, 'delProfilePicture'])->name('delProfilePicture');
Route::get('/studio/delete-user/{id}', [UserController::class, 'deleteUser'])->name('deleteUser')->middleware('verified');
Route::post('/auth-as', [AdminController::class, 'authAs'])->name('authAs');

// Catch all redirects
Route::get('/admin/users/all', fn() => redirect(route('showUsers')));
Route::get('/studio', fn() => redirect(url('dashboard')));
Route::get('/studio/edit-link', fn() => redirect(url('dashboard')));

if(env('ALLOW_USER_EXPORT') != false){
  Route::get('/export-links', [UserController::class, 'exportLinks'])->name('exportLinks');
  Route::get('/export-all', [UserController::class, 'exportAll'])->name('exportAll');
}
if(env('ALLOW_USER_IMPORT') != false){
  Route::post('/import-data', [UserController::class, 'importData'])->name('importData');
}
Route::get('/studio/linkparamform_part/{typeid}/{linkid}', [LinkTypeViewController::class, 'getParamForm'])->name('linkparamform.part');
});
});
}

//Social login route
Route::get('/social-auth/{provider}/callback', [SocialLoginController::class, 'providerCallback']);
Route::get('/social-auth/{provider}', [SocialLoginController::class, 'redirectToProvider'])->name('social.redirect');

Route::middleware(['auth', 'blocked', 'impersonate'])->group(function () {
//Admin route
Route::group([
    'middleware' => 'admin',
], function () {
    if(env('FORCE_ROUTE_HTTPS') == 'true'){URL::forceScheme('https');}
    Route::get('/panel/index', function(){return redirect(url('dashboard'));});
    Route::get('/admin/users', [AdminController::class, 'users'])->name('showUsers');
    Route::get('/admin/links/{id}', [AdminController::class, 'showLinksUser'])->name('showLinksUser');
    Route::get('/admin/deleteLink/{id}', [AdminController::class, 'deleteLinkUser'])->name('deleteLinkUser');
    Route::get('/admin/users/block/{block}/{id}', [AdminController::class, 'blockUser'])->name('blockUser');
    Route::get('/admin/users/status/{activate_status}/{id}', [AdminController::class, 'activeStatus'])->name('activeStatus');
    Route::get('/admin/users/verify/{verify}/{id}', [AdminController::class, 'verifyCheckUser'])->name('verifyCheckUser');
    Route::get('/admin/users/verify-mail/{verify}/{id}', [AdminController::class, 'verifyUser'])->name('verifyUser');
    Route::get('/admin/edit-user/{id}', [AdminController::class, 'showUser'])->name('showUser');
    Route::post('/admin/edit-user/{id}', [AdminController::class, 'editUser'])->name('editUser');
    Route::get('/admin/new-user', [AdminController::class, 'createNewUser'])->name('createNewUser')->middleware('max.users');
    Route::post('/admin/import-users', [AdminController::class, 'import_users'])->name('importUsers');
    Route::get('/admin/delete-user/{id}', [AdminController::class, 'deleteUser'])->name('deleteUser');
    Route::post('/admin/delete-table-user/{id}', [AdminController::class, 'deleteTableUser'])->name('deleteTableUser');
    Route::get('/admin/pages', [AdminController::class, 'showSitePage'])->name('showSitePage');
    Route::post('/admin/pages', [AdminController::class, 'editSitePage'])->name('editSitePage');
    Route::get('/admin/advanced-config', [AdminController::class, 'showFileEditor'])->name('showFileEditor');
    Route::post('/admin/advanced-config', [AdminController::class, 'editAC'])->name('editAC');
    Route::get('/admin/env', [AdminController::class, 'showFileEditor'])->name('showFileEditor');
    Route::post('/admin/env', [AdminController::class, 'editENV'])->name('editENV');
    Route::get('/admin/site', [AdminController::class, 'showSite'])->name('showSite');
    Route::post('/admin/site', [AdminController::class, 'editSite'])->name('editSite');
    Route::get('/admin/site/delavatar', [AdminController::class, 'delAvatar'])->name('delAvatar');
    Route::get('/admin/site/delfavicon', [AdminController::class, 'delFavicon'])->name('delFavicon');
    Route::get('/admin/phpinfo', [AdminController::class, 'phpinfo'])->name('phpinfo');
    Route::get('/admin/backups', [AdminController::class, 'showBackups'])->name('showBackups');
    Route::post('/admin/theme', [AdminController::class, 'deleteTheme'])->name('deleteTheme');
    Route::get('/admin/theme', [AdminController::class, 'showThemes'])->name('showThemes');
    Route::get('/update/theme', [AdminController::class, 'updateThemes'])->name('updateThemes');
    Route::get('/admin/config', [AdminController::class, 'showConfig'])->name('showConfig');
    Route::post('/admin/config', [AdminController::class, 'editConfig'])->name('editConfig');
    Route::post('/generate-qr-code', [AdminController::class, 'generateQrCode']);
    //Tickets
    Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');

    Route::get('/tickets/filter/{eventId}', [TicketController::class, 'filterTickets'])->name('tickets.filter');

    Route::get('/tickets/create', [TicketController::class, 'create'])->name('tickets.create');
    Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');
    Route::get('/tickets/edit/{id}', [TicketController::class, 'show'])->name('tickets.show');
    Route::get('/tickets/{id}/edit', [TicketController::class, 'edit'])->name('tickets.edit');
    Route::put('/tickets/{id}', [TicketController::class, 'update'])->name('tickets.update');
    Route::delete('/tickets/{id}', [TicketController::class, 'destroy'])->name('tickets.destroy');

    //groups

    Route::get('/admin/groups', [GroupController::class, 'index'])->name('groups.index'); // Show all groups
    Route::get('/admin/groups/create', [GroupController::class, 'create'])->name('groups.create'); // Show form to create a new group
    Route::post('/admin/groups', [GroupController::class, 'store'])->name('groups.store'); // Store a new group
    Route::get('/admin/groups/{id}', [GroupController::class, 'show'])->name('groups.show'); // Show a specific group
    Route::get('/admin/groups/{id}/edit', [GroupController::class, 'edit'])->name('groups.edit'); // Show form to edit a group
    Route::put('/admin/groups/{id}', [GroupController::class, 'update'])->name('groups.update'); // Update a specific group
    Route::delete('/admin/groups/{id}', [GroupController::class, 'destroy'])->name('groups.destroy'); // Delete a specific group

    //Invoice
    Route::get('/invoice', [TicketController::class, 'index_invoice'])->name('invoice.index');
    Route::get('/invoice/view/{id}', [TicketController::class, 'view_invoice'])->name('invoice.view');
    Route::post('/invoice', [TicketController::class, 'store_invoice'])->name('invoice.store');
    Route::put('/invoice/{id}', [TicketController::class, 'update_invoice'])->name('invoice.update');
    Route::get('/invoice/show/{id}', [TicketController::class, 'show_invoice']);
    Route::post('/invoice/{invoice_id}/expire', [TicketController::class, 'expireInvoice'])->name('expire.invoice');

  //Guest Ticket
    Route::post('/ticket-verification', [GuestController::class, 'guest_ticket_input'])->name('ticket.guest');
    Route::post('/scan-ticket', [GuestController::class, 'scanTicket'])->name('scan.ticket');
    Route::get('/guest-tickets', [GuestController::class, 'guest_tickets'])->name('guest_tickets.index');
    Route::get('/guest-tickets/filter/{eventId}', [GuestController::class, 'filterGuestTickets'])->name('guest_tickets.filter');
    Route::post('/guest-tickets/create', [GuestController::class, 'store'])->name('guest-tickets.store');
    Route::post('/guest-tickets/create_bulk', [GuestController::class, 'bulk_store'])->name('guest-tickets.bulk_store');
    Route::put('/guest-tickets/{id}', [GuestController::class, 'update'])->name('guest-tickets.update');
    Route::get('/guest-tickets/export-pass/{ticket_id}', [GuestController::class, 'exportPass'])->name('export.pass');
    Route::get('/guest-tickets/export-pass-bulk/{ticket_id}', [GuestController::class, 'exportPassBulk'])->name('export.pass_bulk');

    //Food Service
    Route::get('/food-services', [FoodServiceController::class, 'index'])->name('food-services.index');
    Route::get('/food-services/create', [FoodServiceController::class, 'create'])->name('food-services.create');
    Route::post('/food-services', [FoodServiceController::class, 'store'])->name('food-services.store');
    Route::get('/food-services/{id}/edit', [FoodServiceController::class, 'edit'])->name('food-services.edit');
    Route::put('/food-services/{id}', [FoodServiceController::class, 'update'])->name('food-services.update');
    Route::delete('/food-services/{id}', [FoodServiceController::class, 'destroy'])->name('food-services.destroy');

    // Food Service Claiming Interface
    Route::get('/food-service-claim', [FoodServiceController::class, 'claimInterface'])->name('food-service.claim-interface');
    Route::post('/food-service-claim/scan', [FoodServiceController::class, 'getUserStatus'])->name('food-service.scan');
    Route::post('/food-service-claim/process', [FoodServiceController::class, 'claimService'])->name('food-service.process-claim');
    Route::post('/food-service-claim/unclaim', [FoodServiceController::class, 'unclaimService'])->name('food-service.unclaim');

    // Food Service Reports
    Route::get('/food-service-reports', [FoodServiceController::class, 'reports'])->name('food-service.reports');
    Route::get('/food-service-reports/export/{eventId}', [FoodServiceController::class, 'exportReport'])->name('food-service.export');
    Route::get('/food-service-reports/event/{eventId}', [FoodServiceController::class, 'getEventReport'])->name('food-service.event-report');


    //Discount
    Route::get('/discounts', [DiscountController::class, 'index'])->name('discounts.index');           // List all discounts
    Route::get('/discounts/create', [DiscountController::class, 'create'])->name('discounts.create');   // Show create form
    Route::post('/discounts', [DiscountController::class, 'store'])->name('discounts.store');           // Store new discount
    Route::get('/discounts/{discount}', [DiscountController::class, 'show'])->name('discounts.show');   // Show a specific discount
    Route::get('/discounts/{discount}/edit', [DiscountController::class, 'edit'])->name('discounts.edit'); // Show edit form
    Route::put('/discounts/{discount}', [DiscountController::class, 'update'])->name('discounts.update'); // Update a discount
    Route::delete('/discounts/{discount}', [DiscountController::class, 'destroy'])->name('discounts.destroy'); // Delete a discount
    Route::post('/validate-discount', [DiscountController::class, 'validateDiscount'])->name('validate.discount');
    //Invoice Logs
    Route::get('/invoice/logs', [TicketController::class, 'index_invoice_logs'])->name('invoice_logs.index');
    //Events
    Route::get('event-calendar', [ScheduleController::class, 'index']);
    Route::get('/events', [ScheduleController::class, 'getEvents']);
    Route::get('/events/delete/{id}', [ScheduleController::class, 'deleteEvent']);
    Route::post('/events/{id}', [ScheduleController::class, 'update']);
    Route::post('/events/{id}/resize', [ScheduleController::class, 'resize']);
    Route::get('/events/search', [ScheduleController::class, 'search']);

    Route::get('event-list', [ScheduleController::class, 'index_list'])->name('events.index_list');
    Route::put('/event-list/{id}/update', [ScheduleController::class, 'update_event'])->name('events.update_event'); 
    Route::get('/event-list/create', [ScheduleController::class, 'create_event'])->name('events.create');
    Route::get('/event-list/{id}/edit', [ScheduleController::class, 'edit'])->name('events.edit');
    Route::post('/event-list/store', [ScheduleController::class, 'store'])->name('events.store'); 
    Route::view('add-schedule', 'calendar.add');
    Route::post('create-schedule', [ScheduleController::class, 'create']);

    //Attendance
    Route::get('/attendance/success', function () {
      return view('calendar.attendance-success');
    })->name('attendance.success');

    Route::get('/attendance/error', function () {
      return view('calendar.attendance-error');
    })->name('attendance.error');
    
    Route::get('attendance/fetch', [AttendanceController::class, 'fetch'])->name('attendance.fetch');
    Route::get('attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::get('attendance/live-preview', [AttendanceController::class, 'index_live'])->name('attendance.index_live');
    Route::get('/attendance/live-attendance-user/{eventId}', [AttendanceController::class, 'get_live_user'])->name('attendance.get_live_user');
    Route::post('/attendance-create', [AttendanceController::class, 'store'])->name('attendance.store');
    Route::put('/update-attendance', [AttendanceController::class, 'update'])->name('updateAttendance');

    Route::post('/attendance-input', [AttendanceController::class, 'attendance_input'])->name('attendance.page');
    Route::delete('/attendance/delete/{id}', [AttendanceController::class, 'deleteAttendance']);




    Route::get('/send-test-email', [AdminController::class, 'SendTestMail'])->name('SendTestMail');
    Route::get('/auth-as/{id}', [AdminController::class, 'authAsID'])->name('authAsID');
    Route::get('/theme-updater', function () {return view('studio/theme-updater', []);});
    Route::get('/update', function () {return view('update', []);});
    Route::get('/backup', function () {return view('backup', []);});

    Route::group(['namespace'=>'App\Http\Controllers\Admin', 'prefix'=>'admin', 'as'=>'admin'],function() {
        //Route::resource('/admin/linktype', LinkTypeController::class);
        Route::resources([
            'linktype'=>LinkTypeController::class
        ]);
    });
  

    Route::get('/updating', function (\Codedge\Updater\UpdaterManager $updater) {

  // Check if new version is available
  if($updater->source()->isNewVersionAvailable() and (file_exists(base_path("backups/CANUPDATE")) or env('SKIP_UPDATE_BACKUP') == true)) {

    EnvEditor::editKey('MAINTENANCE_MODE', true);

      // Get the current installed version
      echo $updater->source()->getVersionInstalled();

      // Get the new version available
      $versionAvailable = $updater->source()->getVersionAvailable();

      // Create a release
      $release = $updater->source()->fetch($versionAvailable);

      // Run the update process
      $updater->source()->update($release);

      if(env('SKIP_UPDATE_BACKUP') != true) {unlink(base_path("backups/CANUPDATE"));}

      echo "<meta http-equiv=\"refresh\" content=\"0; " . url()->current() . "/../update?finishing\" />";

  } else {
    echo "<meta http-equiv=\"refresh\" content=\"0; " . url()->current() . "/../update?error\" />";
  }

});

}); // ENd Admin authenticated routes
});

// Displays Maintenance Mode page
if(env('MAINTENANCE_MODE') == 'true'){
Route::get('/{any}', function () {
  return view('maintenance');
  })->where('any', '.*');
}

require __DIR__.'/auth.php';

if(config('advanced-config.custom_url_prefix') == ""){
  Route::get('/{littlelink}', [UserController::class, 'littlelink'])->name('littlelink');
}