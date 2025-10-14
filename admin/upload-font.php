<?php
// MOMVISION • ADMIN • SUBIR FUENTE
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  header('Content-Type: text/plain; charset=utf-8');
  echo "OK: /admin/upload-font.php activo";
  exit;
}

if (!isset($_SESSION['usuario'])) {
  http_response_code(403);
  exit('Acceso denegado');
}

$dir = __DIR__ . '/../uploads/fonts/';
if (!is_dir($dir)) { @mkdir($dir, 0777, true); }
if (!is_writable($dir)) { @chmod($dir, 0777); }

if (empty($_FILES['font'])) {
  http_response_code(400);
  exit('❌ No se recibió archivo.');
}

$err = $_FILES['font']['error'];
if ($err !== UPLOAD_ERR_OK) {
  http_response_code(400);
  exit('❌ Error de subida (code: '.$err.')');
}

$allowed = ['woff2','woff','ttf','otf'];
$orig = $_FILES['font']['name'] ?? 'font';
$ext  = strtolower(pathinfo($orig, PATHINFO_EXTENSION));
if (!in_array($ext, $allowed)) {
  http_response_code(400);
  exit('❌ Formato no permitido (woff2, woff, ttf, otf).');
}

$name = 'font_' . date('Ymd_His') . '_' . mt_rand(1000,9999) . '.' . $ext;
$target = $dir . $name;

if (!move_uploaded_file($_FILES['font']['tmp_name'], $target)) {
  http_response_code(500);
  exit('❌ No se pudo guardar.');
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode(['ok'=>true,'path'=>'uploads/fonts/'.$name], JSON_UNESCAPED_SLASHES);
