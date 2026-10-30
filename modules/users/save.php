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

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT) ?: null;
$name = trim((string)($_POST['name'] ?? ''));
$email = strtolower(trim((string)($_POST['email'] ?? '')));
$password = (string)($_POST['password'] ?? '');
$confirm = (string)($_POST['password_confirm'] ?? '');
$errors = [];

if ($name === '') $errors['name'] = 'Name is required.';
if ($email === '') $errors['email'] = 'Email address is required.';
elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Enter a valid email address.';
if ($id === null || $password !== '') {
    if (strlen($password) < 8) $errors['password'] = 'Password must be at least 8 characters.';
    elseif ($password !== $confirm) $errors['password_confirm'] = 'Passwords do not match.';
}

$users = read_users();
if ($users === null) {
    json_response(['success' => false, 'message' => 'Unable to read user data.'], 500);
}

$found = null;
foreach ($users as $index => $existing) {
    if ($id !== null && (int)($existing['id'] ?? 0) === $id) $found = $index;
    if ((int)($existing['id'] ?? 0) !== $id && isset($existing['email']) && strcasecmp((string)$existing['email'], $email) === 0) {
        $errors['email'] = 'A user with this email already exists.';
    }
}
if ($id !== null && $found === null) {
    json_response(['success' => false, 'message' => 'User not found.'], 404);
}
if ($errors) {
    json_response(['success' => false, 'message' => 'Please fix the highlighted fields.', 'errors' => $errors], 422);
}

if ($id !== null) {
    $users[$found]['name'] = $name;
    $users[$found]['email'] = $email;
    if ($password !== '') $users[$found]['password'] = password_hash($password, PASSWORD_DEFAULT);
    $message = 'User updated successfully.';
} else {
    $maxId = 0;
    foreach ($users as $existing) $maxId = max($maxId, (int)($existing['id'] ?? 0));
    $users[] = ['id' => $maxId + 1, 'name' => $name, 'email' => $email, 'password' => password_hash($password, PASSWORD_DEFAULT)];
    $message = 'User added successfully.';
}

if (!save_users($users)) {
    json_response(['success' => false, 'message' => 'Could not save user data. Check file permissions.'], 500);
}
if ($id !== null && (int)(current_user()['id'] ?? 0) === $id) {
    $_SESSION['user']['name'] = $name;
    $_SESSION['user']['email'] = $email;
}
json_response(['success' => true, 'message' => $message]);
