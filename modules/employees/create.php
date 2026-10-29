<?php
require_once __DIR__ . '/../../includes/auth.php';
require_login();

$errors = [];
$name = '';
$email = '';
$salary = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) $errors[] = 'Your session expired. Refresh the page and try again.';
    $name = trim($_POST['name'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $salary = trim($_POST['salary'] ?? '');

    if ($name === '') $errors[] = 'Employee name is required.';
    if ($email === '') $errors[] = 'Email address is required.';
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Enter a valid email address.';
    if ($salary === '') $errors[] = 'Salary is required.';
    elseif (!is_numeric($salary) || !is_finite((float)$salary) || (float)$salary < 0) $errors[] = 'Enter a valid non-negative salary.';

    $employees = read_employees();
    if ($employees === null) {
        $errors[] = 'Unable to read employee data.';
        $employees = [];
    }
    foreach ($employees as $employee) {
        if (isset($employee['email']) && strcasecmp((string)$employee['email'], $email) === 0) {
            $errors[] = 'An employee with this email already exists.';
            break;
        }
    }

    if (!$errors) {
        $maxId = 0;
        foreach ($employees as $employee) $maxId = max($maxId, (int)($employee['id'] ?? 0));
        $employees[] = ['id' => $maxId + 1, 'name' => $name, 'email' => $email, 'salary' => (float)$salary];
        if (save_employees($employees)) {
            set_flash('success', 'Employee added successfully.');
            redirect('modules/employees/index.php');
        }
        $errors[] = 'Could not save employee data. Check file permissions.';
    }
}

$pageTitle = 'Add Employee';
$activeMenu = 'employees';
require __DIR__ . '/../../includes/header.php';
?>
<div class="mb-4">
    <a href="<?= escape(url('modules/employees/index.php')) ?>" class="text-decoration-none muted small">
        <i class="bi bi-arrow-left me-1"></i>Back to employees
    </a>
    <h1 class="h2 page-title mt-2 mb-1">Add employee</h1>
    <p class="muted mb-0">Add a new person to your employee directory.</p>
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

    <form method="POST" action="<?= escape(url('modules/employees/create.php')) ?>">
        <?= csrf_field() ?>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold" for="name">Full name</label>
                <input class="form-control" id="name" name="name" value="<?= escape($name) ?>" placeholder="e.g. Rahul Sharma" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold" for="email">Work email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?= escape($email) ?>" placeholder="name@company.com" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold" for="salary">Monthly salary (₹)</label>
                <input type="number" min="0" step="0.01" class="form-control" id="salary" name="salary" value="<?= escape($salary) ?>" placeholder="25000" required>
            </div>
        </div>
        <hr class="my-4">
        <button class="btn btn-primary px-4" type="submit"><i class="bi bi-check2 me-2"></i>Save employee</button>
        <a href="<?= escape(url('modules/employees/index.php')) ?>" class="btn btn-light border">Cancel</a>
    </form>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
