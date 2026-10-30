<?php
require_once __DIR__ . '/../../includes/auth.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    set_flash('warning', 'Invalid request.');
    redirect('modules/users/index.php');
}
if (!verify_csrf()) {
    set_flash('danger', 'Your session expired. Please try again.');
    redirect('modules/users/index.php');
}
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id || $id < 1) {
    set_flash('danger', 'Invalid user ID.');
    redirect('modules/users/index.php');
}
if ((int)(current_user()['id'] ?? 0) === $id) {
    set_flash('warning', 'You cannot delete your own account.');
    redirect('modules/users/index.php');
}
$users = read_users();
if ($users === null) {
    set_flash('danger', 'Unable to read user records.');
    redirect('modules/users/index.php');
}
$found = null;
foreach ($users as $index => $u) {
    if ((int)($u['id'] ?? 0) === $id) { $found = $index; break; }
}
if ($found === null) {
    set_flash('warning', 'User not found.');
    redirect('modules/users/index.php');
}
unset($users[$found]);
if (save_users($users)) set_flash('success', 'User deleted successfully.');
else set_flash('danger', 'Could not delete user. Check file permissions.');
redirect('modules/users/index.php');
