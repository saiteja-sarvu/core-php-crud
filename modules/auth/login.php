<?php
require_once __DIR__ . '/../../includes/auth.php';
require_guest();

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) {
        $error = 'Your session expired. Refresh the page and try again.';
    } else {
        $email = strtolower(trim($_POST['email'] ?? ''));
        $password = (string)($_POST['password'] ?? '');
        $users = read_json_file(USER_FILE);

        if ($users === null) {
            $error = 'Unable to read user records. Run setup.php first.';
        } else {
            $matchedUser = null;
            foreach ($users as $user) {
                if (
                    isset($user['email'], $user['password']) &&
                    strtolower((string)$user['email']) === $email &&
                    password_verify($password, (string)$user['password'])
                ) {
                    $matchedUser = $user;
                    break;
                }
            }

            if ($matchedUser !== null) {
                session_regenerate_id(true);
                $_SESSION['user'] = [
                    'id' => $matchedUser['id'] ?? null,
                    'name' => $matchedUser['name'] ?? 'User',
                    'email' => $matchedUser['email']
                ];
                unset($_SESSION['csrf_token']);
                csrf_token();
                set_flash('success', 'Welcome back, ' . ($matchedUser['name'] ?? 'User') . '!');
                redirect('modules/dashboard/index.php');
            } else {
                $error = 'Invalid email address or password.';
            }
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | <?= escape(APP_NAME) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= escape(url('assets/css/login.css')) ?>" rel="stylesheet">
</head>
<body>
<div class="login-shell row g-0">
    <div class="col-md-6 login-art">
        <div>
            <div class="d-flex align-items-center fw-bold fs-4">
                <span class="brand-mark"><i class="bi bi-people-fill"></i></span>Admin-Panel
            </div>
        </div>
    </div>

    <div class="col-md-6 login-form d-flex flex-column justify-content-center">
        <div class="mb-4">
            <div class="text-primary fw-semibold small text-uppercase mb-2">Welcome</div>
            <h2 class="fw-bold">Sign in to your account</h2>
            <p class="text-secondary">Enter your credentials to continue.</p>
        </div>

        <?php display_flash(); ?>
        <?php if ($error !== ''): ?>
            <div class="alert alert-danger"><?= escape($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label for="email" class="form-label fw-semibold">Email address</label>
                <input type="email" class="form-control" id="email" name="email" value="<?= escape($email) ?>" placeholder="you@company.com" autocomplete="username" required>
            </div>
            <div class="mb-4">
                <label for="password" class="form-label fw-semibold">Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" autocomplete="current-password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Sign in <i class="bi bi-arrow-right ms-1"></i></button>
        </form>

        <p class="text-secondary small mt-4 mb-0">Admin-Panel</p>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
