<?php
require_once __DIR__ . '/../../includes/auth.php';
require_login();

$pageTitle = 'Users';
$activeMenu = 'users';
require __DIR__ . '/../../includes/header.php';
?>

<div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
        <h1 class="h2 page-title mb-1">Users</h1>
        <p class="muted mb-0">Manage who can sign in to this application.</p>
    </div>
    <button type="button" class="btn btn-primary" id="addUserBtn">
        <i class="bi bi-plus-lg me-2"></i>Add user
    </button>
</div>

<div class="surface overflow-hidden">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 p-4 border-bottom">
        <div>
            <h2 class="h5 fw-bold mb-1">User accounts</h2>
            <p class="muted small mb-0"><span id="userCount">0</span> user(s)</p>
        </div>
        <div class="input-group search-box">
            <span class="input-group-text bg-white"><i class="bi bi-search muted"></i></span>
            <input type="search" class="form-control border-start-0" id="employeeSearch" placeholder="Search users..." aria-label="Search users">
        </div>
    </div>

    <div id="usersAlert" class="alert alert-danger d-none m-3"></div>
    <div id="usersLoading" class="text-center py-5 muted">Loading users...</div>
    <div id="usersEmpty" class="text-center py-5 d-none">
        <div class="fs-1 text-secondary mb-2"><i class="bi bi-shield-lock"></i></div>
        <h3 class="h6 fw-bold">No users found</h3>
        <p class="muted mb-0">Click "Add user" to create one.</p>
    </div>
    <div class="table-responsive d-none" id="usersWrap">
        <table class="table align-middle mb-0" id="employeesTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Email address</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody id="usersBody"></tbody>
        </table>
        <div id="noSearchResults" class="text-center py-4 muted d-none">No matching users found.</div>
    </div>
</div>

<div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow modal-rounded">
            <form id="userForm" method="POST" action="<?= escape(url('modules/users/save.php')) ?>" data-list-url="<?= escape(url('modules/users/list.php')) ?>" novalidate>
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="userId">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold" id="userModalLabel">Add user</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-3">
                    <div class="alert alert-danger d-none" id="userFormAlert"></div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="userName">Full name <span class="text-danger">*</span></label>
                        <input class="form-control" id="userName" name="name" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="userEmail">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="userEmail" name="email" autocomplete="off" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="userPassword" id="userPasswordLabel">Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="userPassword" name="password" minlength="8" autocomplete="new-password">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-1">
                        <label class="form-label fw-semibold" for="userPasswordConfirm">Confirm password</label>
                        <input type="password" class="form-control" id="userPasswordConfirm" name="password_confirm" minlength="8" autocomplete="new-password">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="userSaveBtn">
                        <i class="bi bi-check2 me-1"></i>Save
                    </button>
                </div>
            </form>
        </div>
    </div>
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
                <form id="userDeleteForm" method="POST" action="<?= escape(url('modules/users/delete.php')) ?>">
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
