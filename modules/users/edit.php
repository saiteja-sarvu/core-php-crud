<?php
require_once __DIR__ . '/../../includes/auth.php';
require_login();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id || $id < 1) {
    set_flash('danger', 'Invalid user ID.');
    redirect('modules/users/index.php');
}
$users = read_users();
if ($users === null) {
    set_flash('danger', 'Unable to read user records.');
    redirect('modules/users/index.php');
}
$userIndex = null;
foreach ($users as $index => $u) {
    if ((int)($u['id'] ?? 0) === $id) { $userIndex = $index; break; }
}
if ($userIndex === null) {
    set_flash('warning', 'User not found.');
    redirect('modules/users/index.php');
}
$name = (string)($users[$userIndex]['name'] ?? '');
$email = (string)($users[$userIndex]['email'] ?? '');
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) $errors[] = 'Your session expired. Refresh the page and try again.';
    $name = trim($_POST['name'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = (string)($_POST['password'] ?? '');
    $confirm = (string)($_POST['password_confirm'] ?? '');

    if ($name === '') $errors[] = 'Name is required.';
    if ($email === '') $errors[] = 'Email address is required.';
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Enter a valid email address.';
    if ($password !== '') {
        if (strlen($password) < 8) $errors[] = 'Password must be at least 8 characters.';
        elseif ($password !== $confirm) $errors[] = 'Passwords do not match.';
    }

    foreach ($users as $index => $existing) {
        if ($index !== $userIndex && isset($existing['email']) && strcasecmp((string)$existing['email'], $email) === 0) {
            $errors[] = 'Another user already uses this email address.';
            break;
        }
    }
    if (!$errors) {
        $users[$userIndex]['name'] = $name;
        $users[$userIndex]['email'] = $email;
        if ($password !== '') $users[$userIndex]['password'] = password_hash($password, PASSWORD_DEFAULT);
        if (save_users($users)) {
            if ((int)(current_user()['id'] ?? 0) === $id) {
                $_SESSION['user']['name'] = $name;
                $_SESSION['user']['email'] = $email;
            }
            set_flash('success', 'User updated successfully.');
            redirect('modules/users/index.php');
        }
        $errors[] = 'Could not save user data. Check file permissions.';
    }
}

$pageTitle = 'Edit User';
$activeMenu = 'users';
require __DIR__ . '/../../includes/header.php';
?>
<div class="mb-4">
    <a href="<?= escape(url('modules/users/index.php')) ?>" class="text-decoration-none muted small">
        <i class="bi bi-arrow-left me-1"></i>Back to users
    </a>
    <h1 class="h2 page-title mt-2 mb-1">Edit user</h1>
    <p class="muted mb-0">Update this account. Leave the password blank to keep it unchanged.</p>
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

    <form method="POST" action="<?= escape(url('modules/users/edit.php?id=' . $id)) ?>">
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
                <label class="form-label fw-semibold" for="password">New password</label>
                <input type="password" class="form-control" id="password" name="password" minlength="8" autocomplete="new-password" placeholder="Leave blank to keep current">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold" for="password_confirm">Confirm new password</label>
                <input type="password" class="form-control" id="password_confirm" name="password_confirm" minlength="8" autocomplete="new-password">
            </div>
        </div>
        <hr class="my-4">
        <button class="btn btn-primary px-4" type="submit"><i class="bi bi-check2 me-2"></i>Update user</button>
        <a href="<?= escape(url('modules/users/index.php')) ?>" class="btn btn-light border">Cancel</a>
    </form>
</div>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
