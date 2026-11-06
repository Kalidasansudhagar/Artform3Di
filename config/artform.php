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

    'tagline' => 'Where design meets technology',

    // Phone and address are confirmed in the client's website brief; email and hours are still to be supplied.
    'contact' => [
        'email' => env('ARTFORM_EMAIL'),
        'phone' => env('ARTFORM_PHONE', '+91 63812 98393'),
        'address' => env('ARTFORM_ADDRESS', 'No. 423, Village High Road, Sholinganallur, Chennai – 600119, Tamil Nadu, India'),
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
