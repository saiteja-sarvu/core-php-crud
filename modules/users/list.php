<?php
require_once __DIR__ . '/../../includes/auth.php';
require_login();

header('Content-Type: application/json');

$users = read_users();
if ($users === null) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Unable to read user records. Check storage/users.json.']);
    exit;
}
usort($users, fn($a, $b) => (int)($a['id'] ?? 0) <=> (int)($b['id'] ?? 0));

$currentId = (int)(current_user()['id'] ?? 0);
$result = array_map(fn($u) => [
    'id' => (int)($u['id'] ?? 0),
    'name' => (string)($u['name'] ?? ''),
    'email' => (string)($u['email'] ?? ''),
    'is_self' => (int)($u['id'] ?? 0) === $currentId,
], $users);

echo json_encode(['success' => true, 'users' => array_values($result)]);
