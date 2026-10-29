<?php
require_once __DIR__ . '/../../includes/auth.php';
require_login();

$errors = [];
$name = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) $errors[] = 'Your session expired. Refresh the page and try again.';
    $name = trim($_POST['name'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = (string)($_POST['password'] ?? '');
    $confirm = (string)($_POST['password_confirm'] ?? '');

    if ($name === '') $errors[] = 'Name is required.';
    if ($email === '') $errors[] = 'Email address is required.';
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Enter a valid email address.';
    if (strlen($password) < 8) $errors[] = 'Password must be at least 8 characters.';
    elseif ($password !== $confirm) $errors[] = 'Passwords do not match.';

    $users = read_users();
    if ($users === null) {
        $errors[] = 'Unable to read user data.';
        $users = [];
    }
    foreach ($users as $existing) {
        if (isset($existing['email']) && strcasecmp((string)$existing['email'], $email) === 0) {
            $errors[] = 'A user with this email already exists.';
            break;
        }
    }

    if (!$errors) {
        $maxId = 0;
        foreach ($users as $existing) $maxId = max($maxId, (int)($existing['id'] ?? 0));
        $users[] = ['id' => $maxId + 1, 'name' => $name, 'email' => $email, 'password' => password_hash($password, PASSWORD_DEFAULT)];
        if (save_users($users)) {
            set_flash('success', 'User added successfully.');
            redirect('modules/users/index.php');
        }
        $errors[] = 'Could not save user data. Check file permissions.';
    }
}

$pageTitle = 'Add User';
$activeMenu = 'users';
require __DIR__ . '/../../includes/header.php';
?>
<div class="mb-4">
    <a href="<?= escape(url('modules/users/index.php')) ?>" class="text-decoration-none muted small">
        <i class="bi bi-arrow-left me-1"></i>Back to users
    </a>
    <h1 class="h2 page-title mt-2 mb-1">Add user</h1>
    <p class="muted mb-0">Create a new account that can sign in.</p>
</div>

<div class="surface form-card p-4 p-lg-5">
    <?php if ($errors): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?= escape($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?= escape(url('modules/users/create.php')) ?>">
        <?= csrf_field() ?>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold" for="name">Full name</label>
                <input class="form-control" id="name" name="name" value="<?= escape($name) ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold" for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?= escape($email) ?>" autocomplete="off" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold" for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" minlength="8" autocomplete="new-password" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold" for="password_confirm">Confirm password</label>
                <input type="password" class="form-control" id="password_confirm" name="password_confirm" minlength="8" autocomplete="new-password" required>
            </div>
        </div>
        <hr class="my-4">
        <button class="btn btn-primary px-4" type="submit"><i class="bi bi-check2 me-2"></i>Save user</button>
        <a href="<?= escape(url('modules/users/index.php')) ?>" class="btn btn-light border">Cancel</a>
    </form>
</div>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
