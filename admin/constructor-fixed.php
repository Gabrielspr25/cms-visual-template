<?php
session_start();
if (empty($_SESSION['usuario'])) { header('Location: login.php'); exit; }

$dataFile = __DIR__ . '/../data.json';
$data = file_exists($dataFile) ? json_decode(file_get_contents($dataFile), true) : [];
if (!is_array($data)) $data = [];

$brand = $data['brand'] ?? ['name'=>'MomVision','logo'=>''];

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Constructor Multiformato - MomVision</title>
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

/* MAIN LAYOUT */
.main { 
  margin-left: 260px; 
  display: grid; 
  grid-template-columns: 280px 1fr 320px; 
  height: 100vh; 
}

/* HEADER */
.header { 
  grid-column: 1 / -1;
  background: var(--bg-secondary); 
  padding: 15px 20px; 
  border-bottom: 1px solid var(--border); 
  display: flex; justify-content: space-between; align-items: center; 
  height: 60px;
}
.header h1 { margin: 0; font-size: 20px; }

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
.quick-panel-title {
  font-size: 14px;
  font-weight: 500;
  margin-bottom: 10px;
  color: var(--accent);
}
.quick-buttons {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 8px;
}

.block-category {
  margin-bottom: 20px;
}
.category-title {
  font-size: 14px;
  font-weight: 500;
  margin-bottom: 10px;
  color: var(--text-secondary);
}
.block-item {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 10px;
  background: var(--bg-primary);
  border: 1px solid var(--border);
  border-radius: 6px;
  margin-bottom: 8px;
  cursor: grab;
  transition: all 0.2s;
  position: relative;
}
.block-item:hover {
  background: var(--accent);
  transform: translateX(5px);
}
.block-item:active {
  cursor: grabbing;
}
.block-icon {
  width: 24px;
  text-align: center;
  color: var(--accent);
  flex-shrink: 0;
}
.block-item:hover .block-icon {
  color: white;
}
.block-item span {
  flex: 1;
}
.add-block-btn {
  background: var(--success);
  border: none;
  color: white;
  width: 24px;
  height: 24px;
  border-radius: 50%;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 12px;
  transition: all 0.2s;
  flex-shrink: 0;
}
.add-block-btn:hover {
  background: #16a085;
  transform: scale(1.1);
}
.block-item:hover .add-block-btn {
  background: white;
  color: var(--success);
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
  display: flex;
  justify-content: space-between;
  align-items: center;
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
  transition: all 0.3s ease;
}
.canvas-area.has-blocks {
  border-style: solid;
  border-color: var(--accent);
  background: var(--bg-secondary);
}
.canvas-area.drag-over {
  border-color: var(--success) !important;
  border-style: solid !important;
  background: rgba(56, 161, 105, 0.1) !important;
  transform: scale(1.02);
}
.drop-placeholder {
  text-align: center;
  color: var(--text-secondary);
  font-size: 18px;
  padding: 60px 20px;
  transition: all 0.3s ease;
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

/* PROPERTIES PANEL */
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
.property-group {
  margin-bottom: 20px;
}
.property-label {
  font-size: 14px;
  font-weight: 500;
  margin-bottom: 6px;
  color: var(--text-secondary);
}
.property-input {
  width: 100%;
  padding: 8px 12px;
  border: 1px solid var(--border);
  border-radius: 6px;
  background: var(--bg-primary);
  color: var(--text-primary);
  margin-bottom: 10px;
}
.property-input:focus {
  outline: none;
  border-color: var(--accent);
}

/* BUTTONS */
.btn {
  padding: 8px 16px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 500;
  transition: all 0.2s;
  display: inline-flex;
  align-items: center;
  gap: 6px;
}
.btn-primary { background: var(--accent); color: white; }
.btn-primary:hover { background: var(--accent-hover); }
.btn-success { background: var(--success); color: white; }
.btn-danger { background: var(--danger); color: white; }
.btn-sm { padding: 6px 12px; font-size: 14px; }

/* MODAL */
.modal {
  display: none;
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.8);
  z-index: 1000;
  align-items: center;
  justify-content: center;
  padding: 20px;
}
.modal.show { display: flex; }
.modal-content {
  background: var(--bg-secondary);
  border-radius: 12px;
  width: 100%;
  max-width: 600px;
  max-height: 90vh;
  overflow-y: auto;
}
.modal-header {
  padding: 20px;
  border-bottom: 1px solid var(--border);
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.modal-body { padding: 20px; }
.modal-footer {
  padding: 20px;
  border-top: 1px solid var(--border);
  display: flex;
  gap: 12px;
  justify-content: flex-end;
}
.close-modal {
  background: none;
  border: none;
  color: var(--text-secondary);
  font-size: 24px;
  cursor: pointer;
}

@media (max-width: 768px) {
  .sidebar { width: 100%; height: auto; position: relative; }
  .main { margin-left: 0; grid-template-columns: 1fr; }
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
    <a href="dashboard-new.php" class="nav-item">
      <i class="fas fa-home"></i>
      Dashboard
    </a>
    <a href="#constructor" class="nav-item active">
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
  <!-- HEADER -->
  <div class="header">
    <h1><i class="fas fa-magic"></i> Constructor Multiformato</h1>
    <div>
      <button class="btn btn-success" onclick="guardarSeccion()">
        <i class="fas fa-save"></i>
        Guardar Sección
      </button>
      <button class="btn btn-primary" onclick="previsualizarSeccion()">
        <i class="fas fa-eye"></i>
        Preview
      </button>
      <button class="btn" onclick="testDragDrop()" style="background: var(--warning); color: white;">
        <i class="fas fa-bug"></i>
        Test D&D
      </button>
    </div>
  </div>

  <!-- BLOCKS LIBRARY -->
  <div class="blocks-library">
    <div class="library-title">
      <i class="fas fa-cube"></i> Biblioteca de Bloques
    </div>
    
    <!-- PANEL DE BOTONES RÁPIDOS -->
    <div class="quick-panel">
      <div class="quick-panel-title">
        <i class="fas fa-bolt"></i> Agregar Rápido
      </div>
      <div class="quick-buttons">
        <button class="btn btn-sm btn-primary" onclick="addBlockQuick('text')" title="Agregar Texto">
          <i class="fas fa-paragraph"></i> Texto
        </button>
        <button class="btn btn-sm btn-primary" onclick="addBlockQuick('heading')" title="Agregar Título">
          <i class="fas fa-heading"></i> Título
        </button>
        <button class="btn btn-sm btn-primary" onclick="addBlockQuick('list')" title="Agregar Lista">
          <i class="fas fa-list"></i> Lista
        </button>
        <button class="btn btn-sm btn-primary" onclick="addBlockQuick('image')" title="Agregar Galería">
          <i class="fas fa-images"></i> Galería
        </button>
        <button class="btn btn-sm btn-primary" onclick="addBlockQuick('video')" title="Agregar Video">
          <i class="fas fa-video"></i> Video
        </button>
        <button class="btn btn-sm btn-primary" onclick="addBlockQuick('columns')" title="Agregar Columnas">
          <i class="fas fa-columns"></i> Columnas
        </button>
        <button class="btn btn-sm btn-primary" onclick="addBlockQuick('button')" title="Agregar Botón">
          <i class="fas fa-hand-pointer"></i> Botón
        </button>
        <button class="btn btn-sm btn-primary" onclick="addBlockQuick('html')" title="Agregar HTML">
          <i class="fas fa-code"></i> HTML
        </button>
      </div>
    </div>

    <div class="block-category">
      <div class="category-title">📝 Contenido</div>
      <div class="block-item" draggable="true" data-type="text">
        <i class="fas fa-paragraph block-icon"></i>
        <span>Texto</span>
        <button class="add-block-btn" onclick="addBlockToCanvas('text')" title="Agregar Texto">
          <i class="fas fa-plus"></i>
        </button>
      </div>
      <div class="block-item" draggable="true" data-type="heading">
        <i class="fas fa-heading block-icon"></i>
        <span>Título</span>
        <button class="add-block-btn" onclick="addBlockToCanvas('heading')" title="Agregar Título">
          <i class="fas fa-plus"></i>
        </button>
      </div>
      <div class="block-item" draggable="true" data-type="list">
        <i class="fas fa-list-ul block-icon"></i>
        <span>Lista</span>
        <button class="add-block-btn" onclick="addBlockToCanvas('list')" title="Agregar Lista">
          <i class="fas fa-plus"></i>
        </button>
      </div>
    </div>

    <div class="block-category">
      <div class="category-title">🖼️ Media</div>
      <div class="block-item" draggable="true" data-type="image">
        <i class="fas fa-image block-icon"></i>
        <span>Galería</span>
        <button class="add-block-btn" onclick="addBlockToCanvas('image')" title="Agregar Galería">
          <i class="fas fa-plus"></i>
        </button>
      </div>
      <div class="block-item" draggable="true" data-type="video">
        <i class="fas fa-video block-icon"></i>
        <span>Video</span>
        <button class="add-block-btn" onclick="addBlockToCanvas('video')" title="Agregar Video">
          <i class="fas fa-plus"></i>
        </button>
      </div>
    </div>

    <div class="block-category">
      <div class="category-title">🏗️ Layout</div>
      <div class="block-item" draggable="true" data-type="columns">
        <i class="fas fa-columns block-icon"></i>
        <span>Columnas</span>
        <button class="add-block-btn" onclick="addBlockToCanvas('columns')" title="Agregar Columnas">
          <i class="fas fa-plus"></i>
        </button>
      </div>
      <div class="block-item" draggable="true" data-type="separator">
        <i class="fas fa-minus block-icon"></i>
        <span>Separador</span>
        <button class="add-block-btn" onclick="addBlockToCanvas('separator')" title="Agregar Separador">
          <i class="fas fa-plus"></i>
        </button>
      </div>
      <div class="block-item" draggable="true" data-type="spacer">
        <i class="fas fa-arrows-alt-v block-icon"></i>
        <span>Espaciador</span>
        <button class="add-block-btn" onclick="addBlockToCanvas('spacer')" title="Agregar Espaciador">
          <i class="fas fa-plus"></i>
        </button>
      </div>
    </div>

    <div class="block-category">
      <div class="category-title">📱 Interacción</div>
      <div class="block-item" draggable="true" data-type="button">
        <i class="fas fa-hand-pointer block-icon"></i>
        <span>Botón</span>
        <button class="add-block-btn" onclick="addBlockToCanvas('button')" title="Agregar Botón">
          <i class="fas fa-plus"></i>
        </button>
      </div>
      <div class="block-item" draggable="true" data-type="html">
        <i class="fas fa-code block-icon"></i>
        <span>HTML</span>
        <button class="add-block-btn" onclick="addBlockToCanvas('html')" title="Agregar HTML">
          <i class="fas fa-plus"></i>
        </button>
      </div>
    </div>
  </div>

  <!-- CANVAS -->
  <div class="canvas">
    <div class="canvas-title">
      Construir Nueva Sección
    </div>

    <div class="section-info">
      <input type="text" id="sectionTitle" placeholder="Título de la sección" class="property-input">
      <input type="color" id="sectionBg" value="#ffffff" class="property-input" style="width: 60px;">
      <select id="sectionMenu" class="property-input">
        <option value="1">Mostrar en menú</option>
        <option value="0">Ocultar del menú</option>
      </select>
    </div>

    <div id="canvas-area" class="canvas-area">
      <div class="drop-placeholder">
        <i class="fas fa-magic" style="font-size: 48px; margin-bottom: 15px; color: var(--accent);"></i>
        <div style="font-size: 18px; font-weight: 500; margin-bottom: 10px;">Construye tu sección</div>
        <div style="font-size: 14px; color: var(--text-secondary); margin-bottom: 15px;">
          ⚡ Usa los botones azules de arriba<br>
          ➕ O los botones verdes de cada elemento<br>
          🖱️ También puedes hacer doble clic
        </div>
        <div style="padding: 10px; background: var(--bg-secondary); border-radius: 6px; font-size: 12px; color: var(--text-secondary);">
          🐈 Abre la consola (F12) para ver logs de depuración
        </div>
      </div>
    </div>
  </div>

  <!-- PROPERTIES PANEL -->
  <div class="properties">
    <div class="properties-title">
      <i class="fas fa-cog"></i> Propiedades
    </div>
    
    <div id="no-selection" style="text-align: center; color: var(--text-secondary); margin-top: 40px;">
      <i class="fas fa-mouse-pointer" style="font-size: 32px; margin-bottom: 15px;"></i>
      <div>Selecciona un bloque para ver sus propiedades</div>
    </div>

    <div id="block-properties" style="display: none;">
      <!-- Se llena dinámicamente -->
    </div>
  </div>
</div>

<!-- MODAL PREVIEW -->
<div id="modalPreview" class="modal">
  <div class="modal-content" style="max-width: 90%; width: 1000px;">
    <div class="modal-header">
      <h3>Vista Previa de la Sección</h3>
      <button class="close-modal" onclick="cerrarModal('modalPreview')">&times;</button>
    </div>
    <div class="modal-body">
      <div id="preview-content"></div>
    </div>
    <div class="modal-footer">
      <button class="btn" onclick="cerrarModal('modalPreview')">Cerrar</button>
    </div>
  </div>
</div>

<script>
let currentBlocks = [];
let selectedBlock = null;
let blockIdCounter = 0;

// FUNCIÓN DE EMERGENCIA PARA AGREGAR BLOQUES (SIEMPRE FUNCIONA)
function addBlockQuick(type) {
  console.log(`🚀 Agregando bloque rápido: ${type}`);
  addBlockToCanvas(type);
  
  // Mostrar mensaje
  const notification = document.createElement('div');
  notification.innerHTML = `✅ Bloque "${type}" agregado`;
  notification.style.cssText = `
    position: fixed; top: 20px; right: 20px; z-index: 2000;
    background: var(--success); color: white; padding: 12px 20px;
    border-radius: 8px; font-weight: 500; box-shadow: 0 4px 12px rgba(0,0,0,0.3);
  `;
  document.body.appendChild(notification);
  
  setTimeout(() => notification.remove(), 2000);
}

function addBlockToCanvas(type) {
  const blockId = 'block_' + (++blockIdCounter);
  const block = {
    id: blockId,
    type: type,
    content: getDefaultContent(type),
    properties: getDefaultProperties(type)
  };
  
  currentBlocks.push(block);
  renderCanvas();
  selectBlock(blockId);
}

function getDefaultContent(type) {
  const defaults = {
    'text': 'Escribe tu texto aquí...',
    'heading': 'Título de Ejemplo',
    'list': ['Elemento 1', 'Elemento 2', 'Elemento 3'],
    'image': { images: [], caption: '' },
    'video': { src: '', caption: '' },
    'columns': { columns: 2, content: ['', ''] },
    'separator': {},
    'spacer': { height: 50 },
    'button': { text: 'Click aquí', url: '#' },
    'html': '<p>Código HTML personalizado</p>'
  };
  
  return defaults[type] || '';
}

function getDefaultProperties(type) {
  const defaults = {
    'text': { fontSize: 16, color: '#333333', alignment: 'left' },
    'heading': { level: 'h2', color: '#333333', alignment: 'left' },
    'list': { style: 'ul', color: '#333333' },
    'image': { columns: 3, spacing: 10, alignment: 'center', showCaptions: true },
    'video': { width: '100%', alignment: 'center' },
    'columns': { gap: 20 },
    'separator': { color: '#cccccc', thickness: 1 },
    'spacer': { backgroundColor: 'transparent' },
    'button': { color: '#ffffff', backgroundColor: '#3182ce', alignment: 'left' },
    'html': {}
  };
  
  return defaults[type] || {};
}

function renderCanvas() {
  const canvasArea = document.getElementById('canvas-area');
  
  if (currentBlocks.length === 0) {
    canvasArea.innerHTML = `
      <div class="drop-placeholder">
        <i class="fas fa-magic" style="font-size: 48px; margin-bottom: 15px; color: var(--accent);"></i>
        <div style="font-size: 18px; font-weight: 500; margin-bottom: 10px;">Construye tu sección</div>
        <div style="font-size: 14px; color: var(--text-secondary); margin-bottom: 15px;">
          ⚡ Usa los botones azules de arriba<br>
          ➕ O los botones verdes de cada elemento<br>
          🖱️ También puedes hacer doble clic
        </div>
        <div style="padding: 10px; background: var(--bg-secondary); border-radius: 6px; font-size: 12px; color: var(--text-secondary);">
          🐈 Abre la consola (F12) para ver logs de depuración
        </div>
      </div>
    `;
    canvasArea.classList.remove('has-blocks');
    return;
  }
  
  canvasArea.classList.add('has-blocks');
  canvasArea.innerHTML = '';
  
  currentBlocks.forEach((block, index) => {
    const blockElement = createBlockElement(block, index);
    canvasArea.appendChild(blockElement);
  });
}

function createBlockElement(block, index) {
  const div = document.createElement('div');
  div.className = 'canvas-block';
  div.dataset.blockId = block.id;
  
  const iconMap = {
    'text': 'fa-paragraph',
    'heading': 'fa-heading', 
    'list': 'fa-list-ul',
    'image': 'fa-image',
    'video': 'fa-video',
    'columns': 'fa-columns',
    'separator': 'fa-minus',
    'spacer': 'fa-arrows-alt-v',
    'button': 'fa-hand-pointer',
    'html': 'fa-code'
  };
  
  div.innerHTML = `
    <div class="block-header">
      <div class="block-title">
        <i class="fas ${iconMap[block.type] || 'fa-cube'}"></i>
        ${block.type.charAt(0).toUpperCase() + block.type.slice(1)}
      </div>
      <div class="block-actions">
        <button class="block-btn" onclick="moveBlock(${index}, -1)" title="Subir">
          <i class="fas fa-arrow-up"></i>
        </button>
        <button class="block-btn" onclick="moveBlock(${index}, 1)" title="Bajar">
          <i class="fas fa-arrow-down"></i>
        </button>
        <button class="block-btn" onclick="duplicateBlock(${index})" title="Duplicar">
          <i class="fas fa-copy"></i>
        </button>
        <button class="block-btn" onclick="deleteBlock(${index})" title="Eliminar" style="color: var(--danger);">
          <i class="fas fa-trash"></i>
        </button>
      </div>
    </div>
    <div class="block-content">
      ${renderBlockContent(block)}
    </div>
  `;
  
  div.addEventListener('click', () => selectBlock(block.id));
  
  return div;
}

function renderBlockContent(block) {
  switch(block.type) {
    case 'text':
      return `<p style="color: ${block.properties.color}; font-size: ${block.properties.fontSize}px; text-align: ${block.properties.alignment};">${block.content}</p>`;
    
    case 'heading':
      return `<${block.properties.level} style="color: ${block.properties.color}; text-align: ${block.properties.alignment};">${block.content}</${block.properties.level}>`;
    
    case 'list':
      const listItems = block.content.map(item => `<li>${item}</li>`).join('');
      return `<${block.properties.style} style="color: ${block.properties.color};">${listItems}</${block.properties.style}>`;
    
    case 'image':
      if (block.content.images && block.content.images.length > 0) {
        const spacing = block.properties.spacing || 10;
        let html = `<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: ${spacing}px; text-align: ${block.properties.alignment};">`;
        
        block.content.images.forEach(img => {
          html += `
            <div style="border-radius: 8px; overflow: hidden; background: #f5f5f5;">
              <img src="${img.src}" alt="${img.alt || ''}" style="width: 100%; height: 200px; object-fit: cover;">
              ${block.properties.showCaptions && img.caption ? `<div style="padding: 8px; font-size: 14px; color: #666;">${img.caption}</div>` : ''}
            </div>
          `;
        });
        
        html += '</div>';
        if (block.content.caption) {
          html += `<div style="text-align: center; font-size: 14px; color: #666; margin-top: 12px;">${block.content.caption}</div>`;
        }
        return html;
      } else {
        return `<div style="padding: 30px; text-align: center; border: 2px dashed #ccc; border-radius: 8px; color: #666;">
          <i class="fas fa-images" style="font-size: 32px; margin-bottom: 15px;"></i><br>
          <strong>Galería de Imágenes</strong><br>
          <small>Selecciona imágenes para crear tu galería</small>
        </div>`;
      }
    
    case 'video':
      return block.content.src ?
        `<div style="text-align: ${block.properties.alignment};">
          <video controls style="max-width: ${block.properties.width}; border-radius: 8px;">
            <source src="${block.content.src}" type="video/mp4">
          </video>
          ${block.content.caption ? `<div style="font-size: 14px; color: #666; margin-top: 8px;">${block.content.caption}</div>` : ''}
        </div>` :
        `<div style="padding: 20px; text-align: center; border: 2px dashed #ccc; border-radius: 8px; color: #666;">
          <i class="fas fa-video" style="font-size: 24px; margin-bottom: 10px;"></i><br>
          Selecciona un video
        </div>`;
    
    case 'button':
      return `<div style="text-align: ${block.properties.alignment};">
        <a href="${block.content.url}" style="display: inline-block; padding: 12px 24px; background: ${block.properties.backgroundColor}; color: ${block.properties.color}; text-decoration: none; border-radius: 8px; font-weight: 500;">
          ${block.content.text}
        </a>
      </div>`;
    
    case 'separator':
      return `<hr style="border: none; height: ${block.properties.thickness}px; background: ${block.properties.color}; margin: 20px 0;">`;
    
    case 'spacer':
      return `<div style="height: ${block.content.height}px; background: ${block.properties.backgroundColor};"></div>`;
    
    case 'html':
      return block.content;
    
    case 'columns':
      const columnContent = block.content.content.map(content => 
        `<div style="flex: 1;">${content || 'Contenido de columna...'}</div>`
      ).join('');
      return `<div style="display: flex; gap: ${block.properties.gap}px;">${columnContent}</div>`;
    
    default:
      return `<div style="padding: 20px; text-align: center; color: #666;">Bloque tipo: ${block.type}</div>`;
  }
}

function selectBlock(blockId) {
  // Remover selección anterior
  document.querySelectorAll('.canvas-block').forEach(el => el.classList.remove('active'));
  
  // Seleccionar nuevo bloque
  const blockElement = document.querySelector(`[data-block-id="${blockId}"]`);
  if (blockElement) {
    blockElement.classList.add('active');
    selectedBlock = currentBlocks.find(b => b.id === blockId);
    renderProperties();
  }
}

function renderProperties() {
  const noSelection = document.getElementById('no-selection');
  const blockProperties = document.getElementById('block-properties');
  
  if (!selectedBlock) {
    noSelection.style.display = 'block';
    blockProperties.style.display = 'none';
    return;
  }
  
  noSelection.style.display = 'none';
  blockProperties.style.display = 'block';
  blockProperties.innerHTML = `
    <div class="property-group">
      <div class="property-label">Tipo de Bloque</div>
      <div style="padding: 8px 12px; background: var(--bg-primary); border-radius: 6px; font-weight: 500;">
        ${selectedBlock.type.charAt(0).toUpperCase() + selectedBlock.type.slice(1)}
      </div>
    </div>
  `;
}

function moveBlock(index, direction) {
  const newIndex = index + direction;
  if (newIndex >= 0 && newIndex < currentBlocks.length) {
    [currentBlocks[index], currentBlocks[newIndex]] = [currentBlocks[newIndex], currentBlocks[index]];
    renderCanvas();
  }
}

function duplicateBlock(index) {
  const block = currentBlocks[index];
  const newBlock = {
    ...JSON.parse(JSON.stringify(block)),
    id: 'block_' + (++blockIdCounter)
  };
  currentBlocks.splice(index + 1, 0, newBlock);
  renderCanvas();
}

function deleteBlock(index) {
  const block = currentBlocks[index];
  if (confirm(`¿Eliminar el bloque "${block.type}"?`)) {
    currentBlocks.splice(index, 1);
    selectedBlock = null;
    renderCanvas();
    renderProperties();
  }
}

function previsualizarSeccion() {
  const title = document.getElementById('sectionTitle').value || 'Sección Sin Título';
  const previewContent = document.getElementById('preview-content');
  
  let html = `<h2 style="margin-bottom: 20px;">${title}</h2>`;
  
  currentBlocks.forEach(block => {
    html += `<div style="margin-bottom: 20px;">${renderBlockContent(block)}</div>`;
  });
  
  previewContent.innerHTML = html;
  document.getElementById('modalPreview').classList.add('show');
}

function guardarSeccion() {
  const title = document.getElementById('sectionTitle').value;
  const bg = document.getElementById('sectionBg').value;
  const showInMenu = document.getElementById('sectionMenu').value === '1';
  
  if (!title) {
    alert('Por favor ingresa un título para la sección');
    return;
  }
  
  if (currentBlocks.length === 0) {
    alert('Agrega al menos un bloque a la sección');
    return;
  }
  
  const sectionData = {
    id: title.toLowerCase().replace(/\s+/g, '-'),
    titulo: title,
    tipo: 'multiformato',
    bg: bg,
    border: 1,
    show_in_menu: showInMenu,
    blocks: currentBlocks
  };
  
  // Enviar al servidor
  fetch('save-multisection.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(sectionData)
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      alert('✅ Sección guardada correctamente!');
      // Limpiar canvas
      currentBlocks = [];
      selectedBlock = null;
      document.getElementById('sectionTitle').value = '';
      renderCanvas();
      renderProperties();
    } else {
      alert('❌ Error al guardar: ' + data.error);
    }
  })
  .catch(error => {
    alert('Error de conexión: ' + error);
  });
}

function cerrarModal(modalId) {
  document.getElementById(modalId).classList.remove('show');
}

function testDragDrop() {
  console.log('\n=== 🧪 TEST DRAG AND DROP ===');
  alert('🧪 TEST D&D\n\n✅ Agregando bloque de texto para probar...');
  addBlockQuick('text');
}

// Eventos para doble clic
document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('.block-item').forEach(item => {
    item.addEventListener('dblclick', function() {
      console.log('Double click on block:', this.dataset.type);
      addBlockToCanvas(this.dataset.type);
    });
  });
});

// Initialize
renderCanvas();
renderProperties();
</script>

</body>
</html>