<?php
require __DIR__ . '/src/bootstrap.php';
$current = user();
if (!$current) {
    redirect('login.php');
}
$file = repository()->document(intValue($_GET['id'] ?? 0), $current);
if (!$file || !preg_match('/^[a-f0-9]{40}\.(?:pdf|jpg)$/D', $file['stored_file'])) {
    http_response_code(404);
    exit('Документ не найден.');
}
$path = $config['storage'] . '/' . $file['stored_file'];
$handle = is_file($path) ? fopen($path, 'rb') : false;
if (!$handle) {
    http_response_code(404);
    exit('Документ не найден.');
}
session_write_close();
$pdf = str_ends_with($file['stored_file'], '.pdf');
header('Content-Type: ' . ($pdf ? 'application/pdf' : 'image/jpeg'));
header('Content-Length: ' . fstat($handle)['size']);
header('Content-Disposition: attachment; filename="document.' . ($pdf ? 'pdf' : 'jpg') . '"; filename*=UTF-8\'\'' . rawurlencode($file['original_file']));
header('X-Content-Type-Options: nosniff');
header('Cache-Control: private, no-store');
fpassthru($handle);
fclose($handle);
