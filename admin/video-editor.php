<?php
session_start();
if(!isset($_SESSION['usuario'])){ header("Location: login.php"); exit(); }
error_reporting(E_ALL);
ini_set('display_errors', 1);

$dataFile = __DIR__ . '/../data.json';
$data  = file_exists($dataFile) ? json_decode(file_get_contents($dataFile), true) : [];
$video = $data['video_banner'] ?? 'uploads/banner.mp4';

// URL inicial con bust de caché
$videoNoBust = '../' . $video;
$videoBust   = $videoNoBust . '?t=' . time();
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Editor de Video del Banner • MomVision</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
  :root{ --bg:#0b1a2b; --card:#0f1f33; --accent:#35a0ff; --accent2:#35e0c2; --text:#fff; }
  body{margin:0;background:var(--bg);color:var(--text);font-family:Segoe UI,system-ui,-apple-system}
  .wrap{max-width:980px;margin:30px auto;padding:0 16px}
  h1{margin:0 0 16px;color:var(--accent2)}
  .card{background:var(--card);border:2px solid var(--accent);border-radius:12px;padding:18px}
  video{width:100%;height:60vh;object-fit:cover;display:block;background:#000;border-radius:10px;border:2px solid var(--accent)}
  .row{display:flex;gap:12px;align-items:center;flex-wrap:wrap;margin-top:12px}
  input[type=file]{background:#0d1a2b;border:1px solid #2c3a4f;color:#fff;padding:10px;border-radius:8px}
  button{background:var(--accent);border:none;color:#00152a;padding:10px 16px;border-radius:8px;font-weight:700;cursor:pointer}
  button:hover{background:#4fc1ff}
  .status{margin-top:10px;opacity:.9}
  .muted{opacity:.7}
  a.link{color:#9ddcff;text-decoration:none}
</style>
</head>
<body>
  <div class="wrap">
    <h1>Editor de Video del Banner</h1>

    <div class="card">
      <h3>Vista previa actual</h3>
      <video id="bannerVideo" controls muted playsinline preload="metadata" src="<?= htmlspecialchars($videoBust) ?>"></video>

      <div class="row">
        <form id="uploadForm" method="post" action="save-video.php" enctype="multipart/form-data">
          <input id="fileInput" type="file" name="banner_video" accept="video/mp4" required>
          <button id="btnUpload" type="submit">Actualizar Video</button>
          <input type="hidden" name="ajax" value="1">
        </form>
        <button id="btnReload" type="button" title="Recargar vista previa">Forzar recarga</button>
        <a class="link" id="directLink" href="<?= htmlspecialchars($videoNoBust) ?>" target="_blank" rel="noopener">Abrir archivo</a>
      </div>

      <div class="status" id="statusMsg" aria-live="polite" class="muted">Listo.</div>
    </div>

    <p class="muted">Tip: Si no cambia en el sitio público, presiona Ctrl+F5 o abre en incógnito (caché).</p>
    <p><a class="link" href="dashboard.php">⬅ Volver al Panel</a></p>
  </div>

<script>
document.addEventListener('DOMContentLoaded', function(){
  const form  = document.getElementById('uploadForm');
  const file  = document.getElementById('fileInput');
  const btn   = document.getElementById('btnUpload');
  const reloadBtn = document.getElementById('btnReload');
  const msg   = document.getElementById('statusMsg');
  const video = document.getElementById('bannerVideo');
  const direct = document.getElementById('directLink');

  function setMsg(t){ msg.textContent = t; }
  function bust(url){ return url + (url.includes('?') ? '&' : '?') + 't=' + Date.now(); }

  // Verifica que la URL exista con HEAD antes de mostrarla
  function verifyUrl(url){
    return fetch(url, {method:'HEAD', cache:'no-store'}).then(r => {
      if (!r.ok) throw new Error('HTTP ' + r.status);
      return true;
    });
  }

  // Forzar recarga manual de la vista previa
  reloadBtn.addEventListener('click', ()=>{
    const clean = direct.href;              // ../uploads/banner.mp4
    const url = bust(clean);                // …?t=123
    setMsg('Recargando vista previa…');
    verifyUrl(url).then(()=>{
      video.src = url;
      video.load();
      video.muted = true;
      video.play().catch(()=>{});
      setMsg('✅ Vista previa recargada.');
    }).catch(()=> setMsg('❌ No se pudo cargar el archivo directo.'));
  });

  // Subida por AJAX con progreso del navegador (simple)
  form.addEventListener('submit', function(e){
    e.preventDefault();
    if (!file.files.length){ setMsg('Selecciona un MP4.'); return; }

    btn.disabled = true;
    setMsg('Subiendo…');

    const fd = new FormData(form);
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'save-video.php', true);
    xhr.setRequestHeader('X-Requested-With','XMLHttpRequest');

    xhr.onload = function(){
      btn.disabled = false;
      if (xhr.status !== 200){ setMsg('❌ Error HTTP ' + xhr.status); return; }

      let res = null;
      try{ res = JSON.parse(xhr.responseText); }catch(e){}
      if (!res || !res.ok || !res.video){ setMsg(res && res.message ? res.message : '❌ Error al subir.'); return; }

      // Actualiza link directo y vista previa con bust de caché
      const clean = '../' + res.video;      // ../uploads/banner.mp4
      direct.href = clean;
      const url = bust(clean);

      setMsg('Verificando archivo subido…');
      verifyUrl(url).then(()=>{
        video.src = url;
        video.load();
        video.muted = true;
        video.play().catch(()=>{});
        setMsg('✅ Video actualizado y vista previa lista.');
      }).catch(()=>{
        setMsg('❌ Subido, pero no accesible aún. Intenta “Forzar recarga”.');
      });
    };

    xhr.onerror = function(){ btn.disabled = false; setMsg('❌ Error de red.'); };
    xhr.send(fd);
  });
});
</script>
</body>
</html>
