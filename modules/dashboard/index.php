<?php
require_once __DIR__ . '/../../includes/auth.php';
require_login();

$employees = read_employees();
if ($employees === null) {
    $employees = [];
    set_flash('danger', 'Unable to read employee records. Check storage/employees.json.');
}
$totalSalary = 0;
foreach ($employees as $employee) $totalSalary += (float)($employee['salary'] ?? 0);
$recent = $employees;
usort($recent, fn($a, $b) => (int)($b['id'] ?? 0) <=> (int)($a['id'] ?? 0));
$recent = array_slice($recent, 0, 5);

$pageTitle = 'Dashboard';
$activeMenu = 'dashboard';
require __DIR__ . '/../../includes/header.php';
?>

<div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
        <h1 class="h2 page-title mb-1">Dashboard</h1>
        <p class="muted mb-0">Here is an overview of workspace.</p>
    </div>
    <!-- <a href="<?= escape(url('modules/employees/create.php')) ?>" class="btn btn-primary"><i class="bi bi-plus-lg me-2"></i>Add employee</a> -->
</div>

<!--
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-4">
        <div class="surface p-4 h-100">
            <div class="d-flex justify-content-between">
                <div>
                    <div class="muted small mb-2">Total employees</div>
                    <div class="display-6 fw-bold"><?= count($employees) ?></div>
                </div>
                <div class="stat-icon stat-indigo"><i class="bi bi-people"></i></div>
            </div>
            <div class="small text-success mt-3"><i class="bi bi-check-circle me-1"></i>Records in your directory</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-4">
        <div class="surface p-4 h-100">
            <div class="d-flex justify-content-between">
                <div>
                    <div class="muted small mb-2">Combined salary</div>
                    <div class="h2 fw-bold mb-0">₹<?= number_format($totalSalary, 2) ?></div>
                </div>
                <div class="stat-icon stat-green"><i class="bi bi-currency-rupee"></i></div>
            </div>
            <div class="small muted mt-3">Sum of listed monthly salaries</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-4">
        <div class="surface p-4 h-100">
            <div class="d-flex justify-content-between">
                <div>
                    <div class="muted small mb-2">Account status</div>
                    <div class="h2 fw-bold mb-0">Active</div>
                </div>
                <div class="stat-icon stat-orange"><i class="bi bi-shield-check"></i></div>
            </div>
            <div class="small muted mt-3">You are signed in securely</div>
        </div>
    </div>
</div>

<div class="surface overflow-hidden">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 p-4 border-bottom">
        <div>
            <h2 class="h5 fw-bold mb-1">Recently added employees</h2>
            <p class="muted small mb-0">Latest employee records in your workspace.</p>
        </div>
        <a href="<?= escape(url('modules/employees/index.php')) ?>" class="btn btn-outline-secondary btn-sm">
            View all employees <i class="bi bi-arrow-right ms-1"></i>
        </a>
    </div>

    <?php if (!$recent): ?>
        <div class="text-center py-5">
            <div class="fs-1 text-secondary mb-2"><i class="bi bi-person-plus"></i></div>
            <h3 class="h6 fw-bold">No employees yet</h3>
            <p class="muted">Add your first employee to get started.</p>
            <a href="<?= escape(url('modules/employees/create.php')) ?>" class="btn btn-primary btn-sm">Add employee</a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Email</th>
                        <th>Salary</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent as $employee): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <span class="avatar"><?= escape(strtoupper(substr((string)($employee['name'] ?? 'E'), 0, 1))) ?></span>
                                    <span class="fw-semibold"><?= escape($employee['name'] ?? '') ?></span>
                                </div>
                            </td>
                            <td class="muted"><?= escape($employee['email'] ?? '') ?></td>
                            <td class="fw-semibold">₹<?= number_format((float)($employee['salary'] ?? 0), 2) ?></td>
                            <td class="text-end">
                                <a href="<?= escape(url('modules/employees/edit.php?id=' . urlencode((string)($employee['id'] ?? '')))) ?>" class="btn btn-sm btn-light border">Edit</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
-->

<?php require __DIR__ . '/../../includes/footer.php'; ?>
