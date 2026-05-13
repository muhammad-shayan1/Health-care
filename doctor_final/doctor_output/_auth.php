<?php
if (session_status() === PHP_SESSION_NONE) {
    $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    session_set_cookie_params([
        'httponly' => true,
        'samesite' => 'Lax',
        'secure' => $secure
    ]);
    session_start();
}
require_once __DIR__ . '../connect.php';
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? null) !== 'doctor') {
    header('Location: ../login.php');
    exit;
}
function doctor_id(): int {
    return intval($_SESSION['user_id']);
}
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}
function check_csrf(): bool {
    return isset($_POST['csrf_token'], $_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
}
?>
