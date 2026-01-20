<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

use GeoSot\EnvEditor\Controllers\EnvController;
use GeoSot\EnvEditor\Exceptions\EnvException;
use GeoSot\EnvEditor\Helpers\EnvFileContentManager;
use GeoSot\EnvEditor\Helpers\EnvFilesManager;
use GeoSot\EnvEditor\Helpers\EnvKeysManager;
use GeoSot\EnvEditor\Facades\EnvEditor;
use GeoSot\EnvEditor\ServiceProvider;


use Auth;
use Exception;
use ZipArchive;
use Carbon\Carbon;

use App\Models\User;
use App\Models\Admin;
use App\Models\Button;
use App\Models\Link;
use App\Models\Page;
use App\Models\UserData;
use App\Models\ProfessionalInformation;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing;
use SimpleSoftwareIO\QrCode\Facades\QrCode;


class AdminController extends Controller
{
    //Statistics of the number of clicks and links
    public function index()
    {
        $userId = Auth::user()->id;
        $littlelink_name = Auth::user()->littlelink_name;
        $links = Link::where('user_id', $userId)->select('link')->count();
        $clicks = Link::where('user_id', $userId)->sum('click_number');

        $userNumber = User::count();
        $siteLinks = Link::count();
        $siteClicks = Link::sum('click_number');

        $users = User::select('id', 'name', 'email', 'created_at', 'updated_at')->get();
        $lastMonthCount = $users->where('created_at', '>=', Carbon::now()->subDays(30))->count();
        $lastWeekCount = $users->where('created_at', '>=', Carbon::now()->subDays(7))->count();
        $last24HrsCount = $users->where('created_at', '>=', Carbon::now()->subHours(24))->count();
        $updatedLast30DaysCount = $users->where('updated_at', '>=', Carbon::now()->subDays(30))->count();
        $updatedLast7DaysCount = $users->where('updated_at', '>=', Carbon::now()->subDays(7))->count();
        $updatedLast24HrsCount = $users->where('updated_at', '>=', Carbon::now()->subHours(24))->count();

        $links = Link::where('user_id', $userId)->select('link')->count();
        $user = User::where('id', $userId)->first();
        $clicks = Link::where('user_id', $userId)->sum('click_number');
        $topLinks = Link::where('user_id', $userId)->orderby('click_number', 'desc')
            ->whereNotNull('link')->where('link', '<>', '')
            ->take(5)->get();

        $pageStats = [
            'visitors' => [
                'all' => visits('App\Models\User', $littlelink_name)->count(),
                'day' => visits('App\Models\User', $littlelink_name)->period('day')->count(),
                'week' => visits('App\Models\User', $littlelink_name)->period('week')->count(),
                'month' => visits('App\Models\User', $littlelink_name)->period('month')->count(),
                'year' => visits('App\Models\User', $littlelink_name)->period('year')->count(),
            ],
            'os' => visits('App\Models\User', $littlelink_name)->operatingSystems(),
            'referers' => visits('App\Models\User', $littlelink_name)->refs(),
            'countries' => visits('App\Models\User', $littlelink_name)->countries(),
        ];

        return view('panel/index', ['lastMonthCount' => $lastMonthCount, 'user' => $user, 'lastWeekCount' => $lastWeekCount, 'last24HrsCount' => $last24HrsCount, 'updatedLast30DaysCount' => $updatedLast30DaysCount, 'updatedLast7DaysCount' => $updatedLast7DaysCount, 'updatedLast24HrsCount' => $updatedLast24HrsCount, 'toplinks' => $topLinks, 'links' => $links, 'clicks' => $clicks, 'pageStats' => $pageStats, 'littlelink_name' => $littlelink_name, 'links' => $links, 'clicks' => $clicks, 'siteLinks' => $siteLinks, 'siteClicks' => $siteClicks, 'userNumber' => $userNumber]);
    }

    // Users page
public function users()
{
    $config = [
        'individual_prefix' => env('INDIVIDUAL_CODE_PREFIX', 'nmyl'),
        'company_prefix' => env('COMPANY_CODE_PREFIX', 'pt'),
    ];
    
    return view('panel/users', compact('config'));
}

    // Send test mail
    public function SendTestMail(Request $request)
    {
        try {
            $userId = auth()->id();
            $user = User::findOrFail($userId);

            Mail::send('auth.test', ['user' => $user], function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('Test Email');
            });

            return redirect()->route('showConfig')->with('success', 'Test email sent successfully!');
        } catch (\Exception $e) {
            return redirect()->route('showConfig')->with('fail', 'Failed to send test email.');
        }
    }

    //Block user
    public function blockUser(request $request)
    {
        $id = $request->id;
        $status = $request->block;

        if ($status == 'yes') {
            $block = 'no';
        } elseif ($status == 'no') {
            $block = 'yes';
        }

        User::where('id', $id)->update(['block' => $block]);

        return redirect('admin/users/all');
    }

    public function activeStatus(request $request)
    {
        $id = $request->id;
        $status = $request->activate_status;

        if ($status == 'activated') {
            $activate_status = 'deactivated';
        } elseif ($status == 'deactivated') {
            $activate_status = 'activated';
        }

        User::where('id', $id)->update(['activate_status' => $activate_status]);

        return redirect('admin/users/all');
    }

    //Verify user
    public function verifyCheckUser(request $request)
    {
        $id = $request->id;
        $status = $request->verify;

        if ($status == 'vip') {
            $verify = 'vip';
            UserData::saveData($id, 'checkmark', true);
        } elseif ($status == 'user') {
            $verify = 'user';
        }

        User::where('id', $id)->update(['role' => $verify]);

        return redirect(url('u') . "/" . $id);
    }

    //Verify or un-verify users emails
    public function verifyUser(request $request)
    {
        $id = $request->id;
        $status = $request->verify;

        if ($status == "true") {
            $verify = '0000-00-00 00:00:00';
        } else {
            $verify = NULL;
        }

        User::where('id', $id)->update(['email_verified_at' => $verify]);
    }
    // Replace these methods in your AdminController.php

public function generateUniqueRandomCode($length, $keyspace, $existingCodes)
{
    $maxAttempts = 100000;
    $attempts = 0;

    do {
        // FIX: Added $this-> to call the private method
        $randomCode = 'pt' . $this->random_str($length - 1, $keyspace);
        $attempts++;
    } while (in_array($randomCode, $existingCodes) && $attempts < $maxAttempts);

    if ($attempts == $maxAttempts) {
        throw new \RuntimeException("Failed to generate a unique random code.");
    }

    return $randomCode;
}

//Create new user from the Admin Panel
public function createNewUser()
{
    $names = User::pluck('name')->toArray();

    $adminCreatedNames = array_filter($names, function ($name) {
        return strpos($name, 'Unset-User-') === 0;
    });

    $numbers = array_map(function ($name) {
        return (int) str_replace('Unset-User-', '', $name);
    }, $adminCreatedNames);

    $maxNumber = !empty($numbers) ? max($numbers) : 0;
    $newNumber = $maxNumber + 1;

    $domain = parse_url(url(''), PHP_URL_HOST);
    $domain = ($domain == 'localhost') ? 'example.com' : $domain;

    $existingCodes = User::pluck('activate_code')->toArray();
    $keyspace = 'abcdefghijklmnopqrstuvwxyz1234567890';
    $randomCode = $this->generateUniqueRandomCode(7, $keyspace, $existingCodes);

    $user = User::create([
        'name' => 'Unset-User-' . $newNumber,
        'email' => strtolower($this->random_str(8)) . '@' . $domain,
        'password' => Hash::make($this->random_str(32)),
        'littlelink_name' => $randomCode,
        'role' => 'user',
        'block' => 'no',
        'activate_code' => $randomCode,
        'activate_status' => 'deactivated'
    ]);

    $userId = $user->id;

    UserData::saveData($userId, 'show-professional', true);
    UserData::saveData($userId, 'show-send-details', true);

    return redirect('admin/users/all');
}

public function createMultipleUsers(Request $request)
{
    $request->validate([
        'user_count' => 'required|integer|min:1|max:100',
        'activate_users' => 'nullable|boolean'
    ]);

    $userCount = $request->input('user_count');
    $activateUsers = $request->has('activate_users') && $request->input('activate_users') == 1;

    $names = User::pluck('name')->toArray();
    $adminCreatedNames = array_filter($names, function($name) {
        return strpos($name, 'Unset-User-') === 0;
    });

    $numbers = array_map(function($name) {
        return (int) str_replace('Unset-User-', '', $name);
    }, $adminCreatedNames);

    $maxNumber = !empty($numbers) ? max($numbers) : 0;

    $domain = parse_url(url(''), PHP_URL_HOST);
    $domain = ($domain == 'localhost') ? 'example.com' : $domain;

    // Get all existing activation codes for uniqueness check
    $existingCodes = User::pluck('activate_code')->toArray();
    $keyspace = 'abcdefghijklmnopqrstuvwxyz1234567890';

    $createdUsers = [];

    for ($i = 1; $i <= $userCount; $i++) {
        $newNumber = $maxNumber + $i;
        
        // Generate unique activation code
        $randomCode = $this->generateUniqueRandomCode(7, $keyspace, $existingCodes);
        // Add to existing codes to prevent duplicates within this batch
        $existingCodes[] = $randomCode;

        $user = User::create([
            'name' => 'Unset-User-' . $newNumber,
            'email' => strtolower($this->random_str(8)) . '@' . $domain,
            'password' => Hash::make($this->random_str(32)),
            'littlelink_name' => $randomCode,
            'role' => 'user',
            'block' => 'no',
            'activate_code' => $randomCode,
            'activate_status' => $activateUsers ? 'activated' : 'deactivated'
        ]);

        $userId = $user->id;

        UserData::saveData($userId, 'show-professional', true);
        UserData::saveData($userId, 'show-send-details', true);

        $createdUsers[] = $user;
    }

    $message = $userCount . ' user(s) created successfully';
    if ($activateUsers) {
        $message .= ' and activated.';
    } else {
        $message .= '. Users will need to activate their accounts using their activation codes.';
    }

    return redirect('admin/users/all')->with('success', $message);
}

// This private method should be at the bottom of your controller
private function random_str(
    int $length = 64,
    string $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'
): string {
    if ($length < 1) {
        throw new \RangeException("Length must be a positive integer");
    }
    $pieces = [];
    $max = mb_strlen($keyspace, '8bit') - 1;
    for ($i = 0; $i < $length; ++$i) {
        $pieces[] = $keyspace[random_int(0, $max)];
    }
    return implode('', $pieces);
}





    //Delete existing user
    public function deleteUser(request $request)
    {
        $id = $request->id;

        Link::where('user_id', $id)->delete();

        Schema::disableForeignKeyConstraints();

        $user = User::find($id);
        $user->forceDelete();

        Schema::enableForeignKeyConstraints();

        return redirect('admin/users/all');
    }

    //Delete existing user with POST request
    public function deleteTableUser(request $request)
    {
        $id = $request->id;

        Link::where('user_id', $id)->delete();

        Schema::disableForeignKeyConstraints();

        $user = User::find($id);
        $user->forceDelete();

        Schema::enableForeignKeyConstraints();
    }

    //Show user to edit
    public function showUser(request $request)
    {
        $id = $request->id;

        $data['user'] = User::where('id', $id)->get();

        Log::info($data['user']);

        return view('panel/edit-user', $data);
    }

    //Show link, click number, up link in links page
    public function showLinksUser(request $request)
    {
        $id = $request->id;

        $data['user'] = User::where('id', $id)->get();

        $data['links'] = Link::select('id', 'link', 'title', 'order', 'click_number', 'up_link', 'links.button_id')->where('user_id', $id)->orderBy('up_link', 'asc')->orderBy('order', 'asc')->paginate(10);
        return view('panel/links', $data);
    }

    //Delete link
    public function deleteLinkUser(request $request)
    {
        $linkId = $request->id;

        Link::where('id', $linkId)->delete();

        return back();
    }

    //Save user edit
    public function editUser(request $request)
    {
        try {
            $request->validate([
                'name' => '',
                'email' => '',
                'password' => '',
            ]);

            $id = $request->id;
            $rfid_no = $request->rfid_no ?? 0;
            $name = $request->name;
            $email = $request->email;
            $password = Hash::make($request->password);
            $profilePhoto = $request->file('image');
            $littlelink_description = '<p>' . $request->littlelink_description . '</p>';

            $role = $request->role;
            $customBackground = $request->file('background');
            $theme = $request->theme;

            ProfessionalInformation::where('user_id', $id)->update([

                'title' => '',

                'company' => '',

                'location' => '',

                'country' => '',

                'email' => $email,

                'mobile' => '',

                'role' => '',

            ]);

            if (User::where('id', $id)->get('role')->first()->role = ! $role) {
                if ($role == 'vip') {
                    UserData::saveData($id, 'checkmark', true);
                }
            }

            if ($request->password == '') {
                User::where('id', $id)->update([
                    'name' => $name,
                    'rfid_no' => $rfid_no,
                    'email' => $email,
                    'littlelink_description' => $littlelink_description,
                    'role' => $role,
                    'theme' => $theme,
                ]);
            } else {
                User::where('id', $id)->update([
                    'name' => $name,
                    'rfid_no' => $rfid_no,
                    'email' => $email,
                    'password' => $password,
                    'littlelink_description' => $littlelink_description,
                    'role' => $role,
                    'theme' => $theme,
                ]);
            }

            if (!empty($profilePhoto)) {
                $profilePhoto->move(base_path('assets/img'), $id . '_' . time() . ".png");
            }
            if (!empty($customBackground)) {
                $directory = base_path('assets/img/background-img/');
                $files = scandir($directory);
                $pathinfo = "error.error";
                foreach ($files as $file) {
                    if (strpos($file, $id . '.') !== false) {
                        $pathinfo = $id . "." . pathinfo($file, PATHINFO_EXTENSION);
                    }
                }
                if (file_exists(base_path('assets/img/background-img/') . $pathinfo)) {
                    File::delete(base_path('assets/img/background-img/') . $pathinfo);
                }

                $customBackground->move(base_path('assets/img/background-img/'), $id . '_' . time() . "." . $request->file('background')->extension());
            }

            return redirect()->back()->with('success', 'User updated successfully!');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Something went wrong. Email is already used or data not properly inputted');
        }
    }

    //Show site pages to edit
    public function showSitePage()
    {
        $data['pages'] = Page::select('terms', 'privacy', 'contact', 'register')->get();
        return view('panel/pages', $data);
    }

    //Save site pages
    public function editSitePage(request $request)
    {
        $terms = $request->terms;
        $privacy = $request->privacy;
        $contact = $request->contact;
        $register = $request->register;

        Page::first()->update(['terms' => $terms, 'privacy' => $privacy, 'contact' => $contact, 'register' => $register]);

        return back();
    }

    //Show home message for edit
    public function showSite()
    {
        $message = Page::select('home_message')->first();
        return view('panel/site', $message);
    }

    //Save home message, logo and favicon
    public function editSite(request $request)
    {
        $message = $request->message;
        $logo = $request->file('image');
        $icon = $request->file('icon');

        Page::first()->update(['home_message' => $message]);

        if (!empty($logo)) {
            // Delete existing image
            $path = findFile('avatar');
            $path = base_path('/assets/linkstack/images/' . $path);

            // Delete existing image
            if (File::exists($path)) {
                File::delete($path);
            }

            $logo->move(base_path('/assets/linkstack/images/'), "avatar" . '_' . time() . "." . $request->file('image')->extension());
        }

        if (!empty($icon)) {
            // Delete existing image
            $path = findFile('favicon');
            $path = base_path('/assets/linkstack/images/' . $path);

            // Delete existing image
            if (File::exists($path)) {
                File::delete($path);
            }

            $icon->move(base_path('/assets/linkstack/images/'), "favicon" . '_' . time() . "." . $request->file('icon')->extension());
        }
        return back();
    }

    //Delete avatar
    public function delAvatar()
    {
        $path = findFile('avatar');
        $path = base_path('/assets/linkstack/images/' . $path);

        // Delete existing image
        if (File::exists($path)) {
            File::delete($path);
        }

        return back();
    }

    //Delete favicon
    public function delFavicon()
    {
        // Delete existing image
        $path = findFile('favicon');
        $path = base_path('/assets/linkstack/images/' . $path);

        // Delete existing image
        if (File::exists($path)) {
            File::delete($path);
        }

        return back();
    }

    //View footer page: terms
    public function pagesTerms(Request $request)
    {
        $name = "terms";

        try {
            $data['page'] = Page::select($name)->first();
        } catch (Exception $e) {
            return abort(404);
        }

        return view('pages', ['data' => $data, 'name' => $name]);
    }

    //View footer page: privacy
    public function pagesPrivacy(Request $request)
    {
        $name = "privacy";

        try {
            $data['page'] = Page::select($name)->first();
        } catch (Exception $e) {
            return abort(404);
        }

        return view('pages', ['data' => $data, 'name' => $name]);
    }

    //View footer page: contact
    public function pagesContact(Request $request)
    {
        $name = "contact";

        try {
            $data['page'] = Page::select($name)->first();
        } catch (Exception $e) {
            return abort(404);
        }

        return view('pages', ['data' => $data, 'name' => $name]);
    }

    //Statistics of the number of clicks and links
    public function phpinfo()
    {
        return view('panel/phpinfo');
    }

    //Shows config file editor page
    public function showFileEditor(request $request)
    {
        return redirect('/panel/config');
    }

    //Saves advanced config
    public function editAC(request $request)
    {
        if ($request->ResetAdvancedConfig == 'RESET_DEFAULTS') {
            copy(base_path('storage/templates/advanced-config.php'), base_path('config/advanced-config.php'));
        } else {
            file_put_contents('config/advanced-config.php', $request->AdvancedConfig);
        }

        return redirect('/admin/config#2');
    }

    //Saves .env config
    public function editENV(request $request)
    {
        $config = $request->altConfig;

        file_put_contents('.env', $config);

        return Redirect('/admin/config?alternative-config');
    }

    //Shows config file editor page
    public function showBackups(request $request)
    {
        return view('/panel/backups');
    }

    //Delete custom theme
    public function deleteTheme(request $request)
    {

        $del = $request->deltheme;

        if (empty($del)) {
            echo '<script type="text/javascript">';
            echo 'alert("No themes to delete!");';
            echo 'window.location.href = "../studio/theme";';
            echo '</script>';
        } else {

            $folderName = base_path() . '/themes/' . $del;



            function removeFolder($folderName)
            {
                if (File::exists($folderName)) {
                    File::deleteDirectory($folderName);
                    return true;
                }

                return false;
            }

            removeFolder($folderName);

            return Redirect('/admin/theme');
        }
    }

    // Update themes
    public function updateThemes()
    {


        if ($handle = opendir('themes')) {
            while (false !== ($entry = readdir($handle))) {

                if (file_exists(base_path('themes') . '/' . $entry . '/readme.md')) {
                    $text = file_get_contents(base_path('themes') . '/' . $entry . '/readme.md');
                    $pattern = '/Theme Version:.*/';
                    preg_match($pattern, $text, $matches, PREG_OFFSET_CAPTURE);
                    if (!count($matches)) continue;
                    $verNr = substr($matches[0][0], 15);
                }


                $themeVe = NULL;

                if ($entry != "." && $entry != "..") {
                    if (file_exists(base_path('themes') . '/' . $entry . '/readme.md')) {
                        if (!strpos(file_get_contents(base_path('themes') . '/' . $entry . '/readme.md'), 'Source code:')) {
                            $hasSource = false;
                        } else {
                            $hasSource = true;

                            $text = file_get_contents(base_path('themes') . '/' . $entry . '/readme.md');
                            $pattern = '/Source code:.*/';
                            preg_match($pattern, $text, $matches, PREG_OFFSET_CAPTURE);
                            $sourceURL = substr($matches[0][0], 13);

                            $replaced = str_replace("https://github.com/", "https://raw.githubusercontent.com/", trim($sourceURL));
                            $replaced = $replaced . "/main/readme.md";

                            if (strpos($sourceURL, 'github.com')) {

                                ini_set('user_agent', 'Mozilla/4.0 (compatible; MSIE 6.0)');
                                try {
                                    $textGit = file_get_contents($replaced);
                                    $patternGit = '/Theme Version:.*/';
                                    preg_match($patternGit, $textGit, $matches, PREG_OFFSET_CAPTURE);
                                    $sourceURLGit = substr($matches[0][0], 15);
                                    $Vgitt = 'v' . $sourceURLGit;
                                    $verNrv = 'v' . $verNr;
                                } catch (Exception $ex) {
                                    $themeVe = "error";
                                    $Vgitt = NULL;
                                    $verNrv = NULL;
                                }

                                if (trim($Vgitt) > trim($verNrv)) {


                                    $fileUrl = trim($sourceURL) . '/archive/refs/tags/' . trim($Vgitt) . '.zip';


                                    file_put_contents(base_path('themes/theme.zip'), fopen($fileUrl, 'r'));


                                    $zip = new ZipArchive;
                                    $zip->open(base_path() . '/themes/theme.zip');
                                    $zip->extractTo(base_path('themes'));
                                    $zip->close();
                                    unlink(base_path() . '/themes/theme.zip');

                                    $folder = base_path('themes');
                                    $regex = '/[0-9.-]/';
                                    $files = scandir($folder);

                                    foreach ($files as $file) {
                                        if ($file !== '.' && $file !== '..') {
                                            if (preg_match($regex, $file)) {
                                                $new_file = preg_replace($regex, '', $file);
                                                File::copyDirectory($folder . '/' . $file, $folder . '/' . $new_file);
                                                $dirname = $folder . '/' . $file;
                                                if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                                                    system('rmdir ' . escapeshellarg($dirname) . ' /s /q');
                                                } else {
                                                    system("rm -rf " . escapeshellarg($dirname));
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return Redirect('/studio/theme');
    }

    //Shows config file editor page
    public function showConfig(request $request)
    {
        return view('/panel/config-editor');
    }

    //Shows config file editor page
    public function editConfig(request $request)
    {

        $type = $request->type;
        $entry = $request->entry;
        $value = $request->value;

        if ($type === "toggle") {
            if ($request->toggle != '') {
                $value = "true";
            } else {
                $value = "false";
            }
            if (EnvEditor::keyExists($entry)) {
                EnvEditor::editKey($entry, $value);
            }
        } elseif ($type === "toggle2") {
            if ($request->toggle != '') {
                $value = "verified";
            } else {
                $value = "auth";
            }
            if (EnvEditor::keyExists($entry)) {
                EnvEditor::editKey($entry, $value);
            }
        } elseif ($type === "text") {
            if (EnvEditor::keyExists($entry)) {
                EnvEditor::editKey($entry, '"' . $value . '"');
            }
        } elseif ($type === "debug") {
            if ($request->toggle != '') {
                if (EnvEditor::keyExists('APP_DEBUG')) {
                    EnvEditor::editKey('APP_DEBUG', 'true');
                }
                if (EnvEditor::keyExists('APP_ENV')) {
                    EnvEditor::editKey('APP_ENV', 'local');
                }
                if (EnvEditor::keyExists('LOG_LEVEL')) {
                    EnvEditor::editKey('LOG_LEVEL', 'debug');
                }
            } else {
                if (EnvEditor::keyExists('APP_DEBUG')) {
                    EnvEditor::editKey('APP_DEBUG', 'false');
                }
                if (EnvEditor::keyExists('APP_ENV')) {
                    EnvEditor::editKey('APP_ENV', 'production');
                }
                if (EnvEditor::keyExists('LOG_LEVEL')) {
                    EnvEditor::editKey('LOG_LEVEL', 'error');
                }
            }
        } elseif ($type === "register") {
            if ($request->toggle != '') {
                $register = "true";
            } else {
                $register = "false";
            }
            Page::first()->update(['register' => $register]);
        } elseif ($type === "smtp") {
            if ($request->toggle != '') {
                $value = "built-in";
            } else {
                $value = "smtp";
            }
            if (EnvEditor::keyExists('MAIL_MAILER')) {
                EnvEditor::editKey('MAIL_MAILER', $value);
            }

            if (EnvEditor::keyExists('MAIL_HOST')) {
                EnvEditor::editKey('MAIL_HOST', $request->MAIL_HOST);
            }
            if (EnvEditor::keyExists('MAIL_PORT')) {
                EnvEditor::editKey('MAIL_PORT', $request->MAIL_PORT);
            }
            if (EnvEditor::keyExists('MAIL_USERNAME')) {
                EnvEditor::editKey('MAIL_USERNAME', '"' . $request->MAIL_USERNAME . '"');
            }
            if (EnvEditor::keyExists('MAIL_PASSWORD')) {
                EnvEditor::editKey('MAIL_PASSWORD', '"' . $request->MAIL_PASSWORD . '"');
            }
            if (EnvEditor::keyExists('MAIL_ENCRYPTION')) {
                EnvEditor::editKey('MAIL_ENCRYPTION', $request->MAIL_ENCRYPTION);
            }
            if (EnvEditor::keyExists('MAIL_FROM_ADDRESS')) {
                EnvEditor::editKey('MAIL_FROM_ADDRESS', $request->MAIL_FROM_ADDRESS);
            }
        } elseif ($type === "homeurl") {
            if ($request->value == 'default') {
                $value = "";
            } else {
                $value = '"' . $request->value . '"';
            }
            if (EnvEditor::keyExists($entry)) {
                EnvEditor::editKey($entry, $value);
            }
        } elseif ($type === "maintenance") {
            if ($request->toggle != '') {
                $value = "true";
            } else {
                $value = "false";
            }
            if (file_exists(base_path("storage/MAINTENANCE"))) {
                unlink(base_path("storage/MAINTENANCE"));
            }
            if (EnvEditor::keyExists($entry)) {
                EnvEditor::editKey($entry, $value);
            }
        } else {
            if (EnvEditor::keyExists($entry)) {
                EnvEditor::editKey($entry, $value);
            }
        }




        return Redirect('/admin/config');
    }

    //Shows theme editor page
    public function showThemes(request $request)
    {
        return view('/panel/theme');
    }

    //Removes impersonation if authenticated
    public function authAs(request $request)
    {

        $userID = $request->id;
        $token = $request->token;

        $user = User::find($userID);

        if ($user->remember_token == $token && $request->session()->get('display_auth_nav') === $user->remember_token) {
            $user->auth_as = null;
            $user->remember_token = null;
            $user->save();

            $request->session()->forget('display_auth_nav');

            Auth::loginUsingId($userID);

            return redirect('/admin/users/all');
        } else {
            Auth::logout();
        }
    }

    //Add impersonation
    public function authAsID(request $request)
    {

        $adminUser = User::whereNotNull('auth_as')->where('role', 'admin')->first();

        if (!$adminUser) {

            $userID = $request->id;
            $id = Auth::user()->id;

            $user = User::find($id);

            $user->auth_as = $userID;
            $user->save();

            return redirect('dashboard');
        } else {
            return redirect('admin/users/all');
        }
    }

    //Show info about link
    public function redirectInfo(request $request)
    {
        $linkId = $request->id;

        if (empty($linkId)) {
            return abort(404);
        }

        $linkData = Link::find($linkId);
        $clicks = $linkData->click_number;

        if (empty($linkData)) {
            return abort(404);
        }

        function isValidLink($url)
        {
            $validPrefixes = array('http', 'https', 'ftp', 'mailto', 'tel', 'news');

            $pattern = '/^(' . implode('|', $validPrefixes) . '):/i';

            if (preg_match($pattern, $url) && strlen($url) <= 155) {
                return $url;
            } else {
                return "N/A";
            }
        }

        $link = isValidLink($linkData->link);

        $userID = $linkData->user_id;
        $userData = User::find($userID);

        return view('linkinfo', ['clicks' => $clicks, 'linkID' => $linkId, 'link' => $link, 'id' => $userID, 'userData' => $userData]);
    }



public function import_users(Request $request)
{
    ini_set('max_execution_time', '2400');
    
    $users = $request->input('users');
    $importType = $request->input('import_type', 'individual'); // 'individual' or 'company'
    
    $duplicateEmailCount = 0;
    $duplicateEmails = [];

    foreach ($users as $userData) {
        try {
            $existingCodes = User::pluck('activate_code')->toArray();
            $keyspace = 'abcdefghijklmnopqrstuvwxyz1234567890';
            
            // Use different prefix based on import type - configurable via .env
            $prefix = $importType === 'company' 
                ? env('COMPANY_CODE_PREFIX', 'pt') 
                : env('INDIVIDUAL_CODE_PREFIX', 'nmyl');
            $randomCode = $this->generateUniqueRandomCodeImport(7, $keyspace, $existingCodes, $prefix);
            
            // Create user based on import type
            if ($importType === 'company') {
                $user = $this->createCompanyUser($userData, $randomCode);
            } else {
                $user = $this->createIndividualUser($userData, $randomCode);
            }

            $userId = $user->id;

            // Add Links
            $this->addUserLinks($userId, $userData);

            // Create Professional Information based on type
            if ($importType === 'company') {
                $this->createCompanyProfessionalInfo($userId, $userData);
            } else {
                $this->createIndividualProfessionalInfo($userId, $userData);
            }

            UserData::saveData($userId, 'show-professional', true);
            UserData::saveData($userId, 'show-send-details', true);
            
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === '23000') {
            $duplicateEmailCount++;

            $email = $userData['EMAILADDRESS']
                ?? $userData['primary_email_address']
                ?? null;

            $duplicateEmails[] = $email;

            Log::warning('Duplicate email detected: ' . ($email ?? 'N/A'));

            } else {
                Log::error('An error occurred while creating a user: ' . $e->getMessage());
            }
        }
    }

    Log::info('Duplicate email count: ' . $duplicateEmailCount);
    Log::info('Duplicate emails: ' . implode(', ', $duplicateEmails));

    return response()->json(['message' => 'Users imported successfully!']);
}

// Helper method for generating unique code with custom prefix
private function generateUniqueRandomCodeImport($length, $keyspace, $existingCodes, $prefix = null)
{
    // If no prefix provided, use default from .env
    if ($prefix === null) {
        $prefix = env('INDIVIDUAL_CODE_PREFIX', 'nmyl');
    }
    
    $maxAttempts = 100000;
    $attempts = 0;

    do {
        $randomCode = $prefix . $this->random_str($length - strlen($prefix), $keyspace);
        $attempts++;
    } while (in_array($randomCode, $existingCodes) && $attempts < $maxAttempts);

    if ($attempts == $maxAttempts) {
        throw new \RuntimeException("Failed to generate a unique random code.");
    }

    return $randomCode;
}

// Create company user
private function createCompanyUser($userData, $randomCode)
{
    return User::create([
        'name' => $userData['company_name'],
        'email' => $userData['primary_email_address'],
        'password' => Hash::make('12345678'),
        'littlelink_name' => $randomCode,
        'littlelink_description' => $userData['office_address_main'] ?? null,
        'mobile_number' => $userData['mobile_number'] ?? $userData['telephone_number'] ?? null,
        'role' => 'user',
        'block' => 'no',
        'activate_code' => $randomCode,
        'activate_status' => 'activated',
        'website' => $userData['website_url'] ?? null,
    ]);
}

// Create individual user
private function createIndividualUser($userData, $randomCode)
{
    // Normalize column names (trim spaces and handle variations)
    $normalizedData = [];
    foreach ($userData as $key => $value) {
        $normalizedKey = trim($key);
        $normalizedData[$normalizedKey] = $value;
    }
    
    // Combine FIRST NAME and M.I. + LAST NAME
    $fullName = trim(($normalizedData['FIRST NAME'] ?? '') . ' ' . ($normalizedData['M.I. + LAST NAME'] ?? ''));
    
    // Get email - check multiple possible column names
    $email = $normalizedData['EMAIL ADDRESS'] ?? $normalizedData['Email Address'] ?? $normalizedData['email address'] ?? null;
    
    // If email is empty, N/A, or invalid, generate a placeholder
    if (empty($email) || in_array(strtolower(trim($email)), ['n/a', 'null', 'none'])) {
        // Generate unique email using activation code
        $domain = parse_url(url(''), PHP_URL_HOST);
        $domain = ($domain == 'localhost') ? 'example.com' : $domain;
        $email = strtolower($randomCode) . '@' . $domain;
    }
    
    return User::create([
        'name' => $fullName,
        'email' => $email,
        'password' => Hash::make('12345678'),
        'littlelink_name' => $randomCode,
        'littlelink_description' => $normalizedData['LOCAL POSITION'] ?? null,
        'mobile_number' => $normalizedData['CONTACT NO.'] ?? null,
        'role' => 'user',
        'block' => 'no',
        'activate_code' => $randomCode,
        'activate_status' => 'activated',
        'website' => $normalizedData['website_url'] ?? null,
    ]);
}

// Add user links
private function addUserLinks($userId, $userData)
{
    // Normalize column names (trim spaces and handle variations)
    $normalizedData = [];
    foreach ($userData as $key => $value) {
        $normalizedKey = trim($key);
        $normalizedData[$normalizedKey] = $value;
    }
    
    // Website link
    if (isset($normalizedData['website_url']) && !in_array(strtolower(trim($normalizedData['website_url'])), ['n/a', 'null'])) {
        $websiteUrl = trim($normalizedData['website_url']);
        if (!preg_match('/^https?:\/\//i', $websiteUrl)) {
            $websiteUrl = 'https://' . $websiteUrl;
        }

        $links = new Link;
        $links->user_id = $userId;
        $links->title = 'Website';
        $links->button_id = "2";
        $links->link = $websiteUrl;
        $links->save();
        $links->order = ($links->id - 1);
        $links->save();
    }

    // Mobile number link - using new column name
    $contactNo = $normalizedData['CONTACT NO.'] ?? null;
    if (!empty($contactNo)) {
        $numbers = preg_split('/[\/\,\s]+/', $contactNo);

        foreach ($numbers as $number) {
            $normalizedNumber = preg_replace('/[^\d+]/', '', $number);

            if (preg_match('/^\d{10}$/', $normalizedNumber)) {
                $normalizedNumber = '+63' . $normalizedNumber;
            } elseif (preg_match('/^(?:\+|0|63)/', $normalizedNumber)) {
                if (strpos($normalizedNumber, '+') !== 0 && strpos($normalizedNumber, '63') === 0) {
                    $normalizedNumber = '+' . $normalizedNumber;
                } elseif (strpos($normalizedNumber, '0') === 0) {
                    $normalizedNumber = '+63' . substr($normalizedNumber, 1);
                }
            }

            if (!empty($normalizedNumber)) {
                $links = new Link;
                $links->user_id = $userId;
                $links->title = $contactNo;
                $links->button_id = "44";
                $links->link = 'tel:' . $normalizedNumber;
                $links->save();
                $links->order = ($links->id - 1);
                $links->save();
                break;
            }
        }
    }

    // Email link - using new column name
    $email = $normalizedData['EMAIL ADDRESS'] ?? $normalizedData['Email Address'] ?? $normalizedData['email address'] ?? null;
    if (!empty($email) && !in_array(strtolower(trim($email)), ['n/a', 'null', 'none'])) {
        $links = new Link;
        $links->user_id = $userId;
        $links->title = $email;
        $links->button_id = "6";
        $links->link = $email;
        $links->save();
        $links->order = ($links->id - 1);
        $links->save();
    }
}

// Create company professional information
private function createCompanyProfessionalInfo($userId, $userData)
{
    ProfessionalInformation::create([
        'user_id' => $userId,
        'title' => null,
        'company' => $userData['company_name'],
        'location' => $userData['office_address_main'] ?? null,
        'country' => 'Philippines',
        'email' => $userData['primary_email_address'],
        'mobile' => $userData['mobile_number'] ?? $userData['telephone_number'] ?? null,
        'role' => null,
    ]);
}

// Create individual professional information
private function createIndividualProfessionalInfo($userId, $userData)
{
    // Normalize column names (trim spaces and handle variations)
    $normalizedData = [];
    foreach ($userData as $key => $value) {
        $normalizedKey = trim($key);
        $normalizedData[$normalizedKey] = $value;
    }
    
    // Combine LGU, Province and REGION
    $location = trim(($normalizedData['LGU, Province'] ?? '') . ', ' . ($normalizedData['REGION'] ?? ''));
    
    // Get email - check multiple possible column names
    $email = $normalizedData['EMAIL ADDRESS'] ?? $normalizedData['Email Address'] ?? $normalizedData['email address'] ?? null;
    
    ProfessionalInformation::create([
        'user_id' => $userId,
        'title' => $normalizedData['LOCAL POSITION'] ?? null,
        'company' => $normalizedData['company_name'] ?? null,
        'location' => $location,
        'country' => 'Philippines',
        'email' => $email,
        'mobile' => $normalizedData['CONTACT NO.'] ?? null,
        'role' => $normalizedData['Type of Membership'] ?? null,
    ]);
}

    public function generateQrCode(Request $request)
{
    // Retrieve the first 50 users with qr_code_status = 0
    ini_set('max_execution_time', '2400');
    //ini_set('memory_limit', '3072M');

    $users = User::where('qr_code_status', '0')->take(299)->get();

    // Check if users exist
    if ($users->isEmpty()) {
        return response()->json(['message' => 'No users found with deactivated status.'], 404);
    }

    // Create a new Spreadsheet
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Set column headings
    $sheet->setCellValue('A1', 'Code');
    $sheet->setCellValue('B1', 'Url');
    $sheet->setCellValue('C1', 'Name');
    $sheet->setCellValue('D1', 'Email');
    $sheet->setCellValue('E1', 'Title');
    $sheet->setCellValue('F1', 'Location'); // New column
    $sheet->setCellValue('G1', 'Mobile Number');
    $sheet->setCellValue('H1', 'ID');
    $sheet->setCellValue('I1', 'Website');
    $sheet->setCellValue('J1', 'QR code'); // Moved to column J

    $sheet->getColumnDimension('B')->setWidth(30);
    $sheet->getColumnDimension('C')->setWidth(15);
    $sheet->getColumnDimension('F')->setWidth(25); // Set width for Location column

    $row = 2; // Start from the second row

    foreach ($users as $user) {
        $redirectURL = url('/u/' . $user->id);
        $publicURL = url('/' . $user->activate_code);

        // Get professional information for the user
        $professionalInfo = ProfessionalInformation::where('user_id', $user->id)->first();
        $location = $professionalInfo ? $professionalInfo->location : '';

        $argValues = [0, 0, 0, 0, 0, 0, 'diagonal'];
        list($arg1, $arg2, $arg3, $arg4, $arg5, $arg6, $arg7) = $argValues;

        try {
            // Generate QR code
            if (extension_loaded('imagick')) {
                $imgSrc = QrCode::format('png')
                    ->color(41, 55, 123) // Dark blue background
                    ->backgroundColor(255, 255, 255)         // White foreground
                    ->margin(1)
                    ->eye('circle')
                    ->style('round')
                    ->size(300)
                    ->generate($redirectURL);
                $imgSrc = base64_encode($imgSrc);
                $imgSrc = 'data:image/png;base64,' . $imgSrc;
            } else {
                $imgSrc = QrCode::gradient($arg1, $arg2, $arg3, $arg4, $arg5, $arg6, $arg7)
                    ->eye('circle')
                    ->style('round')
                    ->size(300)
                    ->generate($redirectURL);
                $imgSrc = base64_encode($imgSrc);
                $imgSrc = 'data:image/svg+xml;base64,' . $imgSrc;
            }
            $sheet->getRowDimension($row)->setRowHeight(76);
            
            // Add data to the spreadsheet
            $sheet->setCellValue('A' . $row, $user->activate_code);
            $sheet->setCellValue('B' . $row, $publicURL);
            $sheet->setCellValue('C' . $row, $user->name);
            $sheet->setCellValue('D' . $row, $user->email);
            $sheet->setCellValue('E' . $row, $user->littlelink_description);
            $sheet->setCellValue('F' . $row, $location); // Add location
            $sheet->setCellValue('G' . $row, $user->mobile_number);
            $sheet->setCellValue('H' . $row, $user->id);
            $sheet->setCellValue('I' . $row, $user->website);

            // Decode the base64 string and create a QR code image resource
            $qrImage = imagecreatefromstring(base64_decode(explode(',', $imgSrc)[1]));

            // Create a MemoryDrawing object to insert the image into the spreadsheet
            $drawing = new MemoryDrawing();
            $drawing->setImageResource($qrImage);
            $drawing->setCoordinates('J' . $row); // Changed to column J
            $drawing->setHeight(100);
            $drawing->setWorksheet($sheet);

            $row++;
        } catch (Exception $e) {
            // Log the error if needed
            continue;
        }
    }

    // Save the spreadsheet to a file
    $writer = new Xlsx($spreadsheet);
    $fileName = 'QR_Codes___' . now()->format('Y-m-d_H-i-s') . '.xlsx';
    $filePath = storage_path("app/{$fileName}");

    $writer->save($filePath);
    User::whereIn('id', $users->pluck('id'))->update(['qr_code_status' => 1]);
    
    // Return the file as a download response
    return response()->download($filePath)->deleteFileAfterSend(false);
}

    //OLD generate QR code for Philtoa
    //     public function generateQrCode(Request $request)
    // {
    //     // Retrieve the first 50 users with qr_code_status = 0
    //     ini_set('max_execution_time', '2400');
    //     //ini_set('memory_limit', '3072M');

    //     $users = User::where('qr_code_status', '0')->take(299)->get();


    //     // Check if users exist
    //     if ($users->isEmpty()) {
    //         return response()->json(['message' => 'No users found with deactivated status.'], 404);
    //     }

    //     // Create a new Spreadsheet
    //     $spreadsheet = new Spreadsheet();
    //     $sheet = $spreadsheet->getActiveSheet();

    //     // Set column headings
    //     $sheet->setCellValue('A1', 'Code');
    //     $sheet->setCellValue('B1', 'Url');
    //     $sheet->setCellValue('C1', 'Name');
    //     $sheet->setCellValue('D1', 'Email');
    //     $sheet->setCellValue('E1', 'Address');
    //     $sheet->setCellValue('F1', 'Mobile Number');
    //     $sheet->setCellValue('G1', 'ID');
    //     $sheet->setCellValue('H1', 'Website');
    //     $sheet->setCellValue('I1', 'QR code');

    //     $sheet->getColumnDimension('B')->setWidth(30);
    //     $sheet->getColumnDimension('C')->setWidth(15);

    //     $row = 2; // Start from the second row

    //     foreach ($users as $user) {
    //         $redirectURL = url('/u/' . $user->id);
    //         $publicURL = url('/' . $user->activate_code);

    //         $argValues = [0, 0, 0, 0, 0, 0, 'diagonal'];
    //         list($arg1, $arg2, $arg3, $arg4, $arg5, $arg6, $arg7) = $argValues;

    //         try {
    //             // Generate QR code
    //             if (extension_loaded('imagick')) {
    //                 $imgSrc = QrCode::format('png')
    //                     ->color(41, 55, 123) // Dark blue background
    //                     ->backgroundColor(255, 255, 255)         // White foreground
    //                     ->margin(1)
    //                     ->eye('circle')
    //                     ->style('round')
    //                     ->size(300)
    //                     ->generate($redirectURL);
    //                 $imgSrc = base64_encode($imgSrc);
    //                 $imgSrc = 'data:image/png;base64,' . $imgSrc;
    //             } else {
    //                 $imgSrc = QrCode::gradient($arg1, $arg2, $arg3, $arg4, $arg5, $arg6, $arg7)
    //                     ->eye('circle')
    //                     ->style('round')
    //                     ->size(300)
    //                     ->generate($redirectURL);
    //                 $imgSrc = base64_encode($imgSrc);
    //                 $imgSrc = 'data:image/svg+xml;base64,' . $imgSrc;
    //             }
    //             $sheet->getRowDimension($row)->setRowHeight(76);
    //             // Add data to the spreadsheet
    //             $sheet->setCellValue('A' . $row, $user->activate_code);
    //             $sheet->setCellValue('B' . $row, $publicURL);
    //             $sheet->setCellValue('C' . $row, $user->name);
    //             $sheet->setCellValue('D' . $row, $user->email);
    //             $sheet->setCellValue('E' . $row, $user->littlelink_description);
    //             $sheet->setCellValue('F' . $row, $user->mobile_number);
    //             $sheet->setCellValue('G' . $row, $user->id);
    //             $sheet->setCellValue('H' . $row, $user->website);

    //             // Decode the base64 string and create a QR code image resource
    //             $qrImage = imagecreatefromstring(base64_decode(explode(',', $imgSrc)[1]));

    //             // Create a MemoryDrawing object to insert the image into the spreadsheet
    //             $drawing = new MemoryDrawing();
    //             $drawing->setImageResource($qrImage);
    //             $drawing->setCoordinates('I' . $row);
    //             $drawing->setHeight(100);
    //             $drawing->setWorksheet($sheet);

    //             $row++;
    //         } catch (Exception $e) {
    //             // Log the error if needed
    //             continue;
    //         }
    //     }

    //     // Save the spreadsheet to a file
    //     $writer = new Xlsx($spreadsheet);
    //     $fileName = 'QR_Codes___' . now()->format('Y-m-d_H-i-s') . '.xlsx';
    //     $filePath = storage_path("app/{$fileName}");


    //     $writer->save($filePath);
    //     User::whereIn('id', $users->pluck('id'))->update(['qr_code_status' => 1]);
    //     // Return the file as a download response
    //     return response()->download($filePath)->deleteFileAfterSend(false);
    // }

    // Add this method to your AdminController class
public function exportUserCredentials(Request $request)
{
    ini_set('max_execution_time', '2400');

    // Get all users
    $users = User::where('id', '!=', 1)->get();

    // Check if users exist
    if ($users->isEmpty()) {
        return response()->json(['message' => 'No users found.'], 404);
    }

    // Create a new Spreadsheet
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Set column headings
    $sheet->setCellValue('A1', 'Name');
    $sheet->setCellValue('B1', 'Email');
    $sheet->setCellValue('C1', 'Password');
    $sheet->setCellValue('D1', 'URL');

    // Style the header row
    $headerStyle = [
        'font' => [
            'bold' => true,
            'size' => 12,
        ],
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'startColor' => ['rgb' => 'E2E8F0'],
        ],
    ];
    $sheet->getStyle('A1:D1')->applyFromArray($headerStyle);

    // Set column widths
    $sheet->getColumnDimension('A')->setWidth(25);
    $sheet->getColumnDimension('B')->setWidth(30);
    $sheet->getColumnDimension('C')->setWidth(15);
    $sheet->getColumnDimension('D')->setWidth(40);

    $row = 2; // Start from the second row

    foreach ($users as $user) {
        // Generate the public URL
        $publicURL = url('/' . $user->activate_code);

        // Add data to the spreadsheet
        $sheet->setCellValue('A' . $row, $user->name);
        $sheet->setCellValue('B' . $row, $user->email);
        $sheet->setCellValue('C' . $row, '12345678'); // Default password
        $sheet->setCellValue('D' . $row, $publicURL);

        $row++;
    }

    // Save the spreadsheet to a file
    $writer = new Xlsx($spreadsheet);
    $fileName = 'User_Credentials_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
    $filePath = storage_path("app/{$fileName}");

    $writer->save($filePath);

    // Return the file as a download response
    return response()->download($filePath)->deleteFileAfterSend(true);
}
}


