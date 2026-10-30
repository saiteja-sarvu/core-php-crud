<?php
require_once __DIR__ . '/../../includes/auth.php';
require_login();

header('Content-Type: application/json');

$products = read_products();
if ($products === null) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Unable to read product records. Check storage/products.json.']);
    exit;
}
usort($products, fn($a, $b) => (int)($a['id'] ?? 0) <=> (int)($b['id'] ?? 0));

echo json_encode(['success' => true, 'products' => array_values($products)]);
