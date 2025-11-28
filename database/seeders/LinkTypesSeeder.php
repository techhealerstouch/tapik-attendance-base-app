<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LinkTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $table = 'link_types'; // <-- change if needed

        DB::table($table)->updateOrInsert([
            'typename' => 'predefined'
        ], [
            'title' => 'Predefined Site',
            'icon' => 'bi bi-boxes',
            'description' => 'Select from a list of predefined websites and have your link automatically styled using that sites brand colors and icon.'
        ]);

        DB::table($table)->updateOrInsert([
            'typename' => 'link'
        ], [
            'title' => 'Custom Link',
            'icon' => 'bi bi-link',
            'description' => 'Create a Custom Link that goes to any website. Customize the button styling and icon, or use the favicon from the website as the button icon.',
            'params' => '[{
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
                "id": "link_url",
                "for": "link_url",
                "label": "Link Address *",
                "type": "text",
                "name": "link_url",
                "class": "form-control",
                "tip": "Enter the website address",
                "required": "required"
            }]'
        ]);

        DB::table($table)->updateOrInsert([
            'typename' => 'heading'
        ], [
            'title' => 'Heading',
            'icon' => 'bi bi-card-heading',
            'description' => 'Use headings to organize your links and separate them into groups.',
            'params' => '[{
                "tag": "input",
                "id": "heading-text",
                "for": "link_title",
                "label": "Heading Text",
                "type": "text",
                "name": "link_title",
                "class": "form-control"
            }]'
        ]);

        DB::table($table)->updateOrInsert([
            'typename' => 'spacer'
        ], [
            'title' => 'Spacer',
            'icon' => 'bi bi-distribute-vertical',
            'description' => 'Add blank space to your list of links. You can choose how tall.',
            'params' => '[{
                "tag": "input",
                "type": "number",
                "min": "1",
                "max": "10",
                "name": "spacer_size",
                "id": "spacer_size",
                "value": 3
            }]'
        ]);

        //         DB::table($this->table)->updateOrInsert([
//             'typename' => 'video',
//             'title' => 'Video',
//             'icon' => 'bi bi-play-btn',
//             'description' => 'Embed or link to a video on another website, such as TikTok, YouTube etc.',
//             'params' => '[
//                 {
//                 "tag": "input",
//                 "id": "link_url",
//                 "for": "link_url",
//                 "label": "URL of video",
//                 "type": "text",
//                 "name": "link_url",
//                 "class": "form-control",
//                 "tip": "Enter the website address",
//                 "required": "required"
//             }
//     {
//         "tag": "select",
//         "name": "video-option",
//         "id": "video-option",

//         "value": [
//             {
//                 "tag": "option",
//                 "label": "Link to video ",
//                 "value": "link"
//             },
//             {
//                 "tag": "option",
//                 "label": "Embed Video",
//                 "value": "embed"
//             },

//         ]
//     }
// ]'
//         ]);

        DB::table($table)->updateOrInsert([
            'typename' => 'text'
        ], [
            'title' => 'Text',
            'icon' => 'bi bi-fonts',
            'description' => 'Add static text to your page that is not clickable.',
            'params' => '[{
                "tag": "textarea",
                "id": "static-text",
                "for": "static_text",
                "label": "Text",
                "name": "static_text",
                "class": "form-control"
            }]'
        ]);

        DB::table($table)->updateOrInsert([
            'typename' => 'email'
        ], [
            'title' => 'E-Mail address',
            'icon' => 'bi bi-envelope-fill',
            'description' => 'Add an email that opens a system dialog to compose a new email.'
        ]);

        DB::table($table)->updateOrInsert([
            'typename' => 'telephone'
        ], [
            'title' => 'Telephone number',
            'icon' => 'bi bi-telephone-fill',
            'description' => 'Add a telephone number that opens a system dialog to initiate a phone call.'
        ]);

        DB::table($table)->updateOrInsert([
            'typename' => 'vcard'
        ], [
            'title' => 'Vcard',
            'icon' => 'bi bi-person-square',
            'description' => 'Create or upload an electronic business card.'
        ]);
    }
}
