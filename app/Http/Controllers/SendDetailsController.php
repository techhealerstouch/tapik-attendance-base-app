<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\DetailsMail;
use JeroenDesloovere\VCard\VCard;

class SendDetailsController extends Controller
{
    public function sendDetailsEmail(Request $request) {
        Log::info($request->all());
        $toEmail = $request->receipient;
        $firstname = $request->firstname;
        $lastname = $request->lastname;
        $fullName = $request->firstname . ' ' . $request->lastname;
        $mobile = $request->mobile;
        $email = $request->email;
        $company = $request->company;
        $street = $request->street;
        $city = $request->city;
        $state = $request->state;
        $zip = $request->zip;
        $country = $request->country;
        $title = $request->title;
        $subject = $fullName  ." Information";
        $urldownload = '/download-vcard?firstname='.$firstname.'&lastname='.$lastname.'&company='.  $company.'&title='. $title .'&mobile='. $mobile .'&email=' . $email . '&street=' . $street . '&city=' . $city . '&state=' . $state . '&zip=' . $zip . '&country=' . $country .'';

        $message = "Full Name: $fullName\n";
        $message .= "Mobile: $mobile\n";
        $message .= "Email: $email\n";
        $message .= "Company: $company\n";

        // Send email with the message
        Mail::to($toEmail)->send(new DetailsMail($message, $subject, $firstname, $lastname, $company, $title, $mobile, $email, $urldownload));
    }

    public function vcard(request $request)
    {
        $firstname = $request->query('firstname');
        $lastname = $request->query('lastname');
        $mobile = $request->query('mobile');
        $email = $request->query('email');
        $company = $request->query('company');
        $title = $request->query('title');
        $street = $request->query('street');
        $city = $request->query('city');
        $state = $request->query('state');
        $zip = $request->query('zip');
        $country = $request->query('country');

        $vcard = new VCard();
        $vcard->addName($lastname, $firstname);
        $vcard->addCompany($company);
        $vcard->addJobtitle($title);
        $vcard->addRole($title);
        $vcard->addEmail($email);
        $vcard->addEmail($email, 'WORK');
        $vcard->addPhoneNumber($mobile, 'WORK');
        $vcard->addAddress($street, '', $city, $state, $zip, $country, 'WORK');
        

        $file_contents = $vcard->getOutput();
        
        $headers = [
            'Content-Type' => 'text/x-vcard',
            'Content-Disposition' => 'attachment; filename="contact.vcf"'
        ];

        // Return the file download response
        return response()->make($file_contents, 200, $headers);

    }
}
