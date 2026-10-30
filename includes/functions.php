<?php
require_once __DIR__ . '/../config/config.php';

function escape($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function url(string $path = ''): string
{
    return rtrim(BASE_URL, '/') . '/' . ltrim($path, '/');
}

function redirect(string $path): void
{
    header('Location: ' . url($path));
    exit;
}

function set_flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function display_flash(): void
{
    if (empty($_SESSION['flash'])) {
        return;
    }
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    $allowed = ['success', 'danger', 'warning', 'info'];
    $type = in_array($flash['type'] ?? '', $allowed, true) ? $flash['type'] : 'info';

    echo '<div class="alert alert-' . $type . ' alert-dismissible fade show" role="alert">';
    echo escape($flash['message'] ?? '');
    echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
    echo '</div>';
}

function read_json_file(string $path): ?array
{
    if (!is_dir(dirname($path)) && !mkdir(dirname($path), 0775, true) && !is_dir(dirname($path))) {
        return null;
    }

    if (!file_exists($path)) {
        return file_put_contents($path, "[]", LOCK_EX) !== false ? [] : null;
    }

    $contents = file_get_contents($path);
    if ($contents === false) {
        return null;
    }

    $decoded = json_decode($contents, true);
    return is_array($decoded) ? $decoded : null;
}

function write_json_file(string $path, array $data): bool
{
    if (!is_dir(dirname($path)) && !mkdir(dirname($path), 0775, true) && !is_dir(dirname($path))) {
        return false;
    }

    $json = json_encode(array_values($data), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    if ($json === false) {
        return false;
    }

    return file_put_contents($path, $json, LOCK_EX) !== false;
}

function read_products(): ?array
{
    return read_json_file(PRODUCT_FILE);
}

function save_products(array $products): bool
{
    return write_json_file(PRODUCT_FILE, $products);
}

function product_categories(): array
{
    return ['Electronics', 'Clothing', 'Groceries', 'Furniture', 'Books', 'Toys', 'Other'];
}

/** Validates product form input; returns [cleanData, errors]. */
function validate_product(array $input): array
{
    $errors = [];
    $name = trim((string)($input['name'] ?? ''));
    $category = trim((string)($input['category'] ?? ''));
    $price = trim((string)($input['price'] ?? ''));
    $quantity = trim((string)($input['quantity'] ?? ''));
    $description = trim((string)($input['description'] ?? ''));

    if ($name === '') $errors['name'] = 'Product name is required.';
    elseif (mb_strlen($name) > 100) $errors['name'] = 'Product name must be 100 characters or fewer.';

    if ($category === '') $errors['category'] = 'Category is required.';
    elseif (!in_array($category, product_categories(), true)) $errors['category'] = 'Select a valid category.';

    if ($price === '') $errors['price'] = 'Price is required.';
    elseif (!is_numeric($price) || !is_finite((float)$price)) $errors['price'] = 'Enter a valid price.';
    elseif ((float)$price < 0) $errors['price'] = 'Price cannot be negative.';

    if ($quantity === '') $errors['quantity'] = 'Quantity is required.';
    elseif (filter_var($quantity, FILTER_VALIDATE_INT) === false) $errors['quantity'] = 'Quantity must be a whole number.';
    elseif ((int)$quantity < 0) $errors['quantity'] = 'Quantity cannot be negative.';

    if (mb_strlen($description) > 500) $errors['description'] = 'Description must be 500 characters or fewer.';

    return [[
        'name' => $name,
        'category' => $category,
        'price' => (float)$price,
        'quantity' => (int)$quantity,
        'description' => $description,
    ], $errors];
}

function read_users(): ?array
{
    return read_json_file(USER_FILE);
}

function save_users(array $users): bool
{
    return write_json_file(USER_FILE, $users);
}

function current_user(): ?array
{
    return isset($_SESSION['user']) && is_array($_SESSION['user']) ? $_SESSION['user'] : null;
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . escape(csrf_token()) . '">';
}

function verify_csrf(): bool
{
    return isset($_POST['csrf_token'], $_SESSION['csrf_token'])
        && is_string($_POST['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
}
