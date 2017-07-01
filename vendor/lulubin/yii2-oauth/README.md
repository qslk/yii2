## [安装、使用方法]

1、安装 lulubin/yii2-oauth

composer require --prefer-dist lulubin/yii2-oauth dev-master

2、配 置，在components中增加如下内容

'components' => [
    'authClientCollection' => [
        'class' => 'yii\authclient\Collection',
        'clients' => [
            'qq' => [
                'class' => 'lulubin\oauth\Qq',
                'clientId' => '***',
                'clientSecret' => '***',
            ],
            'weibo' => [
                'class' => 'lulubin\oauth\Weibo',
                'clientId' => '***',
                'clientSecret' => '***',
            ],
            'weixin' => [
                'class' => 'lulubin\oauth\Weixin',
                'clientId' => '***',
                'clientSecret' => '***',
            ],
            'github' => [
                'class' => 'yii\authclient\clients\GitHub',
                'clientId' => '***',
                'clientSecret' => '***,
            ],
        ]
    ]
]
