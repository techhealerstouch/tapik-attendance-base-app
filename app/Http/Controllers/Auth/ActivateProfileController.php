<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Hash;
use App\Models\Link;
use App\Models\LinkType;
use Illuminate\Support\Facades\Route;
use DB;
use App\Models\Button;
use App\Models\UserData;
use App\Models\ProfessionalInformation;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use micro\FormFactory;

class ActivateProfileController extends Controller
{
    public function activate(){
        return view('panel.activate-profile');
    }

    public function getStarted(){
        return view('panel.get-started');
    }

   
    public function store(Request $request) {
        $profilePhoto = $request->file('image');
        $code = $request->code;
        $name = $request->name;
        $email = $request->email;
        $password = Hash::make($request->password);
        $userId = User::where('activate_code', $code)
        ->value('id');

        Log::info($request->all());
        
        if ($request->hasFile('image')) {
            while (findAvatar($userId) !== "error.error") {
                $avatarName = findAvatar($userId);
                unlink(base_path($avatarName));
            }
            $fileName = $userId . '_' . time() . "." . $profilePhoto->extension();
            $profilePhoto->move(base_path('assets/img'), $fileName);
        }
        
        $user = User::where('activate_code', $code)->first();
        if (!$user) {
            return response()->json(['error' => 'Invalid code'], 400);
        } else {
            
            $user->update([
                'name' => $name,
                'email' => $email,
                'password' => $password,
                'activate_status' => 'activated',
            ]);
    
            return response()->json(['code' => $code], 200);
        }
    }

    public function create_profile(Request $request, $code) {
        Log::info('Form Data: ' . json_encode($request->all()));
        
        $linkType = LinkType::find($request->linktype_id);
        $LinkTitle = ($request->link_text ?? $request->link_title) ?? $request->title;
        $LinkURL = $request->link_url ?? $request->link;
        $OrigLink = Link::find($request->linkid);
        $customParams = [];

        foreach ($request->all() as $key => $param) {
            if (str_starts_with($key, "_") ||  in_array($key, ['linktype_id', 'linktype_title', 'link_text', 'link_url'])) {
                continue;
            }
            $customParams[$key] = $param;
        }

        
        $userId = User::where('activate_code', $code)
        ->value('id');

        //update theme
        $theme = $request->input('selected_theme');

        // If $theme is null or empty, set it to 'default'
        if (empty($theme)) {
            $theme = 'default';
        }

        User::where('id', $userId)->update(['theme' => $theme]);


        $button = Button::where('name', $request->button)->first();

        if ($button && empty($LinkTitle)) {
            $LinkTitle = $button->alt;
        }


        $links = new Link;
        $links->link = $LinkURL;
        $links->user_id = $userId;

        
        //Create Social icons

        /*$inputKeys = [
            'mastodon' => $request->input('mastodon'),
            'instagram' => $request->input('instagram'),
            'twitter' => $request->input('twitter'),
            'facebook' => $request->input('facebook'),
            'github' => $request->input('github'),
            'twitch' => $request->input('twitch'),
            'linkedin' => $request->input('linkedin'),
            'tiktok' => $request->input('tiktok'),
            'discord' => $request->input('discord'),
            'youtube' => $request->input('youtube'),
            'snapchat' => $request->input('snapchat'),
            'reddit' => $request->input('reddit'),
            'pinterest' => $request->input('pinterest')
        ];*/
        $inputKeys = [
            'mastodon',
            'instagram',
            'twitter',
            'facebook',
            'github',
            'twitch',
            'linkedin',
            'tiktok',
            'discord',
            'youtube',
            'snapchat',
            'reddit',
            'pinterest'
        ];
        
        $validationRules = [];

        foreach ($inputKeys as $platform) {
            $validationRules[$platform] = 'nullable|exturl|max:255';
        }

        $request->validate($validationRules);

        foreach ($inputKeys as $platform) {
            $link = $request->input($platform);

            if (!empty($link)) {
                $iconId = $this->searchIcon($platform, $userId);

                if (!is_null($iconId)) {
                    $this->updateIcon($platform, $link);
                } else {
                    $this->addIcon($platform, $link, $userId);
                }
            }
        }

         ProfessionalInformation::firstOrCreate(

            ['user_id' => $userId],  // Condition to check

            [ // Default values if a new record is created

                'title' => $request->input('title'),

                'company' => $request->input('company'),

                'location' => $request->input('location'),

                'country' => $request->input('country'),

                'email' => $request->input('email'),

                'mobile' => $request->input('mobile'),

                'role' => $request->input('role'),

            ]

        );
        UserData::saveData($userId, 'show-professional', true);
        UserData::saveData($userId, 'show-send-details', true);



        
            $prefix = $request->input('prefix');
            $firstName = $request->input('first_name');
            $middleName = $request->input('middle_name');
            $lastName = $request->input('last_name');
            $suffix = $request->input('suffix');
            $nickname = $request->input('nickname');
            $organization = $request->input('organization');
            $vtitle = $request->input('vtitle');
            $role = $request->input('role');
            $workUrl = $request->input('work_url');
            $email = $request->input('email');
            $workEmail = $request->input('work_email');
            $homePhone = $request->input('home_phone');
            $workPhone = $request->input('work_phone');
            $cellPhone = $request->input('cell_phone');
            $homeAddressLabel = $request->input('home_address_label');
            $homeAddressStreet = $request->input('home_address_street');
            $homeAddressCity = $request->input('home_address_city');
            $homeAddressState = $request->input('home_address_state');
            $homeAddressZip = $request->input('home_address_zip');
            $homeAddressCountry = $request->input('home_address_country');
            $workAddressLabel = $request->input('work_address_label');
            $workAddressStreet = $request->input('work_address_street');
            $workAddressCity = $request->input('work_address_city');
            $workAddressState = $request->input('work_address_state');
            $workAddressZip = $request->input('work_address_zip');
            $workAddressCountry = $request->input('work_address_country');
            // Create an array with all the input fields
            $data = [
                'prefix' => $request->input('prefix'),
                'first_name' => $request->input('first_name'),
                'middle_name' => $request->input('middle_name'),
                'last_name' => $request->input('last_name'),
                'suffix' => $request->input('suffix'),
                'nickname' => $request->input('nickname'),
                'organization' => $request->input('organization'),
                'vtitle' => $request->input('vtitle'),
                'role' => $request->input('role'),
                'work_url' => $request->input('work_url'),
                'email' => $request->input('email'),
                'work_email' => $request->input('work_email'),
                'home_phone' => $request->input('home_phone'),
                'work_phone' => $request->input('work_phone'),
                'cell_phone' => $request->input('cell_phone'),
                'home_address_label' => $request->input('home_address_label'),
                'home_address_street' => $request->input('home_address_street'),
                'home_address_city' => $request->input('home_address_city'),
                'home_address_state' => $request->input('home_address_state'),
                'home_address_zip' => $request->input('home_address_zip'),
                'home_address_country' => $request->input('home_address_country'),
                'work_address_label' => $request->input('work_address_label'),
                'work_address_street' => $request->input('work_address_street'),
                'work_address_city' => $request->input('work_address_city'),
                'work_address_state' => $request->input('work_address_state'),
                'work_address_zip' => $request->input('work_address_zip'),
                'work_address_country' => $request->input('work_address_country'),
            ];
            // Convert the array to JSON format
            $json = json_encode($data);
            // Set the JSON as the variable $links->link, or null if the JSON is empty
            $links->link = $json ? $json : null;       
            $links->button_id = 96;
            $links->title = $request->input('link_title');

        if (empty($links->button_id)) {
            throw new \Exception('Invalid link');
        }

        $links->save();

        $links->order = ($links->id - 1);
        $links->save();




    }

    

    private function searchIcon($icon, $userId)

    {

        return DB::table('links')

            ->where('user_id', $userId)

            ->where('title', $icon)

            ->where('button_id', 94)

            ->value('id');

    }



    private function addIcon($icon, $link, $usId)

    {

        $userId = $usId;

        $links = new Link;

        $links->link = $link;

        $links->user_id = $userId;

        $links->title = $icon;

        $links->button_id = '94';

        $links->save();

        $links->order = ($links->id - 1);

        $links->save();

    }



    private function updateIcon($icon, $link)

    {

        Link::where('id', $this->searchIcon($icon))->update([

            'button_id' => 94,

            'link' => $link,

            'title' => $icon

        ]);

    }

    public function deleteLink(request $request, $id)

    {

        $linkId = $request->id;



        Link::where('id', $linkId)->delete();



        $directory = base_path("assets/favicon/icons");

        $files = scandir($directory);

        foreach($files as $file) {

        if (strpos($file, $linkId.".") !== false) {

        $pathinfo = pathinfo($file, PATHINFO_EXTENSION);}}

        if (isset($pathinfo)) {

        try{File::delete(base_path("assets/favicon/icons")."/".$linkId.".".$pathinfo);} catch (exception $e) {}

        }



        return response()->json(['message' => 'Link deleted successfully'], 200);

    }

    public function setup($code, $id = 0)
    {
        $user = User::where('activate_code', $code)->first();
    
        // Check if the activate_code exists and the user is retrieved
        if (!$user) {
            return redirect()->back()->with('error', 'Invalid activation code.');
        }

        $userId = $user->id;

        $data['pagePage'] = 10;
        $data['links'] = Link::select('id', 'link', 'title', 'order', 'click_number', 'up_link', 'links.button_id')->where('user_id', $userId)->orderBy('up_link', 'asc')->orderBy('order', 'asc')->paginate(99999);
        
        if ($id !== 0) {

            $linkData = Link::find($id);

        } elseif ($id == 0) {

            $linkData = new Link(['typename' => 'link', 'id'=>'0']);

        } else {

            $linkData = new Link(['typename' => 'link', 'id'=>'0']);

        }
        
        $data['LinkTypes'] = LinkType::get();

        $data['LinkData'] = $linkData;

        $data['LinkID'] = $id;

        $data['linkTypeID'] = "1";

        $data['title'] = "Predefined Site";

        Log::info('ASDid' . $data['LinkID']);


        if (Route::currentRouteName() != 'setupProfile') {

            $links = DB::table('links')->where('id', $id)->first();


            $bid = $links->button_id;
            Log::info('SASDASDASDSADbid' . $bid);


            if($bid == 1 or $bid == 2){

                $data['linkTypeID'] = "2";

            } elseif ($bid == 42) {

                $data['linkTypeID'] = "3";

            } elseif ($bid == 43) {

                $data['linkTypeID'] = "4";

            } elseif ($bid == 93) {

                $data['linkTypeID'] = "5";

            } elseif ($bid == 6 or $bid == 7) {

                $data['linkTypeID'] = "6";

            } elseif ($bid == 44) {

                $data['linkTypeID'] = "7";

            } elseif ($bid == 96) {

                $data['linkTypeID'] = "8";

            } else {

                $data['linkTypeID'] = "1";

            }



            $data['title'] = LinkType::where('id', $data['linkTypeID'])->value('title');

        }



        foreach ($data['LinkTypes']->toArray() as $key => $val) {

            if ($val['typename'] === $linkData['typename']) {

                $data['SelectedLinkType'] = $val;

                break;

            }

        }

        Log::info($data['SelectedLinkType']);
        return view('setup.multi-step-form', $data);
    }


    public function saveLink(Request $request, $code)
    {
        Log::info('Request Data:', ['data' => $request->all()]);
   
        
        $linkType = LinkType::find($request->linktype_id);
        $LinkTitle = ($request->link_text ?? $request->link_title) ?? $request->title;
        $LinkURL = $request->link_url ?? $request->link;
        $OrigLink = Link::find($request->linkid);
        $customParams = [];

        foreach ($request->all() as $key => $param) {
            if (str_starts_with($key, "_") ||  in_array($key, ['linktype_id', 'linktype_title', 'link_text', 'link_url'])) {
                continue;
            }
            $customParams[$key] = $param;
        }


        $userId = User::where('activate_code', $code)
        ->value('id');

        $button = Button::where('name', $request->button)->first();

        if ($button && empty($LinkTitle)) {
            $LinkTitle = $button->alt;
        }

        if ($linkType->typename == 'video' && empty($LinkTitle)) {
            $embed = OEmbed::get($LinkURL);
            if ($embed) {
                $LinkTitle = $embed->data()['title'];
            }
        }

        $message = (ucwords($button?->name) ?? ucwords($linkType->typename)). " has been ";

        if ($OrigLink) {
            // EDITING EXISTING
            $isCustomWebsite = $customParams['GetSiteIcon'] ?? null;
            $SpacerHeight = $customParams['height'] ?? null;

            if ($linkType->typename == "link" && $isCustomWebsite == "1") {
                $OrigLink->update([
                    'link' => $LinkURL,
                    'title' => $LinkTitle,
                    'button_id' => "2",
                ]);
            } elseif ($linkType->typename == "link") {
                $OrigLink->update([
                    'link' => $LinkURL,
                    'title' => $LinkTitle,
                    'button_id' => "1",
                ]);
            } elseif ($linkType->typename == "spacer") {
                $OrigLink->update([
                    'link' => $LinkURL,
                    'title' => $customParams['height'] ?? null,
                    'button_id' => "43",
                ]);
            } elseif ($linkType->typename == "heading") {
                $OrigLink->update([
                    'link' => $LinkURL,
                    'title' => $LinkTitle,
                    'button_id' => "42",
                ]);
            } elseif ($linkType->typename == "text") {
                $sanitizedText = $request->text;
                $sanitizedText = strip_tags($sanitizedText, '<a><p><strong><i><ul><ol><li><blockquote><h2><h3><h4>');
                $sanitizedText = preg_replace("/<a([^>]*)>/i", "<a $1 rel=\"noopener noreferrer nofollow\">", $sanitizedText);
                $sanitizedText = strip_tags_except_allowed_protocols($sanitizedText);
                $OrigLink->update([
                    'button_id' => "93",
                    'title' => $sanitizedText,
                ]);
            } elseif ($linkType->typename == "email") {
                $OrigLink->update([
                    'link' => $LinkURL,
                    'button_id' => $button?->id,
                    'title' => $LinkTitle,
                ]);
            } elseif ($linkType->typename == "telephone") {
                $OrigLink->update([
                    'link' => $LinkURL,
                    'button_id' => $button?->id,
                    'title' => $LinkTitle,
                ]);
            } elseif ($linkType->typename == "vcard") {
                $prefix = $request->input('prefix');
                $firstName = $request->input('first_name');
                $middleName = $request->input('middle_name');
                $lastName = $request->input('last_name');
                $suffix = $request->input('suffix');
                $nickname = $request->input('nickname');
                $organization = $request->input('organization');
                $vtitle = $request->input('vtitle');
                $role = $request->input('role');
                $workUrl = $request->input('work_url');
                $email = $request->input('email');
                $workEmail = $request->input('work_email');
                $homePhone = $request->input('home_phone');
                $workPhone = $request->input('work_phone');
                $cellPhone = $request->input('cell_phone');
                $homeAddressLabel = $request->input('home_address_label');
                $homeAddressStreet = $request->input('home_address_street');
                $homeAddressCity = $request->input('home_address_city');
                $homeAddressState = $request->input('home_address_state');
                $homeAddressZip = $request->input('home_address_zip');
                $homeAddressCountry = $request->input('home_address_country');
                $workAddressLabel = $request->input('work_address_label');
                $workAddressStreet = $request->input('work_address_street');
                $workAddressCity = $request->input('work_address_city');
                $workAddressState = $request->input('work_address_state');
                $workAddressZip = $request->input('work_address_zip');
                $workAddressCountry = $request->input('work_address_country');
                // Create an array with all the input fields
                $data = [
                    'prefix' => $request->input('prefix'),
                    'first_name' => $request->input('first_name'),
                    'middle_name' => $request->input('middle_name'),
                    'last_name' => $request->input('last_name'),
                    'suffix' => $request->input('suffix'),
                    'nickname' => $request->input('nickname'),
                    'organization' => $request->input('organization'),
                    'vtitle' => $request->input('vtitle'),
                    'role' => $request->input('role'),
                    'work_url' => $request->input('work_url'),
                    'email' => $request->input('email'),
                    'work_email' => $request->input('work_email'),
                    'home_phone' => $request->input('home_phone'),
                    'work_phone' => $request->input('work_phone'),
                    'cell_phone' => $request->input('cell_phone'),
                    'home_address_label' => $request->input('home_address_label'),
                    'home_address_street' => $request->input('home_address_street'),
                    'home_address_city' => $request->input('home_address_city'),
                    'home_address_state' => $request->input('home_address_state'),
                    'home_address_zip' => $request->input('home_address_zip'),
                    'home_address_country' => $request->input('home_address_country'),
                    'work_address_label' => $request->input('work_address_label'),
                    'work_address_street' => $request->input('work_address_street'),
                    'work_address_city' => $request->input('work_address_city'),
                    'work_address_state' => $request->input('work_address_state'),
                    'work_address_zip' => $request->input('work_address_zip'),
                    'work_address_country' => $request->input('work_address_country'),
                ];
                // Convert the array to JSON format
                $json = json_encode($data);
                // Set the JSON as the variable $links->link, or null if the JSON is empty
                $LinkURL = $json ? $json : null;        
                $OrigLink->update([
                    'link' => $LinkURL,
                    'button_id' => 96,
                    'title' => $LinkTitle,
                ]);
            } else {
                $OrigLink->update([
                    'link' => $LinkURL,
                    'title' => $LinkTitle,
                    'button_id' => $button?->id,
                ]);
            }
            $message .="updated";
        } else {
            // ADDING NEW
            $isCustomWebsite = $customParams['GetSiteIcon'] ?? null;
            $SpacerHeight = $customParams['height'] ?? null;
            $links = new Link;
            $links->link = $LinkURL;
            $links->user_id = $userId;
            if ($linkType->typename == "spacer") {
                $links->title = $SpacerHeight;
            } else {
                $links->title = $LinkTitle;
            }
            if ($linkType->typename == "link" && $isCustomWebsite == "1") {
                $links->button_id = "2";
            } elseif ($linkType->typename == "link") {
                $links->button_id = "1";
            } elseif ($linkType->typename == "spacer") {
                $links->button_id = "43";
            } elseif ($linkType->typename == "heading") {
                $links->button_id = "42";
            } elseif ($linkType->typename == "text") {
                $sanitizedText = $request->text;
                $sanitizedText = strip_tags($sanitizedText, '<a><p><strong><i><ul><ol><li><blockquote><h2><h3><h4>');
                $sanitizedText = preg_replace("/<a([^>]*)>/i", "<a $1 rel=\"noopener noreferrer nofollow\">", $sanitizedText);
                $sanitizedText = strip_tags_except_allowed_protocols($sanitizedText);
                $links->button_id = "93";
                $links->title = $sanitizedText;
            } elseif ($linkType->typename == "email") {
                $links->button_id = $button?->id;
            } elseif ($linkType->typename == "telephone") {
                $links->button_id = $button?->id;
            } elseif ($linkType->typename == "vcard") {
                $prefix = $request->input('prefix');
                $firstName = $request->input('first_name');
                $middleName = $request->input('middle_name');
                $lastName = $request->input('last_name');
                $suffix = $request->input('suffix');
                $nickname = $request->input('nickname');
                $organization = $request->input('organization');
                $vtitle = $request->input('vtitle');
                $role = $request->input('role');
                $workUrl = $request->input('work_url');
                $email = $request->input('email');
                $workEmail = $request->input('work_email');
                $homePhone = $request->input('home_phone');
                $workPhone = $request->input('work_phone');
                $cellPhone = $request->input('cell_phone');
                $homeAddressLabel = $request->input('home_address_label');
                $homeAddressStreet = $request->input('home_address_street');
                $homeAddressCity = $request->input('home_address_city');
                $homeAddressState = $request->input('home_address_state');
                $homeAddressZip = $request->input('home_address_zip');
                $homeAddressCountry = $request->input('home_address_country');
                $workAddressLabel = $request->input('work_address_label');
                $workAddressStreet = $request->input('work_address_street');
                $workAddressCity = $request->input('work_address_city');
                $workAddressState = $request->input('work_address_state');
                $workAddressZip = $request->input('work_address_zip');
                $workAddressCountry = $request->input('work_address_country');
                // Create an array with all the input fields
                $data = [
                    'prefix' => $request->input('prefix'),
                    'first_name' => $request->input('first_name'),
                    'middle_name' => $request->input('middle_name'),
                    'last_name' => $request->input('last_name'),
                    'suffix' => $request->input('suffix'),
                    'nickname' => $request->input('nickname'),
                    'organization' => $request->input('organization'),
                    'vtitle' => $request->input('vtitle'),
                    'role' => $request->input('role'),
                    'work_url' => $request->input('work_url'),
                    'email' => $request->input('email'),
                    'work_email' => $request->input('work_email'),
                    'home_phone' => $request->input('home_phone'),
                    'work_phone' => $request->input('work_phone'),
                    'cell_phone' => $request->input('cell_phone'),
                    'home_address_label' => $request->input('home_address_label'),
                    'home_address_street' => $request->input('home_address_street'),
                    'home_address_city' => $request->input('home_address_city'),
                    'home_address_state' => $request->input('home_address_state'),
                    'home_address_zip' => $request->input('home_address_zip'),
                    'home_address_country' => $request->input('home_address_country'),
                    'work_address_label' => $request->input('work_address_label'),
                    'work_address_street' => $request->input('work_address_street'),
                    'work_address_city' => $request->input('work_address_city'),
                    'work_address_state' => $request->input('work_address_state'),
                    'work_address_zip' => $request->input('work_address_zip'),
                    'work_address_country' => $request->input('work_address_country'),
                ];
                // Convert the array to JSON format
                $json = json_encode($data);
                // Set the JSON as the variable $links->link, or null if the JSON is empty
                $links->link = $json ? $json : null;       
                $links->button_id = 96;
            } else {
                $links->button_id = $button?->id;
            }

            if (empty($links->button_id)) {
                throw new \Exception('Invalid link');
            }

            $links->save();

            $links->order = ($links->id - 1);
            $links->save();
            $message .= "added";
        }

        $response = [
            'success' => true,
            'message' => $message
        ];
    
        return response()->json($response);
    }

    public function getParamForm($typeid, $linkId = 0)
    {
        $linkType = LinkType::select('params', 'typename')->where('id', $typeid)->first();

        $data['params'] = '';
        $data['link_title'] = '';
        $data['link_url'] = '';
        $data['button_id'] = 0;

        if ($linkId) {
            $link = Link::find($linkId);
            $data['params'] = json_decode($link['type_params']);
            $data['link_title'] = $link->title;
            $data['link_url'] = $link->link;
            if (Route::currentRouteName() != 'showButtons') {
                $data['button_id'] = $link->button_id;
            }
        }

        if (!empty($linkType) && $linkType->typename === 'predefined') {
            // Get buttons list if showing predefined form
            $buttons = Button::select()->orderBy('name', 'asc')->get();
            foreach ($buttons as $btn) {
                $data['buttons'][] = [
                    'name' => $btn->name,
                    'title' => $btn->alt,
                    'exclude' => $btn->exclude,
                    'selected' => (is_object($data['params']) && $data['params']->button === $btn->name)
                ];
            }
            //echo "<pre>"; print_r($data['params']); exit;
        }

        return view('components.pageitems.' . $linkType->typename . '-form', $data);

        $jsonForm = FormFactory::jsonForm();
        try {
            $json = $linkType->params;
        } catch (\Throwable $th) {
            //throw $th;
        }

        // Dynamically create params for predefined website to fill a select list with available buttons
        if (!empty($linkType) && $linkType->typename === 'predefined') {
            $buttons = Button::select('name')->orderBy('name', 'asc')->get();
            $pdParams[] = ['tag' => 'select', 'name' => 'button', 'id' => 'button'];
            foreach ($buttons as $btn) {
                $pdParams[0]['value'][] = [
                    'tag' => 'option',
                    'label' => ucwords($btn->name),
                    'value' => $btn->name
                ];
            }
            $pdParams[] = ['tag' => 'input', 'name' => 'link_title', 'id' => 'link_title', 'tip' => 'Leave blank for default title'];
            $pdParams[] = ['tag' => 'input', 'name' => 'link_url', 'id' => 'link_url', 'tip' => 'Enter the url address for this site.'];

            $json = json_encode($pdParams);
        }

        if (empty($json)) {
            $json = <<<EOS
            [
                {
                    "tag": "input",
                    "id": "link_title",
                    "for": "link_title",
                    "label": "Link Title *",
                    "type": "text",
                    "name": "link_title",
                    "class": "form-control",
                    "tip": "Enter a title for this link",
                    "required": "required"
                },
                {
                    "tag": "input",
                    "id": "link",
                    "for": "link",
                    "label": "Link Address *",
                    "type": "text",
                    "name": "link_title",
                    "class": "form-control",
                    "tip": "Enter the website address",
                    "required": "required"
                }
            ]
            EOS;
        }

        if ($linkId) {
            $link = Link::find($linkId);
        }

        // Cleanup json
        $params = json_decode($json, true);
        $idx = 0;
        foreach ($params as $p) {
            if (!array_key_exists('for', $p)) {
                $params[$idx]['for'] = $p['name'];
            }
            if (!array_key_exists('label', $p)) {
                $params[$idx]['label'] = ucwords(preg_replace('/[^a-zA-Z0-9-]/', ' ', $p['name']));
            }
            if (!array_key_exists('class', $p) || !str_contains($p['class'], 'form-control')) {
                $params[$idx]['class'] = "form-control";
            }

            // Get existing values if any
            if ($link) {
                $typeParams = json_decode($link['type_params']);
                if ($typeParams && property_exists($typeParams, $params[$idx]['name'])) {
                    if (key_exists('value', $params[$idx]) && is_array($params[$idx]['value'])) {
                        $optIdx = 0;
                        foreach ($params[$idx]['value'] as $option) {
                            if ($option['value'] == $typeParams->{$params[$idx]['name']}) {
                                $params[$idx]['value'][$optIdx]['selected'] = true;
                                break;
                            }
                            $optIdx++;
                        }
                    } else {
                        $params[$idx]['value'] = $typeParams->{$params[$idx]['name']};
                    }
                }
            }
            $idx++;
        }
        $json = json_encode($params);

        echo $jsonForm->render($json);
    }


}
