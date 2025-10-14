<?php
session_start();
if (empty($_SESSION['usuario'])) { header('Location: login.php'); exit; }

$dataFile = __DIR__ . '/../data.json';
$data = file_exists($dataFile) ? json_decode(file_get_contents($dataFile), true) : [];
if (!is_array($data)) $data = [];

$brand = $data['brand'] ?? ['name'=>'MomVision','logo'=>''];
$secciones = $data['secciones'] ?? [];

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

// Verificar si se está editando una sección existente
$editIndex = isset($_GET['edit']) ? intval($_GET['edit']) : null;
$seccionAEditar = null;
$modoEdicion = false;

if ($editIndex !== null && isset($secciones[$editIndex])) {
    $seccionAEditar = $secciones[$editIndex];
    $modoEdicion = true;
}

// Obtener imágenes disponibles
$uploadsDir = __DIR__ . '/../uploads/';
$imagenes = [];
if (is_dir($uploadsDir)) {
    $files = scandir($uploadsDir);
    foreach($files as $file) {
        if (!in_array($file, ['.', '..'])) {
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                $imagenes[] = 'uploads/' . $file;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Constructor Avanzado - MomVision</title>
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
  --accent-hover: #2c5aa0;
  --success: #38a169;
  --warning: #d69e2e;
  --danger: #e53e3e;
}

* { box-sizing: border-box; }
body { 
  margin: 0; 
  font-family: 'Segoe UI', system-ui, sans-serif; 
  background: var(--bg-primary); 
  color: var(--text-primary); 
}

/* SIDEBAR */
.sidebar {
  position: fixed; left: 0; top: 0; width: 260px; height: 100vh;
  background: var(--bg-secondary); 
  display: flex; flex-direction: column; padding: 20px;
  border-right: 1px solid var(--border);
  z-index: 1000;
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
  display: flex; align-items: center; gap: 12px;
  padding: 12px 16px; color: var(--text-secondary); 
  text-decoration: none; border-radius: 8px; 
  margin-bottom: 4px; transition: all 0.2s; 
}
.nav-item:hover, .nav-item.active { 
  background: var(--accent); color: white; 
}
.nav-item i { width: 20px; }

/* MAIN CONTENT */
.main-container {
  margin-left: 260px;
  min-height: 100vh;
}

.header {
  background: var(--bg-secondary);
  padding: 15px 20px;
  border-bottom: 1px solid var(--border);
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.content-grid {
  display: grid;
  grid-template-columns: 280px 1fr 350px;
  height: calc(100vh - 60px);
}

/* BLOCKS LIBRARY */
.blocks-library {
  background: var(--bg-secondary);
  border-right: 1px solid var(--border);
  padding: 20px;
  overflow-y: auto;
}

.library-title {
  font-size: 16px;
  font-weight: 600;
  margin-bottom: 15px;
  color: var(--accent);
}

.quick-panel {
  background: var(--bg-card);
  border-radius: 8px;
  padding: 15px;
  margin-bottom: 20px;
}

.quick-buttons {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 8px;
}

.btn {
  padding: 8px 12px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 500;
  transition: all 0.2s;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  font-size: 12px;
}

.btn-primary { 
  background: var(--accent); 
  color: white; 
}
.btn-primary:hover { 
  background: var(--accent-hover); 
}

.btn-success { 
  background: var(--success); 
  color: white; 
}

.btn-warning {
  background: var(--warning);
  color: white;
}

/* CANVAS */
.canvas {
  background: var(--bg-primary);
  padding: 20px;
  overflow-y: auto;
}

.canvas-title {
  font-size: 18px;
  font-weight: 600;
  margin-bottom: 20px;
}

.section-info {
  display: flex;
  gap: 15px;
  margin-bottom: 20px;
}

.section-info input, .section-info select {
  padding: 8px 12px;
  border: 1px solid var(--border);
  border-radius: 6px;
  background: var(--bg-secondary);
  color: var(--text-primary);
}

.canvas-area {
  min-height: 400px;
  border: 2px dashed var(--border);
  border-radius: 10px;
  padding: 20px;
  position: relative;
}

.canvas-area.has-blocks {
  border-style: solid;
  border-color: var(--accent);
  background: var(--bg-secondary);
}

.drop-placeholder {
  text-align: center;
  color: var(--text-secondary);
  font-size: 18px;
  padding: 60px 20px;
}

/* BLOCKS IN CANVAS */
.canvas-block {
  background: var(--bg-card);
  border: 1px solid var(--border);
  border-radius: 8px;
  margin-bottom: 15px;
  position: relative;
  transition: all 0.2s;
}

.canvas-block:hover {
  border-color: var(--accent);
  box-shadow: 0 4px 12px rgba(49, 130, 206, 0.3);
}

.canvas-block.active {
  border-color: var(--success);
  box-shadow: 0 4px 12px rgba(56, 161, 105, 0.4);
}

.block-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 10px 15px;
  background: var(--bg-secondary);
  border-bottom: 1px solid var(--border);
  border-radius: 8px 8px 0 0;
}

.block-title {
  font-size: 14px;
  font-weight: 500;
  display: flex;
  align-items: center;
  gap: 8px;
}

.block-actions {
  display: flex;
  gap: 5px;
}

.block-btn {
  background: none;
  border: none;
  color: var(--text-secondary);
  cursor: pointer;
  padding: 4px;
  border-radius: 4px;
  transition: all 0.2s;
}

.block-btn:hover {
  background: var(--accent);
  color: white;
}

.block-content {
  padding: 15px;
}

/* PROPERTIES PANEL - MEJORADO */
.properties {
  background: var(--bg-secondary);
  border-left: 1px solid var(--border);
  padding: 20px;
  overflow-y: auto;
}

.properties-title {
  font-size: 16px;
  font-weight: 600;
  margin-bottom: 15px;
  color: var(--success);
}

.form-group {
  margin-bottom: 20px;
}

.form-label {
  display: block;
  font-size: 14px;
  font-weight: 500;
  margin-bottom: 6px;
  color: var(--text-secondary);
}

.form-input {
  width: 100%;
  padding: 10px 12px;
  border: 1px solid var(--border);
  border-radius: 6px;
  background: var(--bg-card);
  color: var(--text-primary);
  font-size: 14px;
}

.form-input:focus {
  outline: none;
  border-color: var(--accent);
}

.form-textarea {
  width: 100%;
  padding: 10px 12px;
  border: 1px solid var(--border);
  border-radius: 6px;
  background: var(--bg-card);
  color: var(--text-primary);
  font-size: 14px;
  resize: vertical;
  min-height: 100px;
}

.form-textarea:focus {
  outline: none;
  border-color: var(--accent);
}

.image-selector {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 10px;
  margin-top: 10px;
}

.image-option {
  position: relative;
  cursor: pointer;
  border: 2px solid var(--border);
  border-radius: 6px;
  overflow: hidden;
  transition: all 0.2s;
}

.image-option:hover {
  border-color: var(--accent);
  transform: scale(1.02);
}

.image-option.selected {
  border-color: var(--success);
  box-shadow: 0 0 10px rgba(56, 161, 105, 0.3);
}

.image-option img {
  width: 100%;
  height: 60px;
  object-fit: cover;
}

.image-upload {
  display: flex;
  align-items: center;
  justify-content: center;
  height: 60px;
  background: var(--bg-primary);
  border: 2px dashed var(--border);
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.2s;
}

.image-upload:hover {
  border-color: var(--accent);
}

/* NOTIFICATION */
.notification {
  position: fixed;
  top: 20px;
  right: 20px;
  background: var(--success);
  color: white;
  padding: 15px 20px;
  border-radius: 8px;
  z-index: 2000;
  box-shadow: 0 4px 12px rgba(0,0,0,0.3);
}

@media (max-width: 768px) {
  .sidebar { width: 100%; height: auto; position: relative; }
  .main-container { margin-left: 0; }
  .content-grid { grid-template-columns: 1fr; }
  .quick-buttons { grid-template-columns: repeat(3, 1fr); }
}
</style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
  <div class="logo">
    <?php if (!empty($brand['logo'])): ?>
      <img src="../<?= h($brand['logo']) ?>" alt="Logo">
    <?php endif; ?>
    <h3><?= h($brand['name']) ?></h3>
  </div>
  
  <div class="nav-menu">
    <a href="dashboard.php" class="nav-item">
      <i class="fas fa-home"></i>
      Dashboard
    </a>
    <a href="constructor.php" class="nav-item active">
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
<div class="main-container">
  <!-- HEADER -->
  <div class="header">
    <h1><i class="fas fa-magic"></i> Constructor Avanzado
      <?php if ($modoEdicion): ?>
        <span style="color: var(--warning); font-size: 16px; margin-left: 10px;">
          (Editando: <?= h($seccionAEditar['titulo'] ?? 'Sección sin título') ?>)
        </span>
      <?php endif; ?>
    </h1>
    <div>
      <button class="btn btn-success" onclick="guardarSeccion()">
        <i class="fas fa-save"></i>
        Guardar
      </button>
      <button class="btn btn-primary" onclick="previsualizarSeccion()">
        <i class="fas fa-eye"></i>
        Preview
      </button>
    </div>
  </div>

  <!-- CONTENT GRID -->
  <div class="content-grid">
    <!-- BLOCKS LIBRARY -->
    <div class="blocks-library">
      <div class="library-title">
        <i class="fas fa-cube"></i> Biblioteca de Bloques
      </div>
      
      <!-- PANEL DE BOTONES RÁPIDOS -->
      <div class="quick-panel">
        <div style="font-size: 14px; font-weight: 500; margin-bottom: 10px; color: var(--accent);">
          <i class="fas fa-bolt"></i> Agregar Rápido
        </div>
        <div class="quick-buttons">
          <button class="btn btn-primary" onclick="agregarBloque('texto')" title="Agregar Texto">
            <i class="fas fa-paragraph"></i> Texto
          </button>
          <button class="btn btn-primary" onclick="agregarBloque('titulo')" title="Agregar Título">
            <i class="fas fa-heading"></i> Título
          </button>
          <button class="btn btn-primary" onclick="agregarBloque('lista')" title="Agregar Lista">
            <i class="fas fa-list"></i> Lista
          </button>
          <button class="btn btn-primary" onclick="agregarBloque('imagen')" title="Agregar Imagen">
            <i class="fas fa-image"></i> Imagen
          </button>
          <button class="btn btn-primary" onclick="agregarBloque('video')" title="Agregar Video">
            <i class="fas fa-video"></i> Video
          </button>
          <button class="btn btn-primary" onclick="agregarBloque('columnas')" title="Agregar Columnas">
            <i class="fas fa-columns"></i> Columnas
          </button>
        </div>
      </div>
      
      <!-- CATEGORÍA CONTENIDO -->
      <div style="margin-bottom: 20px;">
        <div style="font-size: 14px; font-weight: 500; margin-bottom: 10px; color: var(--text-secondary);">📝 Contenido</div>
        
        <div style="display: flex; align-items: center; gap: 10px; padding: 10px; background: var(--bg-primary); border: 1px solid var(--border); border-radius: 6px; margin-bottom: 8px; cursor: pointer; transition: all 0.2s;" onclick="agregarBloque('texto')" onmouseover="this.style.background='var(--accent)'" onmouseout="this.style.background='var(--bg-primary)'">
          <i class="fas fa-paragraph" style="width: 24px; text-align: center; color: var(--accent);"></i>
          <span style="flex: 1;">Texto</span>
          <button style="background: var(--success); border: none; color: white; width: 24px; height: 24px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 12px;" onclick="event.stopPropagation(); agregarBloque('texto')">
            <i class="fas fa-plus"></i>
          </button>
        </div>
        
        <div style="display: flex; align-items: center; gap: 10px; padding: 10px; background: var(--bg-primary); border: 1px solid var(--border); border-radius: 6px; margin-bottom: 8px; cursor: pointer; transition: all 0.2s;" onclick="agregarBloque('titulo')" onmouseover="this.style.background='var(--accent)'" onmouseout="this.style.background='var(--bg-primary)'">
          <i class="fas fa-heading" style="width: 24px; text-align: center; color: var(--accent);"></i>
          <span style="flex: 1;">Título</span>
          <button style="background: var(--success); border: none; color: white; width: 24px; height: 24px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 12px;" onclick="event.stopPropagation(); agregarBloque('titulo')">
            <i class="fas fa-plus"></i>
          </button>
        </div>
        
        <div style="display: flex; align-items: center; gap: 10px; padding: 10px; background: var(--bg-primary); border: 1px solid var(--border); border-radius: 6px; margin-bottom: 8px; cursor: pointer; transition: all 0.2s;" onclick="agregarBloque('lista')" onmouseover="this.style.background='var(--accent)'" onmouseout="this.style.background='var(--bg-primary)'">
          <i class="fas fa-list-ul" style="width: 24px; text-align: center; color: var(--accent);"></i>
          <span style="flex: 1;">Lista</span>
          <button style="background: var(--success); border: none; color: white; width: 24px; height: 24px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 12px;" onclick="event.stopPropagation(); agregarBloque('lista')">
            <i class="fas fa-plus"></i>
          </button>
        </div>
      </div>
      
      <!-- CATEGORÍA MEDIA -->
      <div style="margin-bottom: 20px;">
        <div style="font-size: 14px; font-weight: 500; margin-bottom: 10px; color: var(--text-secondary);">🖼️ Media</div>
        
        <div style="display: flex; align-items: center; gap: 10px; padding: 10px; background: var(--bg-primary); border: 1px solid var(--border); border-radius: 6px; margin-bottom: 8px; cursor: pointer; transition: all 0.2s;" onclick="agregarBloque('imagen')" onmouseover="this.style.background='var(--accent)'" onmouseout="this.style.background='var(--bg-primary)'">
          <i class="fas fa-image" style="width: 24px; text-align: center; color: var(--accent);"></i>
          <span style="flex: 1;">Imagen</span>
          <button style="background: var(--success); border: none; color: white; width: 24px; height: 24px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 12px;" onclick="event.stopPropagation(); agregarBloque('imagen')">
            <i class="fas fa-plus"></i>
          </button>
        </div>
        
        <div style="display: flex; align-items: center; gap: 10px; padding: 10px; background: var(--bg-primary); border: 1px solid var(--border); border-radius: 6px; margin-bottom: 8px; cursor: pointer; transition: all 0.2s;" onclick="agregarBloque('video')" onmouseover="this.style.background='var(--accent)'" onmouseout="this.style.background='var(--bg-primary)'">
          <i class="fas fa-video" style="width: 24px; text-align: center; color: var(--accent);"></i>
          <span style="flex: 1;">Video</span>
          <button style="background: var(--success); border: none; color: white; width: 24px; height: 24px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 12px;" onclick="event.stopPropagation(); agregarBloque('video')">
            <i class="fas fa-plus"></i>
          </button>
        </div>
      </div>
      
      <!-- CATEGORÍA LAYOUT -->
      <div style="margin-bottom: 20px;">
        <div style="font-size: 14px; font-weight: 500; margin-bottom: 10px; color: var(--text-secondary);">🏗️ Layout</div>
        
        <div style="display: flex; align-items: center; gap: 10px; padding: 10px; background: var(--bg-primary); border: 1px solid var(--border); border-radius: 6px; margin-bottom: 8px; cursor: pointer; transition: all 0.2s;" onclick="agregarBloque('columnas')" onmouseover="this.style.background='var(--accent)'" onmouseout="this.style.background='var(--bg-primary)'">
          <i class="fas fa-columns" style="width: 24px; text-align: center; color: var(--accent);"></i>
          <span style="flex: 1;">Columnas</span>
          <button style="background: var(--success); border: none; color: white; width: 24px; height: 24px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 12px;" onclick="event.stopPropagation(); agregarBloque('columnas')">
            <i class="fas fa-plus"></i>
          </button>
        </div>
        
        <div style="display: flex; align-items: center; gap: 10px; padding: 10px; background: var(--bg-primary); border: 1px solid var(--border); border-radius: 6px; margin-bottom: 8px; cursor: pointer; transition: all 0.2s;" onclick="agregarBloque('boton')" onmouseover="this.style.background='var(--accent)'" onmouseout="this.style.background='var(--bg-primary)'">
          <i class="fas fa-hand-pointer" style="width: 24px; text-align: center; color: var(--accent);"></i>
          <span style="flex: 1;">Botón</span>
          <button style="background: var(--success); border: none; color: white; width: 24px; height: 24px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 12px;" onclick="event.stopPropagation(); agregarBloque('boton')">
            <i class="fas fa-plus"></i>
          </button>
        </div>
        
        <div style="display: flex; align-items: center; gap: 10px; padding: 10px; background: var(--bg-primary); border: 1px solid var(--border); border-radius: 6px; margin-bottom: 8px; cursor: pointer; transition: all 0.2s;" onclick="agregarBloque('html')" onmouseover="this.style.background='var(--accent)'" onmouseout="this.style.background='var(--bg-primary)'">
          <i class="fas fa-code" style="width: 24px; text-align: center; color: var(--accent);"></i>
          <span style="flex: 1;">HTML</span>
          <button style="background: var(--success); border: none; color: white; width: 24px; height: 24px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 12px;" onclick="event.stopPropagation(); agregarBloque('html')">
            <i class="fas fa-plus"></i>
          </button>
        </div>
      </div>
      
      <!-- CATEGORÍA ESPECIALES -->
      <div style="margin-bottom: 20px;">
        <div style="font-size: 14px; font-weight: 500; margin-bottom: 10px; color: var(--text-secondary);">⭐ Especiales</div>
        
        <div style="display: flex; align-items: center; gap: 10px; padding: 10px; background: var(--bg-primary); border: 1px solid var(--border); border-radius: 6px; margin-bottom: 8px; cursor: pointer; transition: all 0.2s;" onclick="agregarBloque('coleccion')" onmouseover="this.style.background='var(--accent)'" onmouseout="this.style.background='var(--bg-primary)'">
          <i class="fas fa-th-large" style="width: 24px; text-align: center; color: var(--accent);"></i>
          <span style="flex: 1;">Colección</span>
          <button style="background: var(--success); border: none; color: white; width: 24px; height: 24px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 12px;" onclick="event.stopPropagation(); agregarBloque('coleccion')">
            <i class="fas fa-plus"></i>
          </button>
        </div>
      </div>
      
      <div style="text-align: center; padding: 15px; background: var(--bg-card); border-radius: 8px; margin-bottom: 15px;">
        <div style="font-size: 14px; color: var(--success); font-weight: bold; margin-bottom: 8px;">
          ✅ Constructor Completo
        </div>
        <div style="font-size: 12px; color: var(--text-secondary);">
          Edición completa habilitada<br>
          Biblioteca completa de bloques
        </div>
      </div>

      <div style="text-align: center; padding: 10px;">
        <button class="btn btn-success" onclick="limpiarCanvas()" style="width: 100%; margin-bottom: 10px;">
          <i class="fas fa-trash"></i> Limpiar Todo
        </button>
      </div>
    </div>

    <!-- CANVAS -->
    <div class="canvas">
      <div class="canvas-title">
        <?php if ($modoEdicion): ?>
          Editando Sección: <?= h($seccionAEditar['titulo'] ?? 'Sin título') ?>
          <div style="font-size: 14px; color: var(--text-secondary); margin-top: 5px;">
            Tipo: <?= h($seccionAEditar['tipo'] ?? 'desconocido') ?>
          </div>
        <?php else: ?>
          Construir Nueva Sección
        <?php endif; ?>
      </div>

      <div class="section-info">
        <input type="text" id="tituloSeccion" placeholder="Título de la sección" style="flex: 1;">
        <input type="color" id="colorFondo" value="#ffffff" style="width: 60px;">
        <select id="mostrarMenu">
          <option value="1">Mostrar en menú</option>
          <option value="0">Ocultar del menú</option>
        </select>
      </div>

      <div id="canvas-area" class="canvas-area">
        <div class="drop-placeholder">
          <i class="fas fa-magic" style="font-size: 48px; margin-bottom: 15px; color: var(--accent);"></i>
          <div style="font-size: 18px; font-weight: 500; margin-bottom: 10px;">Construye tu sección</div>
          <div style="font-size: 14px; color: var(--text-secondary); margin-bottom: 15px;">
            🚀 Usa los botones de la izquierda para agregar bloques<br>
            📝 Selecciona un bloque para editarlo en el panel derecho<br>
            ✨ Constructor con edición completa
          </div>
        </div>
      </div>
    </div>

    <!-- PROPERTIES PANEL -->
    <div class="properties">
      <div class="properties-title">
        <i class="fas fa-cog"></i> Editor de Propiedades
      </div>
      
      <div id="no-selection" style="text-align: center; color: var(--text-secondary); margin-top: 40px;">
        <i class="fas fa-mouse-pointer" style="font-size: 32px; margin-bottom: 15px;"></i>
        <div>Selecciona un bloque para editarlo</div>
        <div style="font-size: 12px; margin-top: 10px;">
          Haz clic en cualquier bloque del canvas para ver sus opciones de edición
        </div>
      </div>

      <div id="block-properties" style="display: none;">
        <!-- Se llena dinámicamente con los controles de edición -->
      </div>
    </div>
  </div>
</div>

<input type="file" id="fileUpload" accept="image/*" style="display: none;" onchange="handleImageUpload(event)">

<script>
// Variables globales
let bloques = [];
let contadorId = 0;
let bloqueSeleccionado = null;

// Imágenes disponibles desde PHP
const imagenesDisponibles = <?= json_encode($imagenes) ?>;

// Datos de sección a editar (si aplica)
const modoEdicion = <?= $modoEdicion ? 'true' : 'false' ?>;
const editIndex = <?= $editIndex ?? 'null' ?>;
const seccionExistente = <?= $seccionAEditar ? json_encode($seccionAEditar) : 'null' ?>;

// FUNCIÓN PRINCIPAL PARA AGREGAR BLOQUES
function agregarBloque(tipo) {
  console.log('🚀 Agregando bloque:', tipo);
  
  contadorId++;
  const bloque = {
    id: 'bloque_' + contadorId,
    tipo: tipo,
    contenido: obtenerContenidoPorDefecto(tipo)
  };
  
  bloques.push(bloque);
  actualizarCanvas();
  seleccionarBloque(bloque.id);
  mostrarNotificacion('✅ Bloque "' + tipo + '" agregado');
}

// CONTENIDO POR DEFECTO PARA CADA TIPO DE BLOQUE
function obtenerContenidoPorDefecto(tipo) {
  const defaults = {
    'texto': 'Este es un párrafo de texto de ejemplo. Puedes personalizarlo según tus necesidades.',
    'titulo': 'Título de Ejemplo',
    'lista': ['Primer elemento', 'Segundo elemento', 'Tercer elemento'],
    'imagen': { src: [], alt: 'Galería de imágenes', caption: 'Colección de imágenes' },
    'video': { src: '', caption: 'Video explicativo' },
    'columnas': { columnas: 2, contenido: ['Contenido columna 1', 'Contenido columna 2'] },
    'boton': { texto: 'Hacer clic aquí', url: '#', color: '#3182ce' },
    'html': '<p><strong>Código HTML personalizado</strong></p><p>Puedes agregar cualquier HTML aquí.</p>',
    'coleccion': {
      columns: [
        {
          titulo: 'Elemento 1',
          imagen: '',
          resumen: 'Descripción corta del elemento',
          detalle: 'Descripción detallada del elemento 1'
        },
        {
          titulo: 'Elemento 2', 
          imagen: '',
          resumen: 'Descripción corta del elemento',
          detalle: 'Descripción detallada del elemento 2'
        },
        {
          titulo: 'Elemento 3',
          imagen: '',
          resumen: 'Descripción corta del elemento',
          detalle: 'Descripción detallada del elemento 3'
        },
        {
          titulo: 'Elemento 4',
          imagen: '',
          resumen: 'Descripción corta del elemento',
          detalle: 'Descripción detallada del elemento 4'
        }
      ]
    }
  };
  return defaults[tipo] || 'Contenido por defecto';
}

// ACTUALIZAR EL CANVAS
function actualizarCanvas() {
  const canvas = document.getElementById('canvas-area');
  
  if (bloques.length === 0) {
    canvas.innerHTML = `
      <div class="drop-placeholder">
        <i class="fas fa-magic" style="font-size: 48px; margin-bottom: 15px; color: var(--accent);"></i>
        <div style="font-size: 18px; font-weight: 500; margin-bottom: 10px;">Construye tu sección</div>
        <div style="font-size: 14px; color: var(--text-secondary); margin-bottom: 15px;">
          🚀 Usa los botones de la izquierda para agregar bloques<br>
          📝 Selecciona un bloque para editarlo en el panel derecho<br>
          ✨ Constructor con edición completa
        </div>
      </div>
    `;
    canvas.classList.remove('has-blocks');
    return;
  }
  
  canvas.classList.add('has-blocks');
  let html = '';
  
  bloques.forEach((bloque, index) => {
    html += generarHTMLBloque(bloque, index);
  });
  
  canvas.innerHTML = html;
}

// GENERAR HTML PARA UN BLOQUE
function generarHTMLBloque(bloque, index) {
  const iconos = {
    'texto': 'fa-paragraph',
    'titulo': 'fa-heading',
    'lista': 'fa-list-ul',
    'imagen': 'fa-image',
    'video': 'fa-video',
    'columnas': 'fa-columns',
    'boton': 'fa-hand-pointer',
    'html': 'fa-code',
    'coleccion': 'fa-th-large'
  };
  
  let contenidoHTML = '';
  
  switch(bloque.tipo) {
    case 'texto':
      contenidoHTML = '<p style="margin: 0; line-height: 1.6;">' + bloque.contenido + '</p>';
      break;
      
    case 'titulo':
      contenidoHTML = '<h2 style="margin: 0; color: var(--text-primary);">' + bloque.contenido + '</h2>';
      break;
      
    case 'lista':
      contenidoHTML = '<ul style="margin: 0; padding-left: 20px;">';
      if (Array.isArray(bloque.contenido)) {
        bloque.contenido.forEach(item => {
          contenidoHTML += '<li style="margin-bottom: 5px;">' + item + '</li>';
        });
      }
      contenidoHTML += '</ul>';
      break;
      
    case 'imagen':
      if (bloque.contenido.src && bloque.contenido.src.length > 0) {
        contenidoHTML = `
          <div style="text-align: center;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px;">
              ${bloque.contenido.src.map(src => `
                <img src="../${src}" style="width: 100%; height: 150px; object-fit: cover; border-radius: 6px;" alt="${bloque.contenido.alt}">
              `).join('')}
            </div>
            ${bloque.contenido.caption ? '<div style="font-size: 14px; color: var(--text-secondary); margin-top: 8px;">' + bloque.contenido.caption + '</div>' : ''}
          </div>
        `;
      } else {
        contenidoHTML = `
          <div style="text-align: center; padding: 30px; border: 2px dashed var(--border); border-radius: 8px; background: var(--bg-primary);">
            <i class="fas fa-images" style="font-size: 32px; margin-bottom: 15px; color: var(--accent);"></i>
            <div style="font-weight: 500; margin-bottom: 8px;">Galería de Imágenes</div>
            <div style="font-size: 14px; color: var(--text-secondary);">Selecciona múltiples imágenes</div>
          </div>
        `;
      }
      break;
      
    case 'video':
      if (bloque.contenido.src) {
        contenidoHTML = `
          <div style="text-align: center;">
            <video width="100%" height="200" controls style="border-radius: 8px;">
              <source src="${bloque.contenido.src}" type="video/mp4">
            </video>
            ${bloque.contenido.caption ? '<div style="font-size: 14px; color: var(--text-secondary); margin-top: 8px;">' + bloque.contenido.caption + '</div>' : ''}
          </div>
        `;
      } else {
        contenidoHTML = `
          <div style="text-align: center; padding: 30px; border: 2px dashed var(--border); border-radius: 8px; background: var(--bg-primary);">
            <i class="fas fa-video" style="font-size: 32px; margin-bottom: 15px; color: var(--accent);"></i>
            <div style="font-weight: 500; margin-bottom: 8px;">Agregar video</div>
            <div style="font-size: 14px; color: var(--text-secondary);">Usa el panel derecho para la URL</div>
          </div>
        `;
      }
      break;
      
    case 'columnas':
      contenidoHTML = '<div style="display: flex; gap: 20px;">';
      if (Array.isArray(bloque.contenido.contenido)) {
        bloque.contenido.contenido.forEach(col => {
          contenidoHTML += '<div style="flex: 1; padding: 15px; background: var(--bg-primary); border-radius: 6px; border: 1px solid var(--border);">' + col + '</div>';
        });
      }
      contenidoHTML += '</div>';
      break;
      
    case 'boton':
      contenidoHTML = `
        <div style="text-align: center;">
          <button style="background: ${bloque.contenido.color}; color: white; border: none; padding: 12px 24px; border-radius: 6px; font-size: 16px; cursor: pointer; transition: all 0.2s;">
            ${bloque.contenido.texto}
          </button>
        </div>
      `;
      break;
      
    case 'html':
      contenidoHTML = '<div style="background: var(--bg-primary); padding: 15px; border-radius: 6px; border: 1px solid var(--border);">' + bloque.contenido + '</div>';
      break;
      
    case 'coleccion':
      if (bloque.contenido.columns && bloque.contenido.columns.length > 0) {
        // Siempre usar 4 columnas como máximo para mostrar correctamente en el canvas
        const numElementos = bloque.contenido.columns.length;
        let columnas = 'repeat(auto-fit, minmax(200px, 1fr))';
        
        // Mejores estilos para mostrar como columnas horizontales
        contenidoHTML = `
          <div style="
            display: grid; 
            grid-template-columns: ${columnas}; 
            gap: 12px; 
            max-width: 100%; 
            overflow-x: auto;
            padding: 8px;
            background: var(--bg-primary);
            border-radius: 8px;
            border: 1px solid var(--border);
          ">
        `;
        
        bloque.contenido.columns.forEach((elemento, index) => {
          contenidoHTML += `
            <div style="
              background: var(--bg-secondary); 
              border: 1px solid var(--accent); 
              border-radius: 6px; 
              padding: 12px; 
              min-height: 180px; 
              min-width: 200px;
              display: flex; 
              flex-direction: column;
              position: relative;
              transition: all 0.2s;
            " 
            onmouseover="this.style.transform='scale(1.02)'; this.style.borderColor='var(--success)'; this.querySelector('.delete-btn').style.opacity='1';" 
            onmouseout="this.style.transform='scale(1)'; this.style.borderColor='var(--accent)'; this.querySelector('.delete-btn').style.opacity='0.7';">
              
              <!-- Botón eliminar -->
              <button class="delete-btn" onclick="event.stopPropagation(); eliminarElementoColeccionCanvas(${index})" style="
                position: absolute;
                top: -8px;
                left: -8px;
                background: var(--danger);
                color: white;
                border: none;
                width: 24px;
                height: 24px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 12px;
                cursor: pointer;
                opacity: 0.7;
                transition: all 0.2s;
                box-shadow: 0 2px 6px rgba(0,0,0,0.4);
                z-index: 10;
              " 
              onmouseover="this.style.transform='scale(1.1)'; this.style.opacity='1';" 
              onmouseout="this.style.transform='scale(1)';" 
              title="Eliminar elemento">
                <i class="fas fa-times"></i>
              </button>
              
              <!-- Indicador de índice -->
              <div style="
                position: absolute;
                top: -8px;
                right: -8px;
                background: var(--accent);
                color: white;
                width: 24px;
                height: 24px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 12px;
                font-weight: bold;
                box-shadow: 0 2px 6px rgba(0,0,0,0.3);
              ">${index + 1}</div>
              
              <!-- Título -->
              <h5 style="
                margin: 0 0 10px 0; 
                color: var(--accent); 
                font-size: 14px; 
                font-weight: 600; 
                line-height: 1.2;
                border-bottom: 1px solid var(--border);
                padding-bottom: 6px;
              ">${elemento.titulo || 'Sin título'}</h5>
              
              <!-- Imagen -->
              ${elemento.imagen ? `
                <div style="margin-bottom: 8px; text-align: center;">
                  <img src="../${elemento.imagen}" style="
                    width: 100%; 
                    height: 60px; 
                    object-fit: cover; 
                    border-radius: 4px;
                    border: 1px solid var(--border);
                  " alt="${elemento.titulo}">
                </div>
              ` : `
                <div style="
                  margin-bottom: 8px;
                  height: 60px;
                  background: var(--bg-primary);
                  border: 1px dashed var(--border);
                  border-radius: 4px;
                  display: flex;
                  align-items: center;
                  justify-content: center;
                  color: var(--text-secondary);
                  font-size: 24px;
                ">
                  <i class="fas fa-image"></i>
                </div>
              `}
              
              <!-- Resumen -->
              <p style="
                margin: 0 0 8px 0; 
                font-size: 12px; 
                font-weight: 500; 
                color: var(--text-primary);
                flex-grow: 1;
                line-height: 1.4;
              ">${elemento.resumen || 'Sin descripción'}</p>
              
              <!-- Detalle (truncado) -->
              <p style="
                margin: 0; 
                font-size: 11px; 
                color: var(--text-secondary); 
                line-height: 1.3;
                border-top: 1px solid var(--border);
                padding-top: 6px;
                max-height: 40px;
                overflow: hidden;
              ">${elemento.detalle ? (elemento.detalle.length > 60 ? elemento.detalle.substring(0, 60) + '...' : elemento.detalle) : 'Sin detalle'}</p>
            </div>
          `;
        });
        
        contenidoHTML += '</div>';
        
        // Agregar información de la colección
        let colorInfo = 'var(--text-secondary)';
        let iconoInfo = 'fa-th-large';
        if (numElementos === 1) {
          colorInfo = 'var(--warning)';
          iconoInfo = 'fa-exclamation-triangle';
        } else if (numElementos >= 4) {
          colorInfo = 'var(--success)';
          iconoInfo = 'fa-check-circle';
        }
        
        contenidoHTML += `
          <div style="
            margin-top: 10px;
            padding: 8px 12px;
            background: var(--bg-card);
            border-radius: 4px;
            font-size: 12px;
            color: ${colorInfo};
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
          ">
            <div style="display: flex; align-items: center; gap: 6px;">
              <i class="fas ${iconoInfo}"></i>
              <span>Colección con ${numElementos} elemento${numElementos !== 1 ? 's' : ''}</span>
            </div>
            <div style="display: flex; align-items: center; gap: 4px; color: var(--accent);">
              <i class="fas fa-edit" title="Selecciona para editar"></i>
              <i class="fas fa-times-circle" style="color: var(--danger); font-size: 10px;" title="Eliminar elementos con el botón X rojo"></i>
            </div>
          </div>
        `;
      } else {
        contenidoHTML = `
          <div style="text-align: center; padding: 40px 20px; border: 2px dashed var(--border); border-radius: 8px; background: var(--bg-primary);">
            <i class="fas fa-th-large" style="font-size: 48px; margin-bottom: 15px; color: var(--accent);"></i>
            <div style="font-weight: 600; margin-bottom: 8px; font-size: 16px;">Colección Vacía</div>
            <div style="font-size: 14px; color: var(--text-secondary); margin-bottom: 15px;">Esta colección no tiene elementos aún</div>
            <div style="font-size: 12px; color: var(--success); font-weight: 500;">
              <i class="fas fa-arrow-right"></i> Selecciona este bloque y usa el panel derecho para agregar elementos
            </div>
          </div>
        `;
      }
      break;
      
    default:
      contenidoHTML = '<p>Tipo de bloque: ' + bloque.tipo + '</p>';
  }
  
  const isActive = bloqueSeleccionado === bloque.id ? ' active' : '';
  
  return `
    <div class="canvas-block${isActive}" data-block-id="${bloque.id}" onclick="seleccionarBloque('${bloque.id}')">
      <div class="block-header">
        <div class="block-title">
          <i class="fas ${iconos[bloque.tipo] || 'fa-cube'}"></i>
          ${bloque.tipo.charAt(0).toUpperCase() + bloque.tipo.slice(1)}
        </div>
        <div class="block-actions">
          <button class="block-btn" onclick="event.stopPropagation(); moverBloque(${index}, -1)" title="Subir">
            <i class="fas fa-arrow-up"></i>
          </button>
          <button class="block-btn" onclick="event.stopPropagation(); moverBloque(${index}, 1)" title="Bajar">
            <i class="fas fa-arrow-down"></i>
          </button>
          <button class="block-btn" onclick="event.stopPropagation(); duplicarBloque(${index})" title="Duplicar">
            <i class="fas fa-copy"></i>
          </button>
          <button class="block-btn" onclick="event.stopPropagation(); eliminarBloque(${index})" title="Eliminar" style="color: var(--danger);">
            <i class="fas fa-trash"></i>
          </button>
        </div>
      </div>
      <div class="block-content">
        ${contenidoHTML}
      </div>
    </div>
  `;
}

// SELECCIONAR BLOQUE
function seleccionarBloque(bloqueId) {
  // Remover selección anterior
  document.querySelectorAll('.canvas-block').forEach(el => el.classList.remove('active'));
  
  // Seleccionar nuevo bloque
  const elemento = document.querySelector(`[data-block-id="${bloqueId}"]`);
  if (elemento) {
    elemento.classList.add('active');
    bloqueSeleccionado = bloqueId;
    mostrarPropiedades();
  }
}

// MOSTRAR PROPIEDADES - VERSIÓN COMPLETA
function mostrarPropiedades() {
  const noSelection = document.getElementById('no-selection');
  const blockProperties = document.getElementById('block-properties');
  
  if (!bloqueSeleccionado) {
    noSelection.style.display = 'block';
    blockProperties.style.display = 'none';
    return;
  }
  
  const bloque = bloques.find(b => b.id === bloqueSeleccionado);
  if (!bloque) return;
  
  noSelection.style.display = 'none';
  blockProperties.style.display = 'block';
  
  let propiedadesHTML = '';
  
  switch(bloque.tipo) {
    case 'texto':
      propiedadesHTML = `
        <div class="form-group">
          <label class="form-label">Contenido del texto</label>
          <textarea class="form-textarea" onchange="actualizarPropiedad('contenido', this.value)">${bloque.contenido}</textarea>
        </div>
      `;
      break;
      
    case 'titulo':
      propiedadesHTML = `
        <div class="form-group">
          <label class="form-label">Texto del título</label>
          <input type="text" class="form-input" value="${bloque.contenido}" onchange="actualizarPropiedad('contenido', this.value)">
        </div>
      `;
      break;
      
    case 'lista':
      propiedadesHTML = `
        <div class="form-group">
          <label class="form-label">Elementos de la lista (uno por línea)</label>
          <textarea class="form-textarea" onchange="actualizarPropiedad('contenido', this.value.split('\\n').filter(l => l.trim()))">${Array.isArray(bloque.contenido) ? bloque.contenido.join('\n') : ''}</textarea>
        </div>
      `;
      break;
      
    case 'imagen':
      propiedadesHTML = `
        <div class="form-group">
          <label class="form-label">Seleccionar imágenes (múltiples)</label>
          <div class="image-selector">
            ${imagenesDisponibles.map(img => `
              <div class="image-option ${bloque.contenido.src.includes(img) ? 'selected' : ''}" onclick="toggleImagen('${img}')">
                <img src="../${img}" alt="Imagen">
                <div style="position: absolute; top: 5px; right: 5px; background: ${bloque.contenido.src.includes(img) ? 'var(--success)' : 'rgba(0,0,0,0.5)'}; color: white; width: 20px; height: 20px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px;">
                  ${bloque.contenido.src.includes(img) ? '✓' : '+'}
                </div>
              </div>
            `).join('')}
            <div class="image-upload" onclick="subirNuevaImagen()">
              <i class="fas fa-plus"></i><br>
              <small>Subir</small>
            </div>
          </div>
          <small style="color: var(--text-secondary); font-size: 12px; margin-top: 5px; display: block;">
            Seleccionadas: ${bloque.contenido.src.length} imágenes
          </small>
        </div>
        <div class="form-group">
          <label class="form-label">Descripción de la galería</label>
          <input type="text" class="form-input" value="${bloque.contenido.alt}" onchange="actualizarPropiedadObjeto('alt', this.value)">
        </div>
        <div class="form-group">
          <label class="form-label">Pie de galería</label>
          <input type="text" class="form-input" value="${bloque.contenido.caption}" onchange="actualizarPropiedadObjeto('caption', this.value)">
        </div>
      `;
      break;
      
    case 'video':
      propiedadesHTML = `
        <div class="form-group">
          <label class="form-label">URL del video</label>
          <input type="url" class="form-input" value="${bloque.contenido.src}" onchange="actualizarPropiedadObjeto('src', this.value)" placeholder="https://...">
        </div>
        <div class="form-group">
          <label class="form-label">Descripción del video</label>
          <input type="text" class="form-input" value="${bloque.contenido.caption}" onchange="actualizarPropiedadObjeto('caption', this.value)">
        </div>
      `;
      break;
      
    case 'columnas':
      propiedadesHTML = `
        <div class="form-group">
          <label class="form-label">Contenido Columna 1</label>
          <textarea class="form-textarea" onchange="actualizarColumna(0, this.value)">${bloque.contenido.contenido[0] || ''}</textarea>
        </div>
        <div class="form-group">
          <label class="form-label">Contenido Columna 2</label>
          <textarea class="form-textarea" onchange="actualizarColumna(1, this.value)">${bloque.contenido.contenido[1] || ''}</textarea>
        </div>
      `;
      break;
      
    case 'boton':
      propiedadesHTML = `
        <div class="form-group">
          <label class="form-label">Texto del botón</label>
          <input type="text" class="form-input" value="${bloque.contenido.texto}" onchange="actualizarPropiedadObjeto('texto', this.value)">
        </div>
        <div class="form-group">
          <label class="form-label">URL de destino</label>
          <input type="url" class="form-input" value="${bloque.contenido.url}" onchange="actualizarPropiedadObjeto('url', this.value)" placeholder="https://...">
        </div>
        <div class="form-group">
          <label class="form-label">Color del botón</label>
          <input type="color" class="form-input" value="${bloque.contenido.color}" onchange="actualizarPropiedadObjeto('color', this.value)" style="height: 50px;">
        </div>
      `;
      break;
      
    case 'html':
      propiedadesHTML = `
        <div class="form-group">
          <label class="form-label">Código HTML</label>
          <textarea class="form-textarea" onchange="actualizarPropiedad('contenido', this.value)" style="min-height: 150px; font-family: 'Courier New', monospace;">${bloque.contenido}</textarea>
          <small style="color: var(--text-secondary); font-size: 12px; margin-top: 5px; display: block;">Puedes usar cualquier HTML válido aquí</small>
        </div>
      `;
      break;
      
    case 'coleccion':
      propiedadesHTML = `
        <div style="margin-bottom: 15px;">
          <button class="btn btn-success" onclick="agregarElementoColeccion()" style="width: 100%; margin-bottom: 10px;">
            <i class="fas fa-plus"></i> Agregar Elemento
          </button>
        </div>
      `;
      
      if (bloque.contenido.columns && bloque.contenido.columns.length > 0) {
        // Normalizar elementos para asegurar que tengan todos los campos
        bloque.contenido.columns = bloque.contenido.columns.map((elemento, idx) => ({
          titulo: elemento.titulo || elemento.title || `Elemento ${idx + 1}`,
          imagen: elemento.imagen || elemento.image || '',
          resumen: elemento.resumen || elemento.summary || elemento.descripcion || elemento.description || '',
          detalle: elemento.detalle || elemento.detail || elemento.contenido || elemento.content || ''
        }));
        
        bloque.contenido.columns.forEach((elemento, index) => {
          propiedadesHTML += `
            <div style="background: var(--bg-primary); border: 1px solid var(--border); border-radius: 8px; padding: 15px; margin-bottom: 15px;">
              <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                <strong style="color: var(--accent);">Elemento ${index + 1}</strong>
                <button class="btn" onclick="eliminarElementoColeccion(${index})" style="background: var(--danger); color: white; font-size: 12px; padding: 4px 8px;">
                  <i class="fas fa-trash"></i>
                </button>
              </div>
              
              <div class="form-group">
                <label class="form-label">🏷️ Título del Elemento</label>
                <input type="text" class="form-input" value="${elemento.titulo || 'Sin título'}" onchange="actualizarElementoColeccion(${index}, 'titulo', this.value)" placeholder="Ingresa el título principal...">
                <small style="color: var(--text-secondary); font-size: 12px; margin-top: 4px; display: block;">Este título aparece destacado en la colección</small>
              </div>
              
              <div class="form-group">
                <label class="form-label">Imagen</label>
                <div class="image-selector" style="max-height: 200px; overflow-y: auto;">
                  ${imagenesDisponibles.map(img => `
                    <div class="image-option ${elemento.imagen === img ? 'selected' : ''}" onclick="actualizarElementoColeccion(${index}, 'imagen', '${img}')" style="width: 90px; height: 70px; margin: 4px; position: relative; cursor: pointer;">
                      <img src="../${img}" alt="Imagen" style="width: 100%; height: 100%; object-fit: cover; border-radius: 4px;">
                      <div style="position: absolute; top: 4px; right: 4px; background: ${elemento.imagen === img ? 'var(--success)' : 'rgba(0,0,0,0.7)'}; color: white; width: 18px; height: 18px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: bold;">
                        ${elemento.imagen === img ? '✓' : '+'}
                      </div>
                    </div>
                  `).join('')}
                  <div class="image-upload" onclick="subirNuevaImagen()" style="width: 90px; height: 70px; margin: 4px; display: flex; flex-direction: column; align-items: center; justify-content: center; border: 2px dashed var(--border); border-radius: 4px; cursor: pointer; font-size: 12px; color: var(--text-secondary);">
                    <i class="fas fa-plus" style="margin-bottom: 4px;"></i>
                    <span>Subir</span>
                  </div>
                </div>
                <small style="color: var(--text-secondary); font-size: 12px; margin-top: 8px; display: block;">
                  📌 Selecciona una imagen para este elemento
                </small>
              </div>
              
              <div class="form-group">
                <label class="form-label">📋 Resumen Corto</label>
                <textarea class="form-textarea" onchange="actualizarElementoColeccion(${index}, 'resumen', this.value)" style="min-height: 60px;" placeholder="Escribe una descripción breve que aparezca en la colección...">${elemento.resumen || 'Agrega una descripción breve aqui...'}</textarea>
                <small style="color: var(--text-secondary); font-size: 12px; margin-top: 4px; display: block;">Texto corto que se muestra siempre visible</small>
              </div>
              
              <div class="form-group">
                <label class="form-label">📜 Detalle Completo</label>
                <textarea class="form-textarea" onchange="actualizarElementoColeccion(${index}, 'detalle', this.value)" style="min-height: 100px;" placeholder="Escribe el contenido completo que aparecerá en el modal al hacer clic en 'Leer más'...">${elemento.detalle || 'Agrega el contenido completo para el modal aqui...'}</textarea>
                <small style="color: var(--text-secondary); font-size: 12px; margin-top: 4px; display: block;">Este texto se muestra en el modal cuando el usuario hace clic en "Leer más"</small>
              </div>
            </div>
          `;
        });
      } else {
        propiedadesHTML += `
          <div style="text-align: center; color: var(--text-secondary); padding: 20px; border: 1px dashed var(--border); border-radius: 6px;">
            <i class="fas fa-info-circle" style="margin-bottom: 8px;"></i><br>
            No hay elementos en esta colección.<br>
            <small>Usa el botón "Agregar Elemento" para comenzar.</small>
          </div>
        `;
      }
      break;
  }
  
  blockProperties.innerHTML = `
    <div style="margin-bottom: 20px;">
      <div style="font-size: 14px; font-weight: 500; margin-bottom: 6px; color: var(--text-secondary);">Editando bloque</div>
      <div style="padding: 8px 12px; background: var(--bg-primary); border-radius: 6px; font-weight: 500;">
        ${bloque.tipo.charAt(0).toUpperCase() + bloque.tipo.slice(1)}
      </div>
    </div>
    ${propiedadesHTML}
    <div style="text-align: center; margin-top: 20px;">
      <button class="btn btn-success" onclick="aplicarCambios()">
        <i class="fas fa-check"></i> Aplicar Cambios
      </button>
    </div>
  `;
}

// ACTUALIZAR PROPIEDAD
function actualizarPropiedad(propiedad, valor) {
  const bloque = bloques.find(b => b.id === bloqueSeleccionado);
  if (bloque) {
    bloque[propiedad] = valor;
    actualizarCanvas();
  }
}

// ACTUALIZAR PROPIEDAD DE OBJETO
function actualizarPropiedadObjeto(propiedad, valor) {
  const bloque = bloques.find(b => b.id === bloqueSeleccionado);
  if (bloque && typeof bloque.contenido === 'object') {
    bloque.contenido[propiedad] = valor;
    actualizarCanvas();
  }
}

// ACTUALIZAR COLUMNA
function actualizarColumna(index, valor) {
  const bloque = bloques.find(b => b.id === bloqueSeleccionado);
  if (bloque && bloque.tipo === 'columnas') {
    bloque.contenido.contenido[index] = valor;
    actualizarCanvas();
  }
}

// TOGGLE IMAGEN (PARA MÚLTIPLES SELECCIÓN)
function toggleImagen(src) {
  const bloque = bloques.find(b => b.id === bloqueSeleccionado);
  if (bloque && bloque.tipo === 'imagen') {
    if (!Array.isArray(bloque.contenido.src)) {
      bloque.contenido.src = [];
    }
    
    const index = bloque.contenido.src.indexOf(src);
    if (index > -1) {
      bloque.contenido.src.splice(index, 1);
      mostrarNotificacion('❌ Imagen removida');
    } else {
      bloque.contenido.src.push(src);
      mostrarNotificacion('✅ Imagen agregada');
    }
    
    actualizarCanvas();
    mostrarPropiedades();
  }
}

// SELECCIONAR IMAGEN (MANTENER PARA COMPATIBILIDAD)
function seleccionarImagen(src) {
  toggleImagen(src);
}

// SUBIR NUEVA IMAGEN
function subirNuevaImagen() {
  document.getElementById('fileUpload').click();
}

// MANEJAR SUBIDA DE IMAGEN
function handleImageUpload(event) {
  const file = event.target.files[0];
  if (!file) return;
  
  const formData = new FormData();
  formData.append('files[]', file);
  
  fetch('upload-files.php', {
    method: 'POST',
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      mostrarNotificacion('✅ Imagen subida correctamente');
      location.reload(); // Recargar para mostrar nueva imagen
    } else {
      alert('Error al subir la imagen: ' + data.error);
    }
  });
}

// APLICAR CAMBIOS
function aplicarCambios() {
  actualizarCanvas();
  mostrarNotificacion('✅ Cambios aplicados');
}

// MOVER BLOQUE
function moverBloque(index, direccion) {
  const nuevoIndex = index + direccion;
  if (nuevoIndex >= 0 && nuevoIndex < bloques.length) {
    [bloques[index], bloques[nuevoIndex]] = [bloques[nuevoIndex], bloques[index]];
    actualizarCanvas();
    mostrarNotificacion('📋 Bloque movido');
  }
}

// DUPLICAR BLOQUE
function duplicarBloque(index) {
  const bloque = bloques[index];
  contadorId++;
  const nuevoBloque = {
    ...JSON.parse(JSON.stringify(bloque)),
    id: 'bloque_' + contadorId
  };
  bloques.splice(index + 1, 0, nuevoBloque);
  actualizarCanvas();
  mostrarNotificacion('📋 Bloque duplicado');
}

// ELIMINAR BLOQUE
function eliminarBloque(index) {
  if (confirm('¿Eliminar este bloque?')) {
    const bloque = bloques[index];
    if (bloqueSeleccionado === bloque.id) {
      bloqueSeleccionado = null;
      mostrarPropiedades();
    }
    bloques.splice(index, 1);
    actualizarCanvas();
    mostrarNotificacion('🗑️ Bloque eliminado');
  }
}

// LIMPIAR CANVAS
function limpiarCanvas() {
  if (confirm('¿Limpiar todos los bloques del canvas?')) {
    bloques = [];
    bloqueSeleccionado = null;
    actualizarCanvas();
    mostrarPropiedades();
    mostrarNotificacion('🗑️ Canvas limpiado');
  }
}

// GUARDAR SECCIÓN
function guardarSeccion() {
  const titulo = document.getElementById('tituloSeccion').value;
  
  if (!titulo.trim()) {
    alert('⚠️ Por favor ingresa un título para la sección');
    return;
  }
  
  if (bloques.length === 0) {
    alert('⚠️ Agrega al menos un bloque antes de guardar');
    return;
  }
  
  const datosSeccion = {
    id: titulo.toLowerCase().replace(/\s+/g, '-'),
    titulo: titulo,
    tipo: 'multiformato',
    color_fondo: document.getElementById('colorFondo').value,
    mostrar_menu: document.getElementById('mostrarMenu').value === '1',
    bloques: bloques,
    fecha_creacion: seccionExistente?.fecha_creacion || new Date().toISOString(),
    fecha_modificacion: new Date().toISOString()
  };
  
  // Añadir datos de edición si estamos editando
  if (modoEdicion && editIndex !== null) {
    datosSeccion.editIndex = editIndex;
    datosSeccion.modoEdicion = true;
  }
  
  // Enviar al servidor
  const endpoint = modoEdicion ? 'update-section.php' : 'save-multisection.php';
  fetch(endpoint, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify(datosSeccion)
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      const accion = modoEdicion ? 'actualizada' : 'guardada';
      alert(`✅ Sección "${titulo}" ${accion} correctamente!`);
      mostrarNotificacion(`💾 Sección ${accion}`);
      
      // Preguntar si quiere volver al dashboard
      if (confirm(`¿Quieres volver al dashboard para ver los cambios?`)) {
        window.location.href = 'dashboard.php';
      } else {
        // Solo limpiar si es nueva sección
        if (!modoEdicion) {
          document.getElementById('tituloSeccion').value = '';
          bloques = [];
          bloqueSeleccionado = null;
          actualizarCanvas();
          mostrarPropiedades();
        }
      }
    } else {
      alert('❌ Error al guardar: ' + data.error);
    }
  })
  .catch(error => {
    console.error('Error:', error);
    alert('❌ Error de conexión al guardar');
  });
}

// PREVISUALIZAR SECCIÓN
function previsualizarSeccion() {
  window.open('../index.php', '_blank');
}

// MOSTRAR NOTIFICACIÓN
function mostrarNotificacion(mensaje) {
  const notif = document.createElement('div');
  notif.className = 'notification';
  notif.textContent = mensaje;
  document.body.appendChild(notif);
  
  setTimeout(() => {
    if (notif.parentNode) {
      notif.remove();
    }
  }, 3000);
}

// FUNCIONES PARA MANEJAR COLECCIONES
function agregarElementoColeccion() {
  console.log('🚀 Agregando nuevo elemento a la colección');
  
  const bloque = bloques.find(b => b.id === bloqueSeleccionado);
  
  if (!bloque) {
    alert('⚠️ Error: Selecciona primero el bloque de colección');
    return;
  }
  
  if (bloque.tipo !== 'coleccion') {
    alert('⚠️ Error: El bloque seleccionado no es de tipo colección');
    return;
  }
  
  // Asegurar estructura de datos consistente
  if (!bloque.contenido) {
    bloque.contenido = { columns: [] };
  }
  
  if (!bloque.contenido.columns || !Array.isArray(bloque.contenido.columns)) {
    bloque.contenido.columns = [];
  }
  
  const numeroElemento = bloque.contenido.columns.length + 1;
  const nuevoElemento = {
    titulo: `Elemento ${numeroElemento}`,
    imagen: '',
    resumen: `Descripción corta del elemento ${numeroElemento}`,
    detalle: `Descripción detallada del elemento ${numeroElemento}. Puedes editarlo desde el panel de propiedades para personalizar este contenido.`
  };
  
  // Agregar el nuevo elemento
  bloque.contenido.columns.push(nuevoElemento);
  
  console.log(`✅ Elemento agregado. Total elementos: ${bloque.contenido.columns.length}`);
  
  // Actualizar visualización
  actualizarCanvas();
  mostrarPropiedades(); // Actualizar panel de propiedades con el nuevo elemento
  
  // Notificación de éxito
  mostrarNotificacion(`✅ ¡Elemento ${numeroElemento} agregado! Total: ${bloque.contenido.columns.length} elemento${bloque.contenido.columns.length !== 1 ? 's' : ''}`);
  
  // Auto-scroll al final del panel de propiedades para ver el nuevo elemento
  setTimeout(() => {
    const propertiesPanel = document.querySelector('.properties');
    if (propertiesPanel) {
      propertiesPanel.scrollTop = propertiesPanel.scrollHeight;
    }
  }, 100);
}

function eliminarElementoColeccion(index) {
  const bloque = bloques.find(b => b.id === bloqueSeleccionado);
  if (bloque && bloque.tipo === 'coleccion' && bloque.contenido.columns) {
    if (confirm('¿Eliminar este elemento de la colección?')) {
      bloque.contenido.columns.splice(index, 1);
      actualizarCanvas();
      mostrarPropiedades();
      mostrarNotificacion('🗑️ Elemento eliminado de la colección');
    }
  }
}

// Función específica para eliminar desde el canvas (sin necesidad de selección previa)
function eliminarElementoColeccionCanvas(index) {
  console.log('🗑️ Eliminando elemento desde canvas:', index);
  
  // Buscar el bloque de colección activo
  const bloqueColeccion = bloques.find(b => b.tipo === 'coleccion');
  
  if (!bloqueColeccion || !bloqueColeccion.contenido.columns) {
    alert('⚠️ Error: No se encontró la colección');
    return;
  }
  
  if (bloqueColeccion.contenido.columns.length <= 1) {
    alert('⚠️ No puedes eliminar el último elemento. La colección debe tener al menos 1 elemento.');
    return;
  }
  
  const elemento = bloqueColeccion.contenido.columns[index];
  const titulo = elemento?.titulo || `Elemento ${index + 1}`;
  
  if (confirm(`¿Eliminar "${titulo}"?\n\nEsta acción no se puede deshacer.`)) {
    bloqueColeccion.contenido.columns.splice(index, 1);
    
    // Seleccionar el bloque de colección para actualizar propiedades
    seleccionarBloque(bloqueColeccion.id);
    
    actualizarCanvas();
    mostrarPropiedades();
    
    const elementosRestantes = bloqueColeccion.contenido.columns.length;
    mostrarNotificacion(`🗑️ Elemento "${titulo}" eliminado. Quedan ${elementosRestantes} elemento${elementosRestantes !== 1 ? 's' : ''}`);
  }
}

function actualizarElementoColeccion(index, propiedad, valor) {
  const bloque = bloques.find(b => b.id === bloqueSeleccionado);
  if (bloque && bloque.tipo === 'coleccion' && bloque.contenido.columns && bloque.contenido.columns[index]) {
    bloque.contenido.columns[index][propiedad] = valor;
    actualizarCanvas();
    
    // No actualizar propiedades automáticamente para evitar pérdida de scroll
    // mostrarPropiedades();
    
    mostrarNotificacion(`✅ ${propiedad} actualizado`);
  }
}

// FUNCIÓN PARA CONVERTIR SECCIÓN EXISTENTE A BLOQUES
function convertirSeccionABloques(seccion) {
  const bloques = [];
  let contadorBloques = 0;
  
  if (seccion.tipo === 'multiformato' && seccion.bloques) {
    // Ya es multiformato, usar bloques existentes
    return seccion.bloques.map(bloque => ({
      ...bloque,
      id: bloque.id || 'bloque_' + (++contadorBloques)
    }));
  }
  
  if (seccion.tipo === 'texto') {
    // Convertir sección tipo texto
    if (seccion.contenido && seccion.contenido.html) {
      bloques.push({
        id: 'bloque_' + (++contadorBloques),
        tipo: 'html',
        contenido: seccion.contenido.html
      });
    }
    if (seccion.imagen) {
      bloques.push({
        id: 'bloque_' + (++contadorBloques),
        tipo: 'imagen',
        contenido: {
          src: [seccion.imagen],
          alt: 'Imagen de la sección',
          caption: ''
        }
      });
    }
  }
  
  if (seccion.tipo === 'coleccion') {
    // Convertir sección tipo colección a un solo bloque de colección editable
    if (seccion.columns && seccion.columns.length > 0) {
      // Normalizar elementos para asegurar que tengan todos los campos
      const elementosNormalizados = seccion.columns.map((elemento, index) => ({
        titulo: elemento.titulo || elemento.title || `Elemento ${index + 1}`,
        imagen: elemento.imagen || elemento.image || '',
        resumen: elemento.resumen || elemento.summary || elemento.descripcion || elemento.description || 'Agrega una descripción breve aqui...',
        detalle: elemento.detalle || elemento.detail || elemento.contenido || elemento.content || 'Agrega el contenido completo para el modal aqui...'
      }));
      
      // Crear un solo bloque de colección con todos los elementos normalizados
      bloques.push({
        id: 'bloque_' + (++contadorBloques),
        tipo: 'coleccion',
        contenido: {
          columns: elementosNormalizados
        }
      });
    } else {
      // Crear bloque de colección vacía
      bloques.push({
        id: 'bloque_' + (++contadorBloques),
        tipo: 'coleccion',
        contenido: {
          columns: []
        }
      });
    }
  }
  
  return bloques;
}

// CARGAR SECCIÓN EXISTENTE
function cargarSeccionExistente() {
  if (!modoEdicion || !seccionExistente) return;
  
  console.log('📝 Cargando sección existente:', seccionExistente);
  
  // Cargar datos básicos
  document.getElementById('tituloSeccion').value = seccionExistente.titulo || '';
  document.getElementById('colorFondo').value = seccionExistente.color_fondo || seccionExistente.bg || '#ffffff';
  document.getElementById('mostrarMenu').value = seccionExistente.mostrar_menu || seccionExistente.show_in_menu ? '1' : '0';
  
  // Convertir y cargar bloques
  bloques = convertirSeccionABloques(seccionExistente);
  contadorId = bloques.length;
  
  actualizarCanvas();
  mostrarPropiedades();
  
  mostrarNotificacion('📝 Sección cargada para edición');
}

// INICIALIZACIÓN
document.addEventListener('DOMContentLoaded', function() {
  console.log('🚀 Constructor avanzado cargado correctamente');
  
  // Cargar sección existente si estamos en modo edición
  if (modoEdicion) {
    cargarSeccionExistente();
  } else {
    actualizarCanvas();
    mostrarPropiedades();
  }
  
  // Mostrar mensaje de bienvenida
  setTimeout(() => {
    mostrarNotificacion('🎉 Constructor avanzado listo - Edición completa habilitada');
  }, 500);
});
</script>

</body>
</html>