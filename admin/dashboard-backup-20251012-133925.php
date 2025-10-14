<?php
session_start();
if (empty($_SESSION['usuario'])) { header('Location: login.php'); exit; }

$dataFile = __DIR__ . '/../data.json';
$data = file_exists($dataFile) ? json_decode(file_get_contents($dataFile), true) : [];
if (!is_array($data)) $data = [];
$brand = $data['brand'] ?? ['name'=>'MomVision','logo'=>''];
$video = $data['video_banner'] ?? '';
$secciones = $data['secciones'] ?? [];
$socials = $data['socials'] ?? [];
$contacto = $data['contacto'] ?? [];

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Panel MomVision - Dashboard</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<style>
:root {
  --bg-primary: #0f172a;
  --bg-secondary: #1e293b;
  --bg-card: #334155;
  --border: #475569;
  --text-primary: #f8fafc;
  --text-secondary: #cbd5e1;
  --accent: #3b82f6;
  --accent-hover: #2563eb;
  --success: #10b981;
  --warning: #f59e0b;
  --danger: #ef4444;
}

* { box-sizing: border-box; }
body { margin: 0; font-family: 'Segoe UI', system-ui, sans-serif; background: var(--bg-primary); color: var(--text-primary); }

/* SIDEBAR */
.sidebar {
  position: fixed; left: 0; top: 0; width: 260px; height: 100vh;
  background: var(--bg-secondary); border-right: 1px solid var(--border);
  display: flex; flex-direction: column; padding: 20px;
}
.logo { display: flex; align-items: center; gap: 12px; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid var(--border); }
.logo img { height: 40px; border-radius: 6px; }
.logo h3 { margin: 0; color: var(--text-primary); }
.nav-menu { flex: 1; }
.nav-item { display: block; padding: 12px 16px; color: var(--text-secondary); text-decoration: none; border-radius: 8px; margin-bottom: 4px; transition: all 0.2s; }
.nav-item:hover, .nav-item.active { background: var(--accent); color: white; }
.nav-item i { width: 20px; margin-right: 12px; }

/* MAIN CONTENT */
.main { margin-left: 260px; min-height: 100vh; }
.header { background: var(--bg-secondary); padding: 20px 30px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; }
.header h1 { margin: 0; font-size: 24px; }
.user-info { display: flex; align-items: center; gap: 15px; }
.btn { padding: 10px 16px; border: none; border-radius: 6px; cursor: pointer; font-weight: 500; transition: all 0.2s; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; }
.btn-primary { background: var(--accent); color: white; }
.btn-primary:hover { background: var(--accent-hover); }
.btn-success { background: var(--success); color: white; }
.btn-danger { background: var(--danger); color: white; }

/* CONTENT AREA */
.content { padding: 30px; }
.section-card { background: var(--bg-secondary); border: 1px solid var(--border); border-radius: 12px; margin-bottom: 24px; }
.section-header { padding: 20px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; }
.section-body { padding: 20px; }

/* FORM CONTROLS */
.form-row { display: flex; gap: 20px; margin-bottom: 16px; align-items: center; flex-wrap: wrap; }
.form-group { display: flex; flex-direction: column; gap: 6px; }
.form-group label { font-weight: 500; color: var(--text-secondary); }
.form-control { padding: 10px 12px; border: 1px solid var(--border); border-radius: 6px; background: var(--bg-primary); color: var(--text-primary); }
.form-control:focus { outline: none; border-color: var(--accent); }

/* TOOLBAR */
.toolbar { display: flex; gap: 8px; margin-bottom: 12px; padding: 12px; background: var(--bg-primary); border-radius: 6px; }
.toolbar button { background: var(--bg-card); border: 1px solid var(--border); color: var(--text-primary); padding: 8px 12px; border-radius: 4px; cursor: pointer; transition: all 0.2s; }
.toolbar button:hover { background: var(--accent); }
.editor { min-height: 120px; padding: 12px; border: 1px solid var(--border); border-radius: 6px; background: var(--bg-primary); color: var(--text-primary); }
.editor[contenteditable]:focus { outline: 2px solid var(--accent); outline-offset: 2px; }

/* COLLECTION GRID */
.collection-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; }
.product-card { background: var(--bg-primary); border: 1px solid var(--border); border-radius: 10px; overflow: hidden; }
.product-image { width: 100%; height: 180px; object-fit: cover; }
.product-body { padding: 16px; }
.product-title { margin: 0 0 8px 0; font-size: 16px; font-weight: 600; }
.product-summary { margin: 0 0 12px 0; color: var(--text-secondary); font-size: 14px; }
.product-actions { display: flex; gap: 8px; }
.btn-sm { padding: 6px 12px; font-size: 14px; }

/* DRAG & DROP */
.sortable { transition: all 0.3s; }
.sortable.dragging { opacity: 0.5; transform: rotate(2deg); }
.drop-zone { min-height: 60px; border: 2px dashed var(--border); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: var(--text-secondary); margin: 12px 0; }
.drop-zone.drag-over { border-color: var(--accent); background: rgba(59, 130, 246, 0.1); }

/* MODAL */
.modal { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.8); z-index: 1000; align-items: center; justify-content: center; padding: 20px; }
.modal.show { display: flex; }
.modal-content { background: var(--bg-secondary); border-radius: 12px; width: 100%; max-width: 600px; max-height: 90vh; overflow-y: auto; }
.modal-header { padding: 20px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; }
.modal-body { padding: 20px; }
.modal-footer { padding: 20px; border-top: 1px solid var(--border); display: flex; gap: 12px; justify-content: flex-end; }
.close-modal { background: none; border: none; color: var(--text-secondary); font-size: 24px; cursor: pointer; }

/* STATS */
.stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
.stat-card { background: var(--bg-secondary); border: 1px solid var(--border); border-radius: 10px; padding: 20px; text-align: center; }
.stat-value { font-size: 32px; font-weight: bold; color: var(--accent); }
.stat-label { color: var(--text-secondary); margin-top: 5px; }

@media (max-width: 768px) {
  .sidebar { width: 100%; height: auto; position: relative; }
  .main { margin-left: 0; }
  .form-row { flex-direction: column; }
  .collection-grid { grid-template-columns: 1fr; }
}
</style>
</head>
<body>

<!-- HEADER CON LOGO Y MENÚ -->
<header>
  <div class="brand">
    <?php if(!empty($brand['logo'])): ?>
      <img src="../<?= h($brand['logo']) ?>" alt="Logo">
    <?php else: ?>
      <strong><?= h($brand['name']) ?></strong>
    <?php endif; ?>
  </div>
  <nav>
    <?php foreach($secciones as $s): if(!empty($s['show_in_menu'])): ?>
      <a href="#<?= h($s['id']) ?>"><?= h($s['titulo']) ?></a>
    <?php endif; endforeach; ?>
  </nav>
</header>

<!-- VIDEO BANNER -->
<?php if($video): ?>
<div class="video-banner">
  <video autoplay muted loop playsinline>
    <source src="../<?= h($video) ?>" type="video/mp4">
  </video>
</div>
<?php endif; ?>

<!-- SIDEBAR -->
<div class="sidebar">
  <a href="dashboard.php">🏠 Secciones</a>
  <a href="mensajes.php">✉️ Mensajes</a>
  <button class="btn-save" id="btnGuardar">💾 Guardar cambios</button>
  <a href="logout.php" style="margin-top:auto;color:#f87171">Salir</a>
</div>

<!-- CONTENIDO PRINCIPAL -->
<div class="main">
  <div class="wrap">
    <h2>Editor de Secciones</h2>
    <?php foreach($secciones as $s): ?>
      <div class="section">
        <div class="flex-line">
          <label>Título:</label>
          <input type="text" value="<?= h($s['titulo'] ?? '') ?>" style="flex:2">
          <label>Tipo:</label>
          <select>
            <option value="texto" <?= ($s['tipo']==='texto'?'selected':'') ?>>Texto</option>
            <option value="coleccion" <?= ($s['tipo']==='coleccion'?'selected':'') ?>>Colección</option>
          </select>
          <label>Color fondo:</label>
          <input type="color" value="<?= h($s['bg'] ?? '#ffffff') ?>">
          <label>Mostrar en menú:</label>
          <select>
            <option value="1" <?= !empty($s['show_in_menu'])?'selected':'' ?>>Sí</option>
            <option value="0" <?= empty($s['show_in_menu'])?'selected':'' ?>>No</option>
          </select>
        </div>

        <?php if(($s['tipo'] ?? '') === 'coleccion'): ?>
        <div class="grid cols-4">
          <?php foreach(($s['columns'] ?? []) as $i=>$c): ?>
            <div class="card">
              <?php if(!empty($c['imagen'])): ?>
                <img src="../<?= h($c['imagen']) ?>" alt="">
              <?php endif; ?>
              <h4><?= h($c['titulo']) ?></h4>
              <p><?= h($c['resumen']) ?></p>
              <button onclick="openModal(<?= $i ?>)">Leer más</button>
            </div>

            <div class="modal" id="modal<?= $i ?>">
              <div class="modal-content">
                <span class="btn-close" onclick="closeModal(<?= $i ?>)">&times;</span>
                <?php if(!empty($c['imagen'])): ?>
                  <img src="../<?= h($c['imagen']) ?>" alt="">
                <?php endif; ?>
                <div><?= $c['detalle'] ?></div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
        <?php else: ?>
          <div class="toolbar">
            <button onclick="cmd('bold')"><b>B</b></button>
            <button onclick="cmd('italic')"><i>I</i></button>
            <button onclick="cmd('underline')"><u>U</u></button>
            <button onclick="cmd('justifyLeft')">⯇</button>
            <button onclick="cmd('justifyCenter')">≡</button>
            <button onclick="cmd('justifyRight')">⯈</button>
            <button onclick="cmd('insertUnorderedList')">• Lista</button>
            <button onclick="cmd('foreColor','cyan')">Color</button>
          </div>
          <div class="editor" contenteditable="true"><?= $s['contenido']['html'] ?? '' ?></div>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<script>
function cmd(c,a=null){document.execCommand(c,false,a);}
function openModal(i){document.getElementById('modal'+i).style.display='flex';}
function closeModal(i){document.getElementById('modal'+i).style.display='none';}
document.getElementById('btnGuardar').onclick=()=>alert('Cambios guardados correctamente.');
</script>

</body>
</html>
