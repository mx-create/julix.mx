<?php
/**
 * Example server configuration for the press-kit password gate.
 *
 * Copy this file to config.php (same folder), then replace the two
 * placeholder values below. config.php is listed in .gitignore and
 * must never be committed — upload it directly via Hostinger's
 * File Manager instead. See the deploy notes for exact steps.
 *
 * Generate a real hash for your password with:
 *   php -r "echo password_hash('your-password-here', PASSWORD_DEFAULT), PHP_EOL;"
 */

return [
    'password_hash' => '$2y$10$REPLACE.WITH.A.REAL.BCRYPT.HASH.FROM.PASSWORD_HASH',
    'drive_url'     => 'https://drive.google.com/drive/folders/REPLACE_WITH_FOLDER_ID',
];
