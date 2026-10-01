<?php
declare(strict_types=1);

/**
 * Shared helpers for the press-kit password gate.
 * Included by unlock.php, download.php and drive-link.php.
 */

function pk_start_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }
    $secure = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    session_set_cookie_params([
        'lifetime' => 0,       // expires when the browser is closed
        'path'     => '/',
        'domain'   => '',
        'secure'   => $secure, // true once served over HTTPS
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

/**
 * Loads config.php (not committed to git — see config.example.php).
 * Fails with a clear JSON error instead of a raw PHP fatal if it's
 * missing, which is the most common deploy mistake.
 */
function pk_load_config(): array
{
    $path = __DIR__ . '/config.php';
    if (!is_file($path)) {
        http_response_code(500);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['ok' => false, 'error' => 'server_not_configured']);
        exit;
    }
    $config = require $path;
    return is_array($config) ? $config : [];
}

function pk_is_unlocked(): bool
{
    return !empty($_SESSION['unlocked']);
}

function pk_deny_locked(): void
{
    http_response_code(403);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok' => false, 'error' => 'locked']);
    exit;
}
