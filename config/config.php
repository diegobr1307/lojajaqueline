<?php
// htdocs/config.php

$basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
if ($basePath === '/') {
    $basePath = '';
}

return [
    'basePath' => $basePath,

    'db' => [
        'dsn' => 'mysql:host=HOST;dbname=DBNAME;charset=utf8mb4',
        'user' => 'root',
        'pass' => '',
        'options' => [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ],
    ],
];

