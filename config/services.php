<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],

    'ses' => [
        'key' => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => 'us-east-1',
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model' => burnvideo\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],
	'contact_email' => 'questions@burnvideo.net',
    'braintree' => [
//        Sandbox Keys
//        'merchant_id' => 'hw5txybw6j22xj5z',
//        'public_key' => 'pjzwx99k2bsxc8d5',
//        'private_key' => '5fa6c59b3af623921990e5278ba26df3'

//      Production Key
		'mode' => env('BRAINTREE_MODE'),
        'merchant_id' => env('MERCHANTID'),
        'public_key' => env('PUBLICKEY'),
        'private_key' => env('PRIVATEKEY')
    ],
    'price-per-dvd' => '5.99',
    'price-per-monthly' => '5.99',
    'dvd-per-month' => '50',
    'price-extra-space' => '1.99',
    'spaces-per-dvd' => '50',
    'blog' => [
        'og_twitter' => 'burnvideo',
        'site_name' => 'burnvideo',
        'og_author' => 'burnvideo',
        'og_publisher' => 'burnvideo',
        'base_name' => 'burnvideo',
        'base_path' => '/blog/',
    ],
    'instanceNumber' => env('INSTANCENUMBER'),
    'appRootPath' => env('APPROOTPATH')

];
