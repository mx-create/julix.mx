<?php
declare(strict_types=1);

require __DIR__ . '/auth.php';
pk_start_session();

if (!pk_is_unlocked()) {
    pk_deny_locked();
}

// Whitelist only — the key never maps to a user-supplied path.
$files = [
    'presskit' => [
        'path' => __DIR__ . '/protected/Julix_Press_Kit_2026-27.pdf',
        'mime' => 'application/pdf',
        'name' => 'Julix_Press_Kit_2026-27.pdf',
        'disposition' => 'inline',
    ],
    'rider' => [
        'path' => __DIR__ . '/protected/Julix_Technical_and_Hospitality_Rider.pdf',
        'mime' => 'application/pdf',
        'name' => 'Julix_Technical_and_Hospitality_Rider.pdf',
        'disposition' => 'inline',
    ],
    'logopack' => [
        'path' => __DIR__ . '/protected/Julix-Mx_logo_pack.zip',
        'mime' => 'application/zip',
        'name' => 'Julix-Mx_logo_pack.zip',
        'disposition' => 'attachment',
    ],
];

$key = $_GET['key'] ?? '';
if (!is_string($key) || !isset($files[$key])) {
    http_response_code(404);
    header('Content-Type: text/plain; charset=utf-8');
    exit('Not found');
}

$file = $files[$key];
if (!is_file($file['path'])) {
    http_response_code(404);
    header('Content-Type: text/plain; charset=utf-8');
    exit('Not found');
}

header('Content-Type: ' . $file['mime']);
header('Content-Disposition: ' . $file['disposition'] . '; filename="' . $file['name'] . '"');
header('Content-Length: ' . (string) filesize($file['path']));
header('X-Content-Type-Options: nosniff');
header('Cache-Control: private, max-age=0, no-cache');

readfile($file['path']);
exit;
