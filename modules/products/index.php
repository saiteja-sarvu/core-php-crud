<?php
require_once __DIR__ . '/../../includes/auth.php';
require_login();

$products = read_products();
if ($products === null) {
    $products = [];
    set_flash('danger', 'Unable to read product records. Check storage/products.json.');
}
usort($products, fn($a, $b) => (int)($a['id'] ?? 0) <=> (int)($b['id'] ?? 0));

$pageTitle = 'Product Management';
$activeMenu = 'products';
require __DIR__ . '/../../includes/header.php';
?>

<div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
        <h1 class="h2 page-title mb-1">Product Management</h1>
        <p class="muted mb-0">View and manage all products in your inventory.</p>
    </div>
    <button type="button" class="btn btn-primary" id="addProductBtn">
        <i class="bi bi-plus-lg me-2"></i>Add Product
    </button>
</div>

<div class="surface overflow-hidden">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 p-4 border-bottom">
        <div>
            <h2 class="h5 fw-bold mb-1">Product listing</h2>
            <p class="muted small mb-0"><?= count($products) ?> product record(s)</p>
        </div>
        <div class="input-group search-box">
            <span class="input-group-text bg-white"><i class="bi bi-search muted"></i></span>
            <input type="search" class="form-control border-start-0" id="productSearch" placeholder="Search products..." aria-label="Search products">
        </div>
    </div>

    <?php if (!$products): ?>
        <div class="text-center py-5">
            <div class="fs-1 text-secondary mb-2"><i class="bi bi-box-seam"></i></div>
            <h3 class="h6 fw-bold">No products yet</h3>
            <p class="muted mb-0">Click "Add Product" to get started.</p>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table align-middle mb-0" id="productsTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Product Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Description</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td class="muted">#<?= escape($product['id'] ?? '') ?></td>
                            <td class="fw-semibold"><?= escape($product['name'] ?? '') ?></td>
                            <td><?= escape($product['category'] ?? '') ?></td>
                            <td class="fw-semibold">₹<?= number_format((float)($product['price'] ?? 0), 2) ?></td>
                            <td><?= escape($product['quantity'] ?? 0) ?></td>
                            <td class="muted"><?= escape($product['description'] ?? '') ?></td>
                            <td class="text-end text-nowrap">
                                <button type="button" class="btn btn-sm btn-light border edit-product-btn"
                                        data-product="<?= escape(json_encode($product)) ?>">
                                    <i class="bi bi-pencil me-1"></i>Edit
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger"
                                        data-bs-toggle="modal" data-bs-target="#deleteModal"
                                        data-id="<?= escape($product['id'] ?? '') ?>"
                                        data-name="<?= escape($product['name'] ?? '') ?>">
                                    <i class="bi bi-trash me-1"></i>Delete
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div id="noSearchResults" class="text-center py-4 muted d-none">No matching products found.</div>
        </div>
    <?php endif; ?>
</div>

<div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow modal-rounded">
            <form id="productForm" method="POST" action="<?= escape(url('modules/products/save.php')) ?>" novalidate>
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="productId">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold" id="productModalLabel">Add Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-3">
                    <div class="alert alert-danger d-none" id="productFormAlert"></div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="productName">Product Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="productName" name="name" maxlength="100" placeholder="e.g. Wireless Mouse" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="productCategory">Category <span class="text-danger">*</span></label>
                        <select class="form-select" id="productCategory" name="category" required>
                            <option value="">Select category</option>
                            <?php foreach (product_categories() as $category): ?>
                                <option value="<?= escape($category) ?>"><?= escape($category) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold" for="productPrice">Price (₹) <span class="text-danger">*</span></label>
                            <input type="number" min="0" step="0.01" class="form-control" id="productPrice" name="price" placeholder="0.00" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold" for="productQuantity">Quantity <span class="text-danger">*</span></label>
                            <input type="number" min="0" step="1" class="form-control" id="productQuantity" name="quantity" placeholder="0" required>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="mb-1">
                        <label class="form-label fw-semibold" for="productDescription">Description</label>
                        <textarea class="form-control" id="productDescription" name="description" rows="3" maxlength="500" placeholder="Optional product description"></textarea>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="productSaveBtn">
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
                    <i class="bi bi-exclamation-triangle text-danger me-2"></i>Delete product?
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-3">
                Are you sure you want to delete <strong id="deleteEmployeeName"></strong>? This action cannot be undone.
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="<?= escape(url('modules/products/delete.php')) ?>">
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
