<?php
declare(strict_types=1);

require __DIR__ . '/auth.php';
pk_start_session();

header('Content-Type: application/json; charset=utf-8');

if (!pk_is_unlocked()) {
    pk_deny_locked();
}

$config = pk_load_config();
$url = (string) ($config['drive_url'] ?? '');

if ($url === '') {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'not_configured']);
    exit;
}

echo json_encode(['ok' => true, 'url' => $url]);
