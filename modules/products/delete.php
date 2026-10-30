<?php
require_once __DIR__ . '/../../includes/auth.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    set_flash('warning', 'Invalid request.');
    redirect('modules/products/index.php');
}
if (!verify_csrf()) {
    set_flash('danger', 'Your session expired. Please try again.');
    redirect('modules/products/index.php');
}
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id || $id < 1) {
    set_flash('danger', 'Invalid product ID.');
    redirect('modules/products/index.php');
}
$products = read_products();
if ($products === null) {
    set_flash('danger', 'Unable to read product records.');
    redirect('modules/products/index.php');
}
$found = null;
foreach ($products as $index => $product) {
    if ((int)($product['id'] ?? 0) === $id) { $found = $index; break; }
}
if ($found === null) {
    set_flash('warning', 'Product not found.');
    redirect('modules/products/index.php');
}
unset($products[$found]);
if (save_products($products)) set_flash('success', 'Product deleted successfully.');
else set_flash('danger', 'Could not delete product. Check file permissions.');
redirect('modules/products/index.php');
