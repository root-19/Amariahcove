<?php
// Secure image streamer for admin gallery
// Usage: image.php?p=<filename>

// Resolve storage directory (admin/gallery only)
$storageDir = realpath(__DIR__ . '/../gallery');
if ($storageDir === false) {
    http_response_code(404);
    exit('Not found');
}

$param = isset($_GET['p']) ? (string)$_GET['p'] : '';
if ($param === '') {
    http_response_code(400);
    exit('Missing parameter');
}

// Only allow basename, prevent traversal
$file = basename($param);
$path = realpath($storageDir . DIRECTORY_SEPARATOR . $file);

// Ensure file exists and is within storage directory
if ($path === false || strpos($path, $storageDir) !== 0 || !is_file($path)) {
    http_response_code(404);
    exit('Not found');
}

// Detect mime type
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = $finfo ? finfo_file($finfo, $path) : 'application/octet-stream';
if ($finfo) finfo_close($finfo);

// Serve file with caching headers
header('Content-Type: ' . $mime);
header('Content-Length: ' . filesize($path));
header('Cache-Control: public, max-age=31536000, immutable');
readfile($path);
exit;
?>

