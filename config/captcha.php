<?php

return [

    //'characters' => '12346789abcdefghjmnpqrtuxyzABCDEFGHJMNPQRTUXYZ',
    'characters' => '123467890',

    'default'   => [
        'length'    => 4,
        'width'     => 140,
        'height'    => 30,
        'quality'   => 100,
    ],

    'flat'   => [
        'length'    => 4,
        'width'     => 160,
        'height'    => 46,
        'quality'   => 100,
        'lines'     => 6,
        'bgImage'   => false,
        'bgColor'   => '#ecf2f4',
        'fontColors'=> ['#2c3e50', '#c0392b', '#16a085', '#c0392b', '#8e44ad', '#303f9f', '#f57c00', '#795548'],
        'contrast'  => -5,
    ],

    'mini'   => [
        'length'    => 4,
        'width'     => 60,
        'height'    => 32,
    ],

    'inverse'   => [
        'length'    => 4,
        'width'     => 140,
        'height'    => 30,
        'quality'   => 100,
        'sensitive' => true,
        'angle'     => 12,
        'sharpen'   => 10,
        'blur'      => 2,
        'invert'    => true,
        'contrast'  => -5,
    ]

];
