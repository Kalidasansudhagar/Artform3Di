<?php

/*
|--------------------------------------------------------------------------
| ARTFORM 3Di brand settings
|--------------------------------------------------------------------------
|
| Contact and social values are intentionally left null until the client
| confirms them. Views render a clear placeholder whenever a value is null,
| so nothing is invented on the public site.
|
*/

return [

    'tagline' => 'Premium 3D Art, Creative Manufacturing and Engineering Innovation.',

    'contact' => [
        'email' => env('ARTFORM_EMAIL'),
        'phone' => env('ARTFORM_PHONE'),
        'address' => env('ARTFORM_ADDRESS'),
        'hours' => env('ARTFORM_HOURS'),
    ],

    'social' => [
        'Instagram' => env('ARTFORM_INSTAGRAM'),
        'LinkedIn' => env('ARTFORM_LINKEDIN'),
        'YouTube' => env('ARTFORM_YOUTUBE'),
        'Facebook' => env('ARTFORM_FACEBOOK'),
    ],

    // Drop the client's footage at these paths; the hero falls back to the
    // animated print sequence while the files are missing.
    'hero_video' => [
        'desktop' => 'assets/videos/hero-printing.mp4',
        'mobile' => 'assets/videos/hero-printing-mobile.mp4',
        'poster' => 'assets/images/hero/hero-poster.svg',
    ],

];
