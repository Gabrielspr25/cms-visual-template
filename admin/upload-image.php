<?php
// MOMVISION • ADMIN • SUBIR IMAGEN
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Cache-Control: no-store');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  header('Content-Type: text/plain; charset=utf-8');
  echo "OK: /admin/upload-image.php activo";
  exit;
}

if (!isset($_SESSION['usuario'])) {
  http_response_code(403);
  exit('Acceso denegado');
}

$uploadDir = __DIR__ . '/../uploads/';
if (!is_dir($uploadDir)) { @mkdir($uploadDir, 0777, true); }
if (!is_writable($uploadDir)) { @chmod($uploadDir, 0777); }

if (empty($_FILES['image'])) {
  http_response_code(400);
  exit('❌ No se recibió archivo.');
}

if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
  http_response_code(400);
  exit('❌ Error de subida (code: '.$_FILES['image']['error'].').');
}

$allowed = ['jpg','jpeg','png','webp','gif','svg'];
$ext = strtolower(pathinfo($_FILES['image']['name'] ?? 'file', PATHINFO_EXTENSION));
if (!in_array($ext, $allowed)) {
  http_response_code(400);
  exit('❌ Formato no permitido (jpg, jpeg, png, webp, gif, svg).');
}

$name = 'img_'.date('Ymd_His').'_'.mt_rand(1000,9999).'.'.$ext;
$target = $uploadDir.$name;

if (!move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
  http_response_code(500);
  exit('❌ No se pudo guardar.');
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode(['ok'=>true,'path'=>'uploads/'.$name], JSON_UNESCAPED_SLASHES);
