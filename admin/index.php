<?php
// REDIRECCIONAR AL DASHBOARD - Admin index no debe mostrar contenido directamente
session_start();
if (empty($_SESSION['usuario'])) {
    header('Location: login.php');
} else {
    header('Location: dashboard.php');
}
exit;
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Inbox | Panel MomVision</title>
<style>
  :root{color-scheme:dark}
  body{margin:0;font-family:system-ui,-apple-system,Segoe UI,Roboto;background:#0b0f14;color:#e5e7eb}
  header{padding:16px 24px;background:#0f172a;border-bottom:1px solid #1f2937;display:flex;justify-content:space-between;align-items:center}
  .wrap{max-width:1100px;margin:24px auto;padding:0 16px}
  .card{background:#111827;border:1px solid #1f2937;border-radius:16px;padding:16px}
  .row{display:flex;gap:12px;align-items:center;justify-content:space-between;margin-bottom:12px}
  input[type="search"]{width:320px;max-width:100%;padding:10px;border-radius:10px;border:1px solid #374151;background:#0f172a;color:#e5e7eb}
  table{width:100%;border-collapse:collapse}
  th,td{padding:10px;border-bottom:1px solid #1f2937;vertical-align:top}
  th{font-weight:600;color:#cbd5e1;text-align:left}
  .muted{color:#9ca3af}
  .pill{display:inline-block;padding:2px 8px;border-radius:999px;background:#0f172a;border:1px solid #334155;color:#cbd5e1;font-size:12px}
  details{background:#0f172a;border:1px solid #1f2937;border-radius:10px;padding:10px}
  a.link{color:#93c5fd}
</style>
</head>
<body>
<header>
  <div><strong>Panel MomVision</strong> <span class="muted">/ Inbox</span></div>
  <nav>
    <a class="link" href="dashboard.php">Dashboard</a> &nbsp;|&nbsp;
    <a class="link" href="logout.php">Salir</a>
  </nav>
</header>

<div class="wrap">
  <div class="card">
    <div class="row">
      <div><span class="pill"><?=count($mensajes)?></span> <span class="muted">mensajes</span></div>
      <input type="search" id="q" placeholder="Buscar por nombre, email o texto…">
    </div>

    <table>
      <thead>
        <tr>
          <th style="width:160px">Fecha</th>
          <th style="width:220px">Remitente</th>
          <th>Mensaje</th>
          <th style="width:120px">Meta</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($mensajes as $m):
          $fecha = h($m['fecha'] ?? '');
          $nom   = h($m['nombre'] ?? '');
          $em    = h($m['email'] ?? '');
          $ip    = h($m['ip'] ?? '');
          $msg   = trim((string)($m['mensaje'] ?? ''));
          $prev  = mb_substr($msg, 0, 120) . (mb_strlen($msg) > 120 ? '…' : '');
          $search = mb_strtolower("$fecha $nom $em $msg");
        ?>
        <tr data-search="<?=h($search)?>">
          <td class="muted"><?= $fecha ?></td>
          <td>
            <div><strong><?= $nom ?></strong></div>
            <div class="muted"><a class="link" href="mailto:<?= $em ?>"><?= $em ?></a></div>
          </td>
          <td>
            <details>
              <summary><?= h($prev) ?></summary>
              <div style="margin-top:8px;white-space:pre-wrap;line-height:1.4"><?= nl2br(h($msg)) ?></div>
            </details>
          </td>
          <td class="muted"><?= $ip ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($mensajes)): ?>
        <tr><td colspan="4" class="muted">Aún no hay mensajes.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<script>
  const q = document.getElementById('q');
  if (q) {
    q.addEventListener('input', () => {
      const term = q.value.trim().toLowerCase();
      document.querySelectorAll('tbody tr').forEach(tr => {
        const text = tr.getAttribute('data-search') || '';
        tr.style.display = text.includes(term) ? '' : 'none';
      });
    });
  }
</script>
</body>
</html>
