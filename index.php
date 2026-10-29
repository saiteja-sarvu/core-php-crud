<?php
require_once __DIR__ . '/includes/functions.php';
if (current_user() !== null) redirect('modules/dashboard/index.php');
redirect('modules/auth/login.php');
