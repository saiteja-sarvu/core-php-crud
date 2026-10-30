<?php
require_once __DIR__ . '/../../includes/auth.php';
require_login();

header('Content-Type: application/json');

function json_response(array $payload, int $status = 200): void
{
    http_response_code($status);
    echo json_encode($payload);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'message' => 'Invalid request.'], 405);
}
if (!verify_csrf()) {
    json_response(['success' => false, 'message' => 'Your session expired. Refresh the page and try again.'], 403);
}
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id || $id < 1) {
    json_response(['success' => false, 'message' => 'Invalid user ID.'], 422);
}
if ((int)(current_user()['id'] ?? 0) === $id) {
    json_response(['success' => false, 'message' => 'You cannot delete your own account.'], 422);
}
$users = read_users();
if ($users === null) {
    json_response(['success' => false, 'message' => 'Unable to read user records.'], 500);
}
$found = null;
foreach ($users as $index => $u) {
    if ((int)($u['id'] ?? 0) === $id) { $found = $index; break; }
}
if ($found === null) {
    json_response(['success' => false, 'message' => 'User not found.'], 404);
}
unset($users[$found]);
if (!save_users($users)) {
    json_response(['success' => false, 'message' => 'Could not delete user. Check file permissions.'], 500);
}
json_response(['success' => true, 'message' => 'User deleted successfully.']);
