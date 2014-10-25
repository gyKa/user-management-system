<?php

return [
    'paths' => [
        'migrations' => __DIR__.'/../migrations'
    ],
    'environments' => [
        'default_migration_table' => 'migrations',
        'default_database' => 'dev-mysql',
        'dev-mysql' => [
            'adapter' => 'mysql',
            'host' => 'localhost',
            'name' => 'ums',
            'user' => 'ums',
            'pass' => '',
            'port' => 3306
        ],
    ]
];
