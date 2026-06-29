<?php

return [
    'host'     => getenv('DB_HOST') ?: 'db',
    'port'     => getenv('DB_PORT') ?: '3306',
    'database' => getenv('DB_NAME') ?: 'parque_vehicular',
    'username' => getenv('DB_USER') ?: 'parque_user',
    'password' => getenv('DB_PASSWORD') ?: 'parque_pass',
    'charset'  => 'utf8mb4',
];
