<?php
return [
    'backend' => [
        'frontName' => 'admin_xn9xoq'
    ],
    'crypt' => [
        'key' => 'edf9fdb070f81209952fdc8c11c600bf'
    ],
    'db' => [
        'table_prefix' => '',
        'connection' => [
            'default' => [
                'host' => 'localhost',
                'dbname' => 'brandshopco_lg_new_2021',
                'username' => 'brandshopco_lg_new2021',
                'password' => 'PbseUcl@AIH9',
                'active' => '1',
                'driver_options' => [
                    1014 => false
                ],
                'model' => 'mysql4',
                'engine' => 'innodb',
                'initStatements' => 'SET NAMES utf8;'
            ]
        ]
    ],
    'resource' => [
        'default_setup' => [
            'connection' => 'default'
        ]
    ],
    'x-frame-options' => 'SAMEORIGIN',
    'MAGE_MODE' => 'developer',
    'session' => [
        'save' => 'files'
    ],
    'cache' => [
        'frontend' => [
            'default' => [
                'id_prefix' => 'd99_'
            ],
            'page_cache' => [
                'id_prefix' => 'd99_'
            ]
        ]
    ],
    'lock' => [
        'provider' => 'db',
        'config' => [
            'prefix' => ''
        ]
    ],
    'cache_types' => [
        'config' => 1,
        'layout' => 1,
        'block_html' => 1,
        'collections' => 1,
        'reflection' => 1,
        'db_ddl' => 1,
        'compiled_config' => 1,
        'eav' => 1,
        'customer_notification' => 1,
        'config_integration' => 1,
        'config_integration_api' => 1,
        'full_page' => 1,
        'config_webservice' => 1,
        'translate' => 1
    ],
    'install' => [
        'date' => 'Tue, 24 Mar 2020 13:09:36 +0000'
    ],
    'dev' => [
        'debug' => [
            'debug_logging' => 1
        ]
    ],
    'queue' => [
        'consumers_wait_for_messages' => 1
    ]
];
