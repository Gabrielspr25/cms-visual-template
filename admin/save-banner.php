<?php
session_start();
if (!isset($_SESSION['usuario'])) {
  header("Location: login.php");
  exit;
}

ini_set('upload_max_filesize', '100M');
ini_set('post_max_size', '120M');
ini_set('max_execution_time', '300');
ini_set('max_input_time', '300');

$configFile = __DIR__ . '/../config.json';
$config = ['banner' => '', 'contenido' => ''];

if (file_exists($configFile)) {
  $raw = file_get_contents($configFile);
  $cfg = json_decode($raw, true);
  if (is_array($cfg)) $config = array_merge($config, $cfg);
}

// Subida del video
if (!empty($_FILES['video']['name'])) {
  $targetDir = __DIR__ . '/../uploads/';
  if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
  $fileName = time() . '-' . basename($_FILES['video']['name']);
  $targetFile = $targetDir . $fileName;

  if (move_uploaded_file($_FILES['video']['tmp_name'], $targetFile)) {
    $config['banner'] = 'uploads/' . $fileName;
  }
}

// Guardar contenido
if (isset($_POST['contenido'])) {
  $config['contenido'] = $_POST['contenido'];
}

// Guardar JSON actualizado
file_put_contents($configFile, json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

header("Location: banner-editor.php?msg=✅ Cambios guardados correctamente");
exit;
?>
