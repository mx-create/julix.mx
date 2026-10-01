<?php
declare(strict_types=1);

require __DIR__ . '/auth.php';
pk_start_session();

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'method_not_allowed']);
    exit;
}

const PK_MAX_ATTEMPTS   = 5;
const PK_LOCKOUT_SECONDS = 300; // 5 minutes

if (!isset($_SESSION['pk_attempts'])) {
    $_SESSION['pk_attempts'] = 0;
}

// Still inside an active lockout window.
if (!empty($_SESSION['pk_lockout_until']) && $_SESSION['pk_lockout_until'] > time()) {
    http_response_code(429);
    echo json_encode([
        'ok' => false,
        'error' => 'locked',
        'retry_after' => $_SESSION['pk_lockout_until'] - time(),
    ]);
    exit;
}

// Small fixed delay on every attempt to slow down automated guessing.
usleep(700000);

$input = json_decode((string) file_get_contents('php://input'), true);
$password = is_array($input) ? ($input['password'] ?? '') : ($_POST['password'] ?? '');
$password = is_string($password) ? $password : '';

$config = pk_load_config();
$hash = (string) ($config['password_hash'] ?? '');

if ($password !== '' && $hash !== '' && password_verify($password, $hash)) {
    $_SESSION['pk_attempts'] = 0;
    unset($_SESSION['pk_lockout_until']);
    $_SESSION['unlocked'] = true;
    session_regenerate_id(true);
    echo json_encode(['ok' => true]);
    exit;
}

$_SESSION['pk_attempts']++;

if ($_SESSION['pk_attempts'] >= PK_MAX_ATTEMPTS) {
    $_SESSION['pk_lockout_until'] = time() + PK_LOCKOUT_SECONDS;
    http_response_code(429);
    echo json_encode([
        'ok' => false,
        'error' => 'locked',
        'retry_after' => PK_LOCKOUT_SECONDS,
    ]);
    exit;
}

http_response_code(401);
echo json_encode([
    'ok' => false,
    'error' => 'wrong_password',
    'remaining' => PK_MAX_ATTEMPTS - $_SESSION['pk_attempts'],
]);
