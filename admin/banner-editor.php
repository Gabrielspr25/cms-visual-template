<?php
session_start();
if (!isset($_SESSION['usuario'])) {
  header("Location: login.php");
  exit;
}

// Ruta del archivo de configuración
$configFile = __DIR__ . '/../config.json';
$config = ['banner' => '', 'contenido' => ''];
if (file_exists($configFile)) {
  $raw = file_get_contents($configFile);
  $cfg = json_decode($raw, true);
  if (is_array($cfg)) $config = array_merge($config, $cfg);
}

$msg = isset($_GET['msg']) ? htmlspecialchars($_GET['msg']) : '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Editor de Banner y Secciones</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<script src="https://cdn.tiny.cloud/1/h2m74jrm47y6rtd2bn0ut9zeq24hz5g2ydu1492reodzxner/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
tinymce.init({
  selector: '#editor',
  plugins: 'image media link lists table code',
  toolbar: 'undo redo | styles | bold italic | alignleft aligncenter alignright | bullist numlist | link image media | code',
  height: 360,
  language: 'es'
});
</script>
<style>
body{font-family:Arial,Helvetica,sans-serif;background:#f6f8fb;margin:0;padding:20px}
.container{max-width:960px;margin:0 auto;background:#fff;padding:20px;border-radius:10px;box-shadow:0 4px 10px rgba(0,0,0,0.1)}
h1{margin-bottom:10px;font-size:22px}
.field{margin-bottom:14px}
label{font-weight:bold;display:block;margin-bottom:4px}
button{background:#0d6efd;color:#fff;border:none;padding:10px 16px;border-radius:6px;cursor:pointer}
.note{background:#e9f6ef;padding:10px;border-left:4px solid #2e7d32;border-radius:6px;margin-bottom:14px}
.small{font-size:13px;color:#666}
video{max-width:100%;margin-top:12px;border-radius:6px}
.msg{padding:8px;background:#fff3cd;border:1px solid #ffe08a;border-radius:6px;margin-bottom:12px}
</style>
</head>
<body>
<div class="container">
  <h1>🎬 Editor de Banner y Secciones</h1>
  <?php if($msg): ?><div class="msg"><?= $msg ?></div><?php endif; ?>
  <div class="note">Aquí podés subir un nuevo video para el banner principal y editar el texto de presentación.</div>

  <form action="save-banner.php" method="post" enctype="multipart/form-data">
    <div class="field">
      <label>Video del banner (MP4, máx. 100MB)</label>
      <input type="file" name="video" accept="video/*">
      <div class="small">Si no subís un video, el actual se mantiene.</div>
    </div>

    <div class="field">
      <label>Contenido de la sección:</label>
      <textarea id="editor" name="contenido"><?= htmlspecialchars($config['contenido']) ?></textarea>
    </div>

    <button type="submit">💾 Guardar cambios</button>
  </form>

  <hr>
  <h3>Vista previa actual:</h3>
  <?php if(!empty($config['banner'])): ?>
    <video controls muted autoplay loop>
      <source src="../<?= htmlspecialchars($config['banner']) ?>" type="video/mp4">
    </video>
  <?php else: ?>
    <p class="small">No hay video cargado actualmente.</p>
  <?php endif; ?>
</div>
</body>
</html>
