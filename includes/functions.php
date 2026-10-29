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

function read_employees(): ?array
{
    return read_json_file(EMPLOYEE_FILE);
}

function save_employees(array $employees): bool
{
    return write_json_file(EMPLOYEE_FILE, $employees);
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
