<?php

return array(
    'environment' => 'development',
    'debug' => true,
    'name' => 'Chat-O-Mat',
    'version' => '0.1',
    'baseUrl' => 'http://projects.dev/chat-o-mat/web/', // WITH trailing slash

    // Admin email (& name)
    'email' => 'info@bobalazek.com',
    'emailName' => 'Chat-O-Mat Mailer',

    // Default Locale / Language stuff
    'locale' => 'en_US', // Default locale
    'languageCode' => 'en', // Default language code
    'languageName' => 'English',
    'countryCode' => 'us', // Default country code
    'flagCode' => 'us',
    'dateFormat' => 'd.m.Y',
    'dateTimeFormat' => 'd.m.Y H:i:s',

    // Time and date
    'currentTime' => date('H:i:s'),
    'currentDate' => date('Y-m-d'),
    'currentDateTime' => date('Y-m-d H:i:s'),

    // Database / Doctrine options
    'databaseOptions' => array(
        'default' => array(
            'driver' => 'pdo_mysql',
            'host' => 'localhost',
            'dbname' => 'chat_with_me',
            'user' => 'chat_with_me',
            'password' => 'chat_with_me',
            'charset' => 'utf8',
        ),
    ),

    // Swiftmailer options
    'swiftmailerOptions' => array(
        'host' => 'corcosoft.com',
        'port' => 465,
        'username' => 'info@corcosoft.com',
        'password' => '',
        'encryption' => 'ssl',
        'auth_mode' => null,
    ),

    // Default settings (the setting values from the DB
    //   will override this values)
    'settings' => array(

    ),
);
