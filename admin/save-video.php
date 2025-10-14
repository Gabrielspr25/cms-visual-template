<?php
// ========================================
// MOMVISION • ADMIN • GUARDAR VIDEO BANNER (sin tocar el front)
// Sube y sobreescribe /uploads/banner.mp4 para que el sitio lo tome solo.
// ========================================
session_start();
if (!isset($_SESSION['usuario'])) { http_response_code(403); exit('Acceso denegado'); }

error_reporting(E_ALL);
ini_set('display_errors', 1);

$dataFile  = __DIR__ . '/../data.json';
$uploadDir = __DIR__ . '/../uploads/';
$finalName = 'banner.mp4';                // nombre fijo que usa el front
$finalPath = $uploadDir . $finalName;

if (!is_dir($uploadDir)) { @mkdir($uploadDir, 0777, true); }
if (!is_writable($uploadDir)) { @chmod($uploadDir, 0777); }

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_FILES['banner_video'])) {
  header('Location: video-editor.php'); exit;
}

// Validación básica
$err = $_FILES['banner_video']['error'];
if ($err !== UPLOAD_ERR_OK) {
  $map = [
    UPLOAD_ERR_INI_SIZE   => 'El archivo excede upload_max_filesize.',
    UPLOAD_ERR_FORM_SIZE  => 'El archivo excede MAX_FILE_SIZE del formulario.',
    UPLOAD_ERR_PARTIAL    => 'Archivo subido parcialmente.',
    UPLOAD_ERR_NO_FILE    => 'No se subió ningún archivo.',
    UPLOAD_ERR_NO_TMP_DIR => 'Falta carpeta temporal del servidor.',
    UPLOAD_ERR_CANT_WRITE => 'No se pudo escribir en disco.',
    UPLOAD_ERR_EXTENSION  => 'Extensión bloqueada por PHP.'
  ];
  $msg = $map[$err] ?? ('Error desconocido: ' . $err);
  exit('❌ ' . $msg);
}

// Solo MP4 para asegurar compatibilidad con el front
$original = $_FILES['banner_video']['name'];
$ext      = strtolower(pathinfo($original, PATHINFO_EXTENSION));
if ($ext !== 'mp4') { exit('❌ Solo se permite MP4.'); }

// Subir a un temporal y luego reemplazar el banner
$tmpPath = $uploadDir . ('tmp_' . uniqid() . '.mp4');
if (!move_uploaded_file($_FILES['banner_video']['tmp_name'], $tmpPath)) {
  exit('❌ No se pudo mover el archivo al destino final.');
}

// Reemplazar el archivo final
@unlink($finalPath);
if (!@rename($tmpPath, $finalPath)) {
  // fallback si el rename falla
  if (!@copy($tmpPath, $finalPath)) {
    @unlink($tmpPath);
    exit('❌ No se pudo reemplazar el banner.');
  }
  @unlink($tmpPath);
}

// Actualizar JSON (opcional pero útil)
$payload = [];
if (file_exists($dataFile)) {
  $raw = file_get_contents($dataFile);
  $payload = json_decode($raw, true);
  if (!is_array($payload)) $payload = [];
}
$payload['video_banner']     = 'uploads/' . $finalName;
$payload['video_updated_at'] = date('c');
file_put_contents($dataFile, json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

// Volver con OK
if (
  (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') ||
  (isset($_POST['ajax']) && $_POST['ajax'] === '1')
) {
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode(['ok'=>true,'video'=>'uploads/'.$finalName], JSON_UNESCAPED_SLASHES);
  exit;
}
header('Location: video-editor.php?ok=video');
exit;
