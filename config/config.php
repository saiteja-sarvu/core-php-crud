<?php
declare(strict_types=1);

// Central configuration for the application.
define('APP_NAME', 'Project-Panel');
define('BASE_URL', '/core-php-crud-modular');

define('STORAGE_DIR', dirname(__DIR__) . '/storage');
define('EMPLOYEE_FILE', STORAGE_DIR . '/employees.json');
define('USER_FILE', STORAGE_DIR . '/users.json');

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
