<?php

return array(

    'BurnVideoIOS'     => array(
        'environment' =>'production',
        'certificate' => storage_path().'/push_pem/ckPro.pem',
        'passPhrase'  =>'BurnVideo',
        'service'     =>'apns'
    ),
    'appNameAndroid' => array(
        'environment' =>'production',
        'apiKey'      =>'yourAPIKey',
        'service'     =>'gcm'
    )

);