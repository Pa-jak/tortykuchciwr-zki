<?php
require_once __DIR__ . '/db.php';

function e(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function ensure_session(): void {
    if (session_status() === PHP_SESSION_NONE) {
        ini_set('session.cookie_httponly', '1');
        ini_set('session.cookie_samesite', 'Lax');
        if (!empty($_SERVER['HTTPS'])) {
            ini_set('session.cookie_secure', '1');
        }
        session_start();
    }
}

function csrf_token(): string {
    ensure_session();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_input(): string {
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function verify_csrf(): void {
    ensure_session();
    $token = $_POST['csrf_token'] ?? '';
    if (!is_string($token) || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(403);
        exit('Błąd CSRF.');
    }
}

function rate_limit_file(): string {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $safe = preg_replace('/[^a-zA-Z0-9.-]/', '_', $ip);
    return sys_get_temp_dir() . '/tk_login_' . $safe . '.json';
}

function rate_lockout_remaining(): int {
    $file = rate_limit_file();
    if (!file_exists($file)) return 0;
    $data = json_decode(file_get_contents($file), true) ?: [];
    $until = (int)($data['locked_until'] ?? 0);
    return max(0, $until - time());
}

function record_login_attempt(bool $success): void {
    $file = rate_limit_file();
    $data = ['failed' => 0, 'locked_until' => 0];
    if (file_exists($file)) {
        $d = json_decode(file_get_contents($file), true);
        if (is_array($d)) $data = $d;
    }
    if ($success) {
        $data = ['failed' => 0, 'locked_until' => 0];
    } else {
        $data['failed'] = (int)($data['failed'] ?? 0) + 1;
        if ($data['failed'] >= 5) {
            $data['locked_until'] = time() + 900;
        }
    }
    file_put_contents($file, json_encode($data), LOCK_EX);
}

function try_login(string $password): bool {
    ensure_session();
    if (rate_lockout_remaining() > 0) {
        return false;
    }
    if (!defined('ADMIN_PASSWORD')) {
        record_login_attempt(false);
        return false;
    }
    if (hash_equals(ADMIN_PASSWORD, $password)) {
        $_SESSION['admin_logged_in'] = true;
        session_regenerate_id(true);
        record_login_attempt(true);
        return true;
    }
    record_login_attempt(false);
    return false;
}

function is_logged_in(): bool {
    ensure_session();
    return !empty($_SESSION['admin_logged_in']);
}

function require_admin(): void {
    ensure_session();
    if (!is_logged_in()) {
        header('Location: /admin/');
        exit;
    }
}

function logout(): void {
    ensure_session();
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
}
