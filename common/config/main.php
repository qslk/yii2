<?php
return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],

        'authClientCollection' => [
            'class' => 'yii\authclient\Collection',
            'clients' => [
                'qq' => [
                    'class' => 'lulubin\oauth\Qq',
                    'clientId' => '101412608',
                    'clientSecret' => '0415fff1e8347f629ad2ccbfa5fd8d75',
                ],
                'google' => [
                    'class' => 'yii\authclient\clients\Google',
                    'clientId' => '634754228745-9rsd5ovnhf1p5djgu181vgl3dmc22d9i.apps.googleusercontent.com',
                    'clientSecret' => 'WpvKX5TKujMuPpQOVnHD95M_',
                ],
                'facebook' => [
                    'class' => 'yii\authclient\clients\Facebook',
                    'clientId' => 'facebook_client_id',
                    'clientSecret' => 'facebook_client_secret',
                ],
                'github' => [
                    'class' => 'yii\authclient\clients\GitHub',
                    'clientId' => 'c2028f3a098dbb891a1f',
                    'clientSecret' => 'bda3ff5a7c5567c6e902e5b335be2a3216244b65',
                ],
            ],
        ],

        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],

    ],
];
