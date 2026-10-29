<?php
require_once __DIR__ . '/includes/functions.php';

$users = read_json_file(USER_FILE);
if ($users === null) {
    http_response_code(500);
    exit('Unable to read storage/users.json. Check file permissions and JSON format.');
}

$adminExists = false;
foreach ($users as $user) {
    if (isset($user['email']) && strtolower((string)$user['email']) === 'admin@example.com') {
        $adminExists = true;
        break;
    }
}

if (!$adminExists) {
    $maxId = 0;
    foreach ($users as $user) $maxId = max($maxId, (int)($user['id'] ?? 0));
    $users[] = [
        'id' => $maxId + 1,
        'name' => 'Administrator',
        'email' => 'admin@example.com',
        'password' => password_hash('Admin@123', PASSWORD_DEFAULT)
    ];

    if (!write_json_file(USER_FILE, $users)) {
        http_response_code(500);
        exit('Unable to save storage/users.json. Check file permissions.');
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= escape(APP_NAME) ?> Setup</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= escape(url('assets/css/style.css')) ?>" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="card border-0 shadow-sm mx-auto setup-card">
        <div class="card-body p-4 p-md-5">
            <div class="text-success fs-1 mb-3"><i class="bi bi-check-circle"></i></div>
            <h1 class="h3 mb-3">Setup complete</h1>
            <p>Your administrator account is ready.</p>
            <div class="alert alert-info">
                <div><strong>Email:</strong> admin@example.com</div>
                <div><strong>Password:</strong> Admin@123</div>
            </div>
            <p class="text-danger small">Delete setup.php after setup. Change the demo password before using this application on a shared or live server.</p>
            <a href="modules/auth/login.php" class="btn btn-primary">Go to Login</a>
        </div>
    </div>
</div>
</body>
</html>
