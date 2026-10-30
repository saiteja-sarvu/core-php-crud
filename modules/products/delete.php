<?php
require_once __DIR__ . '/../../includes/auth.php';
require_login();

header('Content-Type: application/json');

function json_response(array $payload, int $status = 200): void
{
    http_response_code($status);
    echo json_encode($payload);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'message' => 'Invalid request.'], 405);
}
if (!verify_csrf()) {
    json_response(['success' => false, 'message' => 'Your session expired. Refresh the page and try again.'], 403);
}
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id || $id < 1) {
    json_response(['success' => false, 'message' => 'Invalid product ID.'], 422);
}
$products = read_products();
if ($products === null) {
    json_response(['success' => false, 'message' => 'Unable to read product records.'], 500);
}
$found = null;
foreach ($products as $index => $product) {
    if ((int)($product['id'] ?? 0) === $id) { $found = $index; break; }
}
if ($found === null) {
    json_response(['success' => false, 'message' => 'Product not found.'], 404);
}
unset($products[$found]);
if (!save_products($products)) {
    json_response(['success' => false, 'message' => 'Could not delete product. Check file permissions.'], 500);
}
json_response(['success' => true, 'message' => 'Product deleted successfully.']);
