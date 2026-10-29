<?php
require_once __DIR__ . '/../../includes/auth.php';
require_login();

$employees = read_employees();
if ($employees === null) {
    $employees = [];
    set_flash('danger', 'Unable to read employee records. Check storage/employees.json.');
}
usort($employees, fn($a, $b) => (int)($a['id'] ?? 0) <=> (int)($b['id'] ?? 0));

$pageTitle = 'Employees';
$activeMenu = 'employees';
require __DIR__ . '/../../includes/header.php';
?>

<div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
        <h1 class="h2 page-title mb-1">Employees</h1>
        <p class="muted mb-0">View and manage everyone in your organisation.</p>
    </div>
    <a href="<?= escape(url('modules/employees/create.php')) ?>" class="btn btn-primary">
        <i class="bi bi-plus-lg me-2"></i>Add employee
    </a>
</div>

<div class="surface overflow-hidden">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 p-4 border-bottom">
        <div>
            <h2 class="h5 fw-bold mb-1">Employee directory</h2>
            <p class="muted small mb-0"><?= count($employees) ?> employee record(s)</p>
        </div>
        <div class="input-group search-box">
            <span class="input-group-text bg-white"><i class="bi bi-search muted"></i></span>
            <input type="search" class="form-control border-start-0" id="employeeSearch" placeholder="Search employees..." aria-label="Search employees">
        </div>
    </div>

    <?php if (!$employees): ?>
        <div class="text-center py-5">
            <div class="fs-1 text-secondary mb-2"><i class="bi bi-person-lines-fill"></i></div>
            <h3 class="h6 fw-bold">Your directory is empty</h3>
            <p class="muted">Add an employee to get started.</p>
            <a href="<?= escape(url('modules/employees/create.php')) ?>" class="btn btn-primary">Add your first employee</a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table align-middle mb-0" id="employeesTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Employee</th>
                        <th>Email address</th>
                        <th>Monthly salary</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($employees as $employee): ?>
                        <tr>
                            <td class="muted">#<?= escape($employee['id'] ?? '') ?></td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <span class="avatar"><?= escape(strtoupper(substr((string)($employee['name'] ?? 'E'), 0, 1))) ?></span>
                                    <span class="fw-semibold"><?= escape($employee['name'] ?? '') ?></span>
                                </div>
                            </td>
                            <td class="muted"><?= escape($employee['email'] ?? '') ?></td>
                            <td class="fw-semibold">₹<?= number_format((float)($employee['salary'] ?? 0), 2) ?></td>
                            <td class="text-end text-nowrap">
                                <a href="<?= escape(url('modules/employees/edit.php?id=' . urlencode((string)($employee['id'] ?? '')))) ?>" class="btn btn-sm btn-light border">
                                    <i class="bi bi-pencil me-1"></i>Edit
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-danger"
                                        data-bs-toggle="modal" data-bs-target="#deleteModal"
                                        data-id="<?= escape($employee['id'] ?? '') ?>"
                                        data-name="<?= escape($employee['name'] ?? '') ?>">
                                    <i class="bi bi-trash me-1"></i>Delete
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div id="noSearchResults" class="text-center py-4 muted d-none">No matching employees found.</div>
        </div>
    <?php endif; ?>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow modal-rounded">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="deleteModalLabel">
                    <i class="bi bi-exclamation-triangle text-danger me-2"></i>Delete employee?
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-3">
                Are you sure you want to delete <strong id="deleteEmployeeName"></strong>? This action cannot be undone.
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="<?= escape(url('modules/employees/delete.php')) ?>">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id" id="deleteEmployeeId">
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-1"></i>Yes, delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
