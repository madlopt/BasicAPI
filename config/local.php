<?php
/*
 * Configuration file for local environment
 */

return [
    'php_version' => '7.4',
    'default_timezone' => 'Europe/Kiev',
    'logs_dir' => 'var' . DIRECTORY_SEPARATOR . 'logs',
    'db_dir' => 'var' . DIRECTORY_SEPARATOR . 'files',
    'db_file' => 'sqlitedb',
    'logs_suffix' => '_basic_api',
    'headers' => [
        "Accept" => " app/json, */*; q=0.01",
        "Content-Type" => " text/plain; charset=UTF-8",
        "Cache-Control" => " no-store, no-cache, must-revalidate",
        "Pragma" => " no-cache"
    ]
];