<?php
require_once __DIR__ . '/../../includes/auth.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    set_flash('warning', 'Invalid request.');
    redirect('modules/employees/index.php');
}
if (!verify_csrf()) {
    set_flash('danger', 'Your session expired. Please try again.');
    redirect('modules/employees/index.php');
}
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id || $id < 1) {
    set_flash('danger', 'Invalid employee ID.');
    redirect('modules/employees/index.php');
}
$employees = read_employees();
if ($employees === null) {
    set_flash('danger', 'Unable to read employee records.');
    redirect('modules/employees/index.php');
}
$found = null;
foreach ($employees as $index => $employee) {
    if ((int)($employee['id'] ?? 0) === $id) { $found = $index; break; }
}
if ($found === null) {
    set_flash('warning', 'Employee not found.');
    redirect('modules/employees/index.php');
}
unset($employees[$found]);
if (save_employees($employees)) set_flash('success', 'Employee deleted successfully.');
else set_flash('danger', 'Could not delete employee. Check file permissions.');
redirect('modules/employees/index.php');
