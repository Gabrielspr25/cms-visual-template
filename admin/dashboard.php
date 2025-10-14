<?php
session_start();
if (empty($_SESSION['usuario'])) { header('Location: login.php'); exit; }

$dataFile = __DIR__ . '/../data.json';
$data = [];
if (file_exists($dataFile)) {
    $content = file_get_contents($dataFile);
    if ($content) {
        $data = json_decode($content, true);
        if (!is_array($data)) $data = [];
    }
}

$brand = isset($data['brand']) ? $data['brand'] : ['name' => 'MomVision', 'logo' => ''];
$video = isset($data['video_banner']) ? $data['video_banner'] : '';
$secciones = isset($data['secciones']) ? $data['secciones'] : [];
$socials = isset($data['socials']) ? $data['socials'] : [];

function h($s) { 
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); 
}

// Contar archivos de manera más segura
$uploadsDir = __DIR__ . '/../uploads';
$fileCount = 0;
if (is_dir($uploadsDir)) {
    $files = scandir($uploadsDir);
    if ($files) {
        $fileCount = count($files) - 2; // Restar . y ..
        if ($fileCount < 0) $fileCount = 0;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Panel MomVision - Dashboard</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<style>
:root {
  --bg-primary: #1a1f2e;
  --bg-secondary: #2d3748;
  --bg-card: #4a5568;
  --border: #718096;
  --text-primary: #ffffff;
  --text-secondary: #cbd5e1;
  --accent: #3182ce;
  --success: #38a169;
  --danger: #e53e3e;
}
* { box-sizing: border-box; }
body { 
  margin: 0; 
  font-family: 'Segoe UI', system-ui, sans-serif; 
  background: var(--bg-primary); 
  color: var(--text-primary); 
}

.sidebar {
  position: fixed; left: 0; top: 0; width: 260px; height: 100vh;
  background: var(--bg-secondary); 
  display: flex; flex-direction: column; padding: 20px;
  border-right: 1px solid var(--border);
}
.logo { 
  display: flex; align-items: center; gap: 12px; 
  margin-bottom: 30px; padding-bottom: 20px; 
  border-bottom: 1px solid var(--border); 
}
.logo img { height: 40px; border-radius: 6px; }
.logo h3 { margin: 0; color: var(--text-primary); }
.nav-menu { flex: 1; }
.nav-item { 
  display: flex; align-items: center; gap: 12px; padding: 12px 16px; 
  color: var(--text-secondary); text-decoration: none; border-radius: 8px; 
  margin-bottom: 4px; transition: all 0.2s; 
}
.nav-item:hover, .nav-item.active { 
  background: var(--accent); color: white; 
}
.nav-item i { width: 20px; }

.main { margin-left: 260px; min-height: 100vh; }
.header { 
  background: var(--bg-secondary); 
  padding: 20px 30px; 
  border-bottom: 1px solid var(--border); 
  display: flex; justify-content: space-between; align-items: center; 
}
.header h1 { margin: 0; font-size: 24px; }
.content { padding: 30px; }

.section-card { 
  background: var(--bg-secondary); 
  border: 1px solid var(--border); 
  border-radius: 12px; 
  margin-bottom: 24px; 
}
.section-header { 
  padding: 20px; 
  border-bottom: 1px solid var(--border); 
  display: flex; justify-content: space-between; align-items: center; 
}
.section-body { padding: 20px; }

.stats-grid { 
  display: grid; 
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); 
  gap: 20px; 
  margin-bottom: 30px; 
}
.stat-card { 
  background: var(--bg-secondary); 
  border: 1px solid var(--border); 
  border-radius: 10px; 
  padding: 20px; 
  text-align: center; 
}
.stat-value { 
  font-size: 32px; 
  font-weight: bold; 
  color: var(--accent); 
}
.stat-label { 
  color: var(--text-secondary); 
  margin-top: 5px; 
}

.btn { 
  padding: 10px 16px; 
  border: none; 
  border-radius: 6px; 
  cursor: pointer; 
  font-weight: 500; 
  transition: all 0.2s; 
  text-decoration: none; 
  display: inline-flex; 
  align-items: center; 
  gap: 8px; 
}
.btn-primary { background: var(--accent); color: white; }
.btn-success { background: var(--success); color: white; }
.btn-danger { background: var(--danger); color: white; }
.btn-sm { padding: 6px 12px; font-size: 14px; }

.collection-grid { 
  display: grid; 
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); 
  gap: 20px; 
}
.product-card { 
  background: var(--bg-primary); 
  border: 1px solid var(--border); 
  border-radius: 10px; 
  overflow: hidden; 
}
.product-body { padding: 16px; }
.product-title { 
  margin: 0 0 8px 0; 
  font-size: 16px; 
  font-weight: 600; 
}
.product-summary { 
  margin: 0 0 12px 0; 
  color: var(--text-secondary); 
  font-size: 14px; 
}
.product-actions { display: flex; gap: 8px; }

@media (max-width: 768px) {
  .sidebar { width: 100%; height: auto; position: relative; }
  .main { margin-left: 0; }
}
</style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
  <div class="logo">
    <?php if (!empty($brand['logo'])): ?>
      <img src="../<?php echo h($brand['logo']); ?>" alt="Logo">
    <?php endif; ?>
    <h3><?php echo h($brand['name']); ?></h3>
  </div>
  
  <div class="nav-menu">
    <a href="dashboard.php" class="nav-item active">
      <i class="fas fa-home"></i>
      Dashboard
    </a>
    <a href="constructor.php" class="nav-item">
      <i class="fas fa-magic"></i>
      Constructor
    </a>
    <a href="mensajes.php" class="nav-item">
      <i class="fas fa-envelope"></i>
      Mensajes
    </a>
    <a href="galeria.php" class="nav-item">
      <i class="fas fa-images"></i>
      Galería
    </a>
    <a href="configuracion.php" class="nav-item">
      <i class="fas fa-cog"></i>
      Configuración
    </a>
  </div>
  
  <a href="logout.php" class="nav-item" style="margin-top: auto; color: var(--danger);">
    <i class="fas fa-sign-out-alt"></i>
    Cerrar Sesión
  </a>
</div>

<!-- MAIN CONTENT -->
<div class="main">
  <div class="header">
    <h1>Panel de Control</h1>
    <div>
      <button class="btn btn-success" onclick="guardarTodo()">
        <i class="fas fa-save"></i>
        Guardar Cambios
      </button>
      <span>Bienvenido, <?php echo h($_SESSION['usuario']); ?></span>
    </div>
  </div>

  <div class="content">
    <!-- STATS -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-value"><?php echo count($secciones); ?></div>
        <div class="stat-label">Secciones</div>
      </div>
      <div class="stat-card">
        <div class="stat-value"><?php echo $fileCount; ?></div>
        <div class="stat-label">Archivos</div>
      </div>
      <div class="stat-card">
        <div class="stat-value"><?php echo count($socials); ?></div>
        <div class="stat-label">Redes Sociales</div>
      </div>
    </div>

    <!-- VIDEO BANNER -->
    <?php if ($video): ?>
    <div class="section-card">
      <div class="section-header">
        <h3><i class="fas fa-video"></i> Video Banner</h3>
        <button class="btn btn-primary" onclick="cambiarVideo()">
          <i class="fas fa-upload"></i>
          Cambiar Video
        </button>
      </div>
      <div class="section-body">
        <video width="100%" height="200" controls style="border-radius: 8px;">
          <source src="../<?php echo h($video); ?>" type="video/mp4">
        </video>
      </div>
    </div>
    <?php endif; ?>

    <!-- SECCIONES -->
    <div class="section-card">
      <div class="section-header">
        <h3><i class="fas fa-list"></i> Secciones del Sitio</h3>
        <button class="btn btn-success" onclick="agregarSeccion()">
          <i class="fas fa-plus"></i>
          Agregar Sección
        </button>
      </div>
      <div class="section-body">
        <?php if (empty($secciones)): ?>
          <p style="text-align: center; color: var(--text-secondary); padding: 40px;">
            <i class="fas fa-info-circle" style="font-size: 32px; margin-bottom: 15px;"></i><br>
            No hay secciones creadas aún.<br>
            <button class="btn btn-success" onclick="window.location.href='constructor.php'" style="margin-top: 15px;">
              <i class="fas fa-magic"></i> Crear Primera Sección
            </button>
          </p>
        <?php else: ?>
          <div class="collection-grid">
            <?php foreach ($secciones as $i => $seccion): ?>
            <div class="product-card">
              <div class="product-body">
                <h4 class="product-title">
                  <?php echo h(isset($seccion['titulo']) ? $seccion['titulo'] : 'Sección ' . ($i+1)); ?>
                </h4>
                <p class="product-summary">
                  Tipo: <?php echo h(isset($seccion['tipo']) ? $seccion['tipo'] : 'texto'); ?>
                </p>
                <div class="product-actions">
                  <button class="btn btn-primary btn-sm" onclick="editarSeccion(<?php echo $i; ?>)">
                    <i class="fas fa-edit"></i> Editar
                  </button>
                  <button class="btn btn-danger btn-sm" onclick="eliminarSeccion(<?php echo $i; ?>)">
                    <i class="fas fa-trash"></i> Eliminar
                  </button>
                </div>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- REDES SOCIALES -->
    <div class="section-card">
      <div class="section-header">
        <h3><i class="fas fa-share-alt"></i> Redes Sociales</h3>
        <button class="btn btn-primary" onclick="agregarRedSocial()">
          <i class="fas fa-plus"></i>
          Agregar Red
        </button>
      </div>
      <div class="section-body">
        <div class="collection-grid">
          <?php foreach($socials as $i => $red): ?>
          <div class="product-card">
            <div class="product-body">
              <h4 class="product-title">
                <?php echo h(isset($red['nombre']) ? $red['nombre'] : ''); ?>
                <?php echo isset($red['icono']) ? $red['icono'] : ''; ?>
              </h4>
              <p class="product-summary">
                <?php echo h(isset($red['url']) ? $red['url'] : ''); ?>
              </p>
              <div class="product-actions">
                <button class="btn btn-primary btn-sm" onclick="editarRedSocial(<?php echo $i; ?>)">
                  <i class="fas fa-edit"></i>
                  Editar
                </button>
                <button class="btn btn-danger btn-sm" onclick="eliminarRedSocial(<?php echo $i; ?>)">
                  <i class="fas fa-trash"></i>
                  Eliminar
                </button>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
          
          <div class="product-card" style="display: flex; align-items: center; justify-content: center; min-height: 140px; border: 2px dashed var(--border); cursor: pointer;" onclick="agregarRedSocial()">
            <div style="text-align: center; color: var(--text-secondary);">
              <i class="fas fa-plus" style="font-size: 24px; margin-bottom: 10px;"></i>
              <div>Agregar Red Social</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ACCESO RÁPIDO -->
    <div class="section-card">
      <div class="section-header">
        <h3><i class="fas fa-rocket"></i> Acceso Rápido</h3>
      </div>
      <div class="section-body">
        <div style="display: flex; gap: 15px; flex-wrap: wrap;">
          <a href="constructor.php" class="btn btn-primary">
            <i class="fas fa-magic"></i> Constructor
          </a>
          <a href="mensajes.php" class="btn btn-primary">
            <i class="fas fa-envelope"></i> Mensajes
          </a>
          <a href="galeria.php" class="btn btn-primary">
            <i class="fas fa-images"></i> Galería
          </a>
          <a href="configuracion.php" class="btn btn-primary">
            <i class="fas fa-cog"></i> Configuración
          </a>
        </div>
      </div>
    </div>

    <!-- INFORMACIÓN DEL SISTEMA -->
    <div class="section-card">
      <div class="section-header">
        <h3><i class="fas fa-info-circle"></i> Estado del Sistema</h3>
      </div>
      <div class="section-body">
        <p><strong>✅ Dashboard funcionando correctamente</strong></p>
        <p>📅 Cargado: <?php echo date('Y-m-d H:i:s'); ?></p>
        <p>👤 Usuario: <?php echo h($_SESSION['usuario']); ?></p>
        <p>📊 Datos: <?php echo file_exists($dataFile) ? 'Disponibles' : 'No encontrados'; ?></p>
      </div>
    </div>
  </div>
</div>

<script>
function agregarSeccion() {
  window.location.href = 'constructor.php';
}

function editarSeccion(index) {
  // Redirigir al constructor con el índice de la sección
  window.location.href = `constructor.php?edit=${index}`;
}

function eliminarSeccion(index) {
  if (confirm('¿Eliminar esta sección? Esta acción no se puede deshacer.')) {
    // Enviar petición para eliminar
    fetch('delete-section.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ index: index })
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        alert('✅ Sección eliminada correctamente');
        location.reload();
      } else {
        alert('❌ Error: ' + data.error);
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('❌ Error de conexión');
    });
  }
}

function agregarRedSocial() {
  window.location.href = 'configuracion.php';
}

function editarRedSocial(index) {
  window.location.href = 'configuracion.php';
}

function eliminarRedSocial(index) {
  if (confirm('¿Eliminar esta red social?')) {
    alert('Función de eliminar en desarrollo.');
  }
}

function cambiarVideo() {
  alert('Función de cambiar video en desarrollo.');
}

function guardarTodo() {
  alert('✅ Los cambios se guardan automáticamente en cada sección.');
}

// Mostrar mensaje de éxito
setTimeout(function() {
  const notification = document.createElement('div');
  notification.innerHTML = '✅ Dashboard cargado correctamente';
  notification.style.cssText = 'position: fixed; top: 20px; right: 20px; background: var(--success); color: white; padding: 12px 20px; border-radius: 8px; z-index: 1000; font-weight: 500;';
  document.body.appendChild(notification);
  
  setTimeout(() => notification.remove(), 3000);
}, 500);

console.log('📊 Dashboard optimizado y funcionando');
</script>

</body>
</html>