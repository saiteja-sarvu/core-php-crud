<?php
require_once __DIR__ . '/../../includes/auth.php';
require_login();

$users = read_users();
if ($users === null) {
    $users = [];
    set_flash('danger', 'Unable to read user records. Check storage/users.json.');
}
usort($users, fn($a, $b) => (int)($a['id'] ?? 0) <=> (int)($b['id'] ?? 0));
$currentId = (int)(current_user()['id'] ?? 0);

$pageTitle = 'Users';
$activeMenu = 'users';
require __DIR__ . '/../../includes/header.php';
?>

<div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
        <h1 class="h2 page-title mb-1">Users</h1>
        <p class="muted mb-0">Manage who can sign in to this application.</p>
    </div>
    <a href="<?= escape(url('modules/users/create.php')) ?>" class="btn btn-primary">
        <i class="bi bi-plus-lg me-2"></i>Add user
    </a>
</div>

<div class="surface overflow-hidden">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 p-4 border-bottom">
        <div>
            <h2 class="h5 fw-bold mb-1">User accounts</h2>
            <p class="muted small mb-0"><?= count($users) ?> user(s)</p>
        </div>
        <div class="input-group search-box">
            <span class="input-group-text bg-white"><i class="bi bi-search muted"></i></span>
            <input type="search" class="form-control border-start-0" id="employeeSearch" placeholder="Search users..." aria-label="Search users">
        </div>
    </div>

    <?php if (!$users): ?>
        <div class="text-center py-5">
            <div class="fs-1 text-secondary mb-2"><i class="bi bi-shield-lock"></i></div>
            <h3 class="h6 fw-bold">No users found</h3>
            <a href="<?= escape(url('modules/users/create.php')) ?>" class="btn btn-primary">Add a user</a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table align-middle mb-0" id="employeesTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Email address</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                        <?php $isSelf = (int)($u['id'] ?? 0) === $currentId; ?>
                        <tr>
                            <td class="muted">#<?= escape($u['id'] ?? '') ?></td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <span class="avatar"><?= escape(strtoupper(substr((string)($u['name'] ?? 'U'), 0, 1))) ?></span>
                                    <span class="fw-semibold"><?= escape($u['name'] ?? '') ?></span>
                                    <?php if ($isSelf): ?>
                                        <span class="badge text-bg-light border">You</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="muted"><?= escape($u['email'] ?? '') ?></td>
                            <td class="text-end text-nowrap">
                                <a href="<?= escape(url('modules/users/edit.php?id=' . urlencode((string)($u['id'] ?? '')))) ?>" class="btn btn-sm btn-light border">
                                    <i class="bi bi-pencil me-1"></i>Edit
                                </a>
                                <?php if ($isSelf): ?>
                                    <button type="button" class="btn btn-sm btn-outline-danger" disabled title="You cannot delete your own account">
                                        <i class="bi bi-trash me-1"></i>Delete
                                    </button>
                                <?php else: ?>
                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                            data-bs-toggle="modal" data-bs-target="#deleteModal"
                                            data-id="<?= escape($u['id'] ?? '') ?>"
                                            data-name="<?= escape($u['name'] ?? '') ?>">
                                        <i class="bi bi-trash me-1"></i>Delete
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div id="noSearchResults" class="text-center py-4 muted d-none">No matching users found.</div>
        </div>
    <?php endif; ?>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow modal-rounded">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="deleteModalLabel">
                    <i class="bi bi-exclamation-triangle text-danger me-2"></i>Delete user?
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-3">
                Are you sure you want to delete <strong id="deleteEmployeeName"></strong>?
                They will no longer be able to sign in.
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="<?= escape(url('modules/users/delete.php')) ?>">
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
