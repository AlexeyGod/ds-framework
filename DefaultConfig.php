<?php
/**
 * Created by Digital-Solution.Ru web-studio.
 * https://digital-solution.ru
 * support@digital-solution.ru
 */

namespace framework;


class DefaultConfig
{

    public static function load($userConfig = [])
    {
        if(!is_array($userConfig))
        $userConfig = [];
        
        return array_replace_recursive(
            array_merge(
            self::getComponents(),
            self::getAliases(),
            self::getContainers()
        ),
            $userConfig);
    }

    public static function getComponents()
    {
        return [
            'components' =>
                [
                    'logger' => [
                        'class' => 'framework\\helpers\\Logger'
                    ],
                    'db' => [
                        'class' => 'framework\\components\\db\\DataBase',
                        'options' => [
                            'host' => 'localhost',
                            'database' => 'project',
                            'username' => 'root',
                            'password' => '',
                            'defaultCharset' => 'utf-8',
                        ],
                    ],
                    'request' => [
                        'class' => 'framework\\components\\Request',
                    ],
                    'urlManager' => [
                        'class' => 'framework\\components\\routing\\UrlManager',
                        'options' => [
                            'rules' => [
                                '' => 'default/index',
                                '<action: [A-Za-z0-9_-]+>' => '<action>',
                                '<controller: [A-Za-z0-9_-]+>/<action: [A-Za-z0-9_-]+>' => '<controller>/<action>',
                                '<module: [A-Za-z0-9_-]+>/<controller: [A-Za-z0-9_-]+>/<action: [A-Za-z0-9_-]+>' => '<module>/<controller>/<action>',
                                '<module: [A-Za-z0-9_-]+>/<controller: [A-Za-z0-9_-]+>/<action: [A-Za-z0-9_-]+>/<id: [0-9]+>' => '<module>/<controller>/<action>',
                            ]
                        ]
                    ],
                    'route' => [
                        'class' => 'framework\\components\\Route'
                    ],
                    'assetManager' => [
                        'class' => 'framework\\components\\AssetManager',
                        'options' => [
                            'autoReload' => false,
                            'versionAppend' => false,
                        ]
                    ],
                    'identy' => [
                        'class' => 'modules\\user\\models\\User',
                        'options' => [
                            'autoLogin' => true
                        ]
                    ],
                ],

        ];
    }


    public static function getContainers()
    {
        return [
            'containers' =>
                [
                    'framework\\components\\View' => [
                        'viewPath' => '@root/themes'
                    ],
                    'framework\\helpers\\captcha\\Captcha' => [
                        'image_dir' => '@framework/helpers/captcha/img',
                        'fonts_path' =>  '@framework/helpers/captcha/fonts',
                        'verifyCodeName' => 'ds-captcha',
                    ]
                ],
        ];
    }

    public static function getAliases()
    {
        return [
            'aliases' =>
                [
                    '@domain' => getenv('HTTP_HOST'),
                    '@webroot' => getenv('DOCUMENT_ROOT'),
                    '@root' => getenv('DOCUMENT_ROOT'),
                    '@web' => '/',
                    '@application' => getenv('DOCUMENT_ROOT').'/application',
                    '@themes' => getenv('DOCUMENT_ROOT').'/themes',
                    '@uploadPath' => '/uploads', // Указывается относительно ROOT
                ],
        ];
    }
}