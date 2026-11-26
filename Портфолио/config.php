<?php
return [
    'host' => getenv('PORTFOLIO_DB_HOST') ?: '127.0.0.1',
    'port' => getenv('PORTFOLIO_DB_PORT') ?: '3306',
    'database' => 'student_portfolio',
    'username' => getenv('PORTFOLIO_DB_USER') ?: 'root',
    'password' => getenv('PORTFOLIO_DB_PASSWORD') ?: '',
    'storage' => getenv('PORTFOLIO_STORAGE') ?: dirname(rtrim($_SERVER['DOCUMENT_ROOT'] ?? '', '/\\')) . '/portfolio-storage',
];

