<?php
session_start();
if (empty($_SESSION['usuario'])) { header('Location: login.php'); exit; }

$file = __DIR__ . '/mensajes.json';
if (!file_exists($file)) file_put_contents($file, "[]");

$mensajes = json_decode(file_get_contents($file), true);
if (!is_array($mensajes)) $mensajes = [];

usort($mensajes, fn($a,$b)=>strtotime($b['fecha']??'')<=>strtotime($a['fecha']??''));
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Mensajes recibidos - MomVision</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
  :root{color-scheme:dark;}
  body{margin:0;font-family:system-ui,Arial;background:#0b0f14;color:#e5e7eb;}
  header{background:#0f172a;padding:16px 24px;border-bottom:1px solid #1f2937;display:flex;justify-content:space-between;align-items:center}
  a{color:#93c5fd;text-decoration:none}
  .wrap{max-width:1100px;margin:24px auto;padding:0 16px}
  .card{background:#111827;border:1px solid #1f2937;border-radius:14px;padding:18px}
  h1{margin:0 0 12px 0;font-size:22px}
  table{width:100%;border-collapse:collapse;margin-top:10px}
  th,td{padding:10px;border-bottom:1px solid #1f2937;text-align:left;vertical-align:top}
  th{color:#cbd5e1}
  .muted{color:#9ca3af;font-size:13px}
  details{background:#0f172a;border:1px solid #1f2937;border-radius:10px;padding:8px}
  input[type=search]{padding:8px 10px;border-radius:8px;border:1px solid #374151;background:#0f172a;color:#e5e7eb;width:280px}
</style>
</head>
<body>
<header>
  <div><strong>MomVision</strong> <span class="muted">/ Mensajes</span></div>
  <nav><a href="dashboard.php">Volver al Panel</a></nav>
</header>

<div class="wrap">
  <div class="card">
    <h1>📩 Mensajes recibidos (<?= count($mensajes) ?>)</h1>
    <input type="search" id="buscador" placeholder="Buscar...">

    <table>
      <thead>
        <tr><th>Fecha</th><th>Nombre</th><th>Email</th><th>Mensaje</th></tr>
      </thead>
      <tbody>
        <?php foreach($mensajes as $m): ?>
        <tr data-search="<?= strtolower(($m['nombre']??'').($m['email']??'').($m['mensaje']??'')) ?>">
          <td class="muted"><?= htmlspecialchars($m['fecha']??'') ?></td>
          <td><?= htmlspecialchars($m['nombre']??'') ?></td>
          <td><a href="mailto:<?= htmlspecialchars($m['email']??'') ?>"><?= htmlspecialchars($m['email']??'') ?></a></td>
          <td>
            <details>
              <summary><?= htmlspecialchars(substr($m['mensaje']??'',0,50)) ?>...</summary>
              <div style="margin-top:8px;white-space:pre-wrap"><?= nl2br(htmlspecialchars($m['mensaje']??'')) ?></div>
            </details>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<script>
const q = document.getElementById('buscador');
q.addEventListener('input',()=>{
  const val = q.value.toLowerCase();
  document.querySelectorAll('tbody tr').forEach(tr=>{
    tr.style.display = tr.dataset.search.includes(val) ? '' : 'none';
  });
});
</script>
</body>
</html>
