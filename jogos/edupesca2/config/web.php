<?php

$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'edupesca',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
	'language' => 'pt-BR',
    'sourceLanguage' => 'en-US',

    'components' => [
        'request' => [
            'cookieValidationKey' => '62975ff1f3c5bd263ca65b351bae295e',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
		'urlManager' => [
			'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => false,
        ],
		'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
		'authManager' => [
			'class' => 'yii\rbac\DbManager',
        ],
		'view' => [
			'class' => 'app\components\View',
			'theme' => [
				'pathMap' => [
					'@dektrium/user/views' => '@app/views/user',
					'@dektrium/rbac/views' => '@app/views/rbac',
				],
			],
		],
		'i18n' => [
			'translations' => [
				'app*' => [
					'class' => 'yii\i18n\PhpMessageSource',
					'basePath' => '@app/messages',
					'sourceLanguage' => 'en-US',
					'fileMap' => [
						'app' => 'app.php',
					],
				],
			],
		],
        'db' => require(__DIR__ . '/db.php'),
    ],

	'modules' => [
        'user' => [
            'class' => 'dektrium\user\Module',
			'admins' => ['Administrador'],
			'modelMap' => [
				'User' => 'app\models\User',
			],
			'controllerMap' => [
				'admin' => 'app\controllers\user\AdminController'
			],
        ],

		'rbac' => [
			'class' => 'dektrium\rbac\Module'
		]
    ],

    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] =  [
		'class' => 'yii\debug\Module',
		'allowedIPs' => ['127.0.0.1', '::1', '192.168.25.*', '192.168.1.*', '192.168.0.*'],
	];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] =  [
		'class' => 'yii\gii\Module',
		'allowedIPs' => ['127.0.0.1', '::1', '192.168.25.*', '192.168.1.*', '192.168.0.*'],
	];
}

return $config;
