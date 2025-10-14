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
  background: var(--success-hover, #16a085);
  transform: scale(1.1);
}
.block-item:hover .add-block-btn {
  background: white;
  color: var(--success);
}
.block-item.dragging {
  opacity: 0.5;
  transform: rotate(2deg) scale(0.95);
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
.drop-placeholder.drag-over {
  color: var(--success);
  transform: scale(1.05);
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

/* DRAG AND DROP */
.dragging {
  opacity: 0.5;
  transform: rotate(2deg);
}
.drop-zone {
  min-height: 40px;
  border: 2px dashed transparent;
  border-radius: 6px;
  transition: all 0.2s;
}
.drop-zone.drag-over {
  border-color: var(--accent);
  background: rgba(49, 130, 206, 0.1);
}

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
      <button class="btn" onclick="reinitializeDragDrop()" style="background: var(--success); color: white;">
        <i class="fas fa-sync"></i>
        Fix D&D
      </button>
    </div>
  </div>

  <!-- BLOCKS LIBRARY -->
  <div class="blocks-library">
    <div class="library-title">
      <i class="fas fa-cube"></i> Biblioteca de Bloques
    </div>
    
    <!-- PANEL DE BOTONES RÁPIDOS -->
    <div style="background: var(--bg-card); border-radius: 8px; padding: 15px; margin-bottom: 20px;">
      <div style="font-size: 14px; font-weight: 500; margin-bottom: 10px; color: var(--accent);">
        <i class="fas fa-bolt"></i> Agregar Rápido
      </div>
      <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 8px;">
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
        <span>Imagen</span>
        <button class="add-block-btn" onclick="addBlockToCanvas('image')" title="Agregar Imagen">
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
      <div class="block-item" draggable="true" data-type="gallery">
        <i class="fas fa-images block-icon"></i>
        <span>Galería</span>
        <button class="add-block-btn" onclick="addBlockToCanvas('gallery')" title="Agregar Galería">
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
        <div>Arrastra bloques aquí para construir tu sección</div>
        <div style="font-size: 14px; margin-top: 10px; color: var(--text-secondary);">
          Puedes combinar texto, imágenes, videos y más
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

// DRAG AND DROP - Versión simplificada y robusta
let draggedType = null;

document.addEventListener('DOMContentLoaded', function() {
  console.log('🚀 Inicializando Drag and Drop');
  
  const canvasArea = document.getElementById('canvas-area');
  if (!canvasArea) {
    console.error('❌ Canvas area no encontrado');
    return;
  }
  
  console.log('✅ Canvas area encontrado:', canvasArea);
  
  // Setup drag para todos los block-items
  setupDragForBlocks();
  
  // Setup drop para el canvas
  setupDropForCanvas(canvasArea);
});

function setupDragForBlocks() {
  const blockItems = document.querySelectorAll('.block-item');
  console.log('📦 Configurando', blockItems.length, 'bloques para drag');
  
  blockItems.forEach((item, index) => {
    const blockType = item.getAttribute('data-type');
    console.log(`🔧 Configurando bloque ${index + 1}: ${blockType}`);
    
    // Asegurar que sea draggable
    item.draggable = true;
    
    // Eventos drag
    item.ondragstart = function(e) {
      console.log('🎬 Drag iniciado para:', blockType);
      draggedType = blockType;
      e.dataTransfer.setData('text/plain', blockType);
      e.dataTransfer.effectAllowed = 'copy';
      this.classList.add('dragging');
      
      // Agregar efecto visual al canvas
      const canvas = document.getElementById('canvas-area');
      if (canvas) {
        canvas.style.borderColor = 'var(--success)';
        canvas.style.backgroundColor = 'rgba(56, 161, 105, 0.1)';
      }
    };
    
    item.ondragend = function(e) {
      console.log('🏁 Drag terminado');
      this.classList.remove('dragging');
      draggedType = null;
      
      // Remover efectos visuales del canvas
      const canvas = document.getElementById('canvas-area');
      if (canvas) {
        canvas.classList.remove('drag-over');
        canvas.style.borderColor = '';
        canvas.style.backgroundColor = '';
      }
    };
  });
}

function setupDropForCanvas(canvasArea) {
  console.log('🎯 Configurando drop para canvas');
  
  canvasArea.ondragover = function(e) {
    e.preventDefault();
    e.dataTransfer.dropEffect = 'copy';
    this.classList.add('drag-over');
    return false;
  };
  
  canvasArea.ondragenter = function(e) {
    e.preventDefault();
    console.log('📥 Drag enter en canvas');
    this.classList.add('drag-over');
    return false;
  };
  
  canvasArea.ondragleave = function(e) {
    // Solo remover si salimos completamente
    const rect = this.getBoundingClientRect();
    const x = e.clientX;
    const y = e.clientY;
    
    if (x <= rect.left || x >= rect.right || y <= rect.top || y >= rect.bottom) {
      console.log('📤 Drag leave del canvas');
      this.classList.remove('drag-over');
    }
  };
  
  canvasArea.ondrop = function(e) {
    e.preventDefault();
    console.log('🎉 DROP DETECTADO!');
    
    this.classList.remove('drag-over');
    this.style.borderColor = '';
    this.style.backgroundColor = '';
    
    let blockType = e.dataTransfer.getData('text/plain') || draggedType;
    console.log('📦 Tipo de bloque a agregar:', blockType);
    
    if (blockType) {
      console.log('✅ Agregando bloque al canvas:', blockType);
      addBlockToCanvas(blockType);
    } else {
      console.error('❌ No se pudo determinar el tipo de bloque');
    }
    
    return false;
  };
}

// También agregar eventos para hacer click en los bloques como alternativa
document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('.block-item').forEach(item => {
    item.addEventListener('dblclick', function() {
      console.log('Double click on block:', this.dataset.type);
      addBlockToCanvas(this.dataset.type);
    });
  });
  
  // Prevenir drag cuando se hace clic en el botón +
  document.querySelectorAll('.add-block-btn').forEach(btn => {
    btn.addEventListener('mousedown', function(e) {
      e.stopPropagation();
    });
    
    btn.addEventListener('dragstart', function(e) {
      e.preventDefault();
      e.stopPropagation();
    });
  });
});

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
    'gallery': { images: [] },
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
    'gallery': { columns: 3, spacing: 10 },
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
      <div class="drop-placeholder" id="drop-placeholder">
        <i class="fas fa-magic" style="font-size: 48px; margin-bottom: 15px; color: var(--accent);"></i>
        <div style="font-size: 18px; font-weight: 500; margin-bottom: 10px;">Construye tu sección</div>
        <div style="font-size: 14px; color: var(--text-secondary); margin-bottom: 15px;">
          🔄 Arrastra bloques desde la biblioteca<br>
          ➕ O haz clic en los botones verdes<br>
          🔄 También puedes hacer doble clic
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
    'gallery': 'fa-images',
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
        const columns = block.properties.columns || 3;
        const spacing = block.properties.spacing || 10;
        const showCaptions = block.properties.showCaptions || false;
        
        let html = `<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: ${spacing}px; text-align: ${block.properties.alignment};">``;
        
        block.content.images.forEach(img => {
          html += `
            <div style="border-radius: 8px; overflow: hidden; background: #f5f5f5;">
              <img src="${img.src}" alt="${img.alt || ''}" style="width: 100%; height: 200px; object-fit: cover;">
              ${showCaptions && img.caption ? `<div style="padding: 8px; font-size: 14px; color: #666;">${img.caption}</div>` : ''}
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
  blockProperties.innerHTML = generatePropertiesHTML(selectedBlock);
}

function generatePropertiesHTML(block) {
  let html = `<div class="property-group">
    <div class="property-label">Tipo de Bloque</div>
    <div style="padding: 8px 12px; background: var(--bg-primary); border-radius: 6px; font-weight: 500;">
      ${block.type.charAt(0).toUpperCase() + block.type.slice(1)}
    </div>
  </div>`;
  
  // Properties específicas por tipo
  switch(block.type) {
    case 'text':
      html += `
        <div class="property-group">
          <div class="property-label">Contenido</div>
          <textarea class="property-input" rows="4" onchange="updateBlockContent('${block.id}', 'content', this.value)">${block.content}</textarea>
        </div>
        <div class="property-group">
          <div class="property-label">Tamaño de Fuente</div>
          <input type="number" class="property-input" value="${block.properties.fontSize}" onchange="updateBlockProperty('${block.id}', 'fontSize', this.value)">
        </div>
        <div class="property-group">
          <div class="property-label">Color</div>
          <input type="color" class="property-input" value="${block.properties.color}" onchange="updateBlockProperty('${block.id}', 'color', this.value)">
        </div>
        <div class="property-group">
          <div class="property-label">Alineación</div>
          <select class="property-input" onchange="updateBlockProperty('${block.id}', 'alignment', this.value)">
            <option value="left" ${block.properties.alignment === 'left' ? 'selected' : ''}>Izquierda</option>
            <option value="center" ${block.properties.alignment === 'center' ? 'selected' : ''}>Centro</option>
            <option value="right" ${block.properties.alignment === 'right' ? 'selected' : ''}>Derecha</option>
          </select>
        </div>`;
      break;
      
    case 'heading':
      html += `
        <div class="property-group">
          <div class="property-label">Texto</div>
          <input type="text" class="property-input" value="${block.content}" onchange="updateBlockContent('${block.id}', 'content', this.value)">
        </div>
        <div class="property-group">
          <div class="property-label">Nivel</div>
          <select class="property-input" onchange="updateBlockProperty('${block.id}', 'level', this.value)">
            <option value="h1" ${block.properties.level === 'h1' ? 'selected' : ''}>H1</option>
            <option value="h2" ${block.properties.level === 'h2' ? 'selected' : ''}>H2</option>
            <option value="h3" ${block.properties.level === 'h3' ? 'selected' : ''}>H3</option>
            <option value="h4" ${block.properties.level === 'h4' ? 'selected' : ''}>H4</option>
          </select>
        </div>
        <div class="property-group">
          <div class="property-label">Color</div>
          <input type="color" class="property-input" value="${block.properties.color}" onchange="updateBlockProperty('${block.id}', 'color', this.value)">
        </div>`;
      break;
      
    case 'button':
      html += `
        <div class="property-group">
          <div class="property-label">Texto del Botón</div>
          <input type="text" class="property-input" value="${block.content.text}" onchange="updateBlockContentProperty('${block.id}', 'text', this.value)">
        </div>
        <div class="property-group">
          <div class="property-label">URL/Enlace</div>
          <input type="text" class="property-input" value="${block.content.url}" onchange="updateBlockContentProperty('${block.id}', 'url', this.value)">
        </div>
        <div class="property-group">
          <div class="property-label">Color de Fondo</div>
          <input type="color" class="property-input" value="${block.properties.backgroundColor}" onchange="updateBlockProperty('${block.id}', 'backgroundColor', this.value)">
        </div>
        <div class="property-group">
          <div class="property-label">Color de Texto</div>
          <input type="color" class="property-input" value="${block.properties.color}" onchange="updateBlockProperty('${block.id}', 'color', this.value)">
        </div>`;
      break;
      
    case 'list':
      const listItems = Array.isArray(block.content) ? block.content : ['Elemento 1', 'Elemento 2', 'Elemento 3'];
      html += `
        <div class="property-group">
          <div class="property-label">Elementos de la Lista</div>
          <div id="list-items-${block.id}">`;
      listItems.forEach((item, i) => {
        html += `
          <div style="display: flex; gap: 8px; margin-bottom: 8px;">
            <input type="text" class="property-input" value="${item}" onchange="updateListItem('${block.id}', ${i}, this.value)" style="flex: 1;">
            <button class="btn-danger" style="padding: 4px 8px;" onclick="removeListItem('${block.id}', ${i})">×</button>
          </div>`;
      });
      html += `
          </div>
          <button class="btn-primary" onclick="addListItem('${block.id}')" style="width: 100%; margin-top: 8px;">+ Agregar Elemento</button>
        </div>
        <div class="property-group">
          <div class="property-label">Tipo de Lista</div>
          <select class="property-input" onchange="updateBlockProperty('${block.id}', 'style', this.value)">
            <option value="ul" ${block.properties.style === 'ul' ? 'selected' : ''}>Lista con viñetas</option>
            <option value="ol" ${block.properties.style === 'ol' ? 'selected' : ''}>Lista numerada</option>
          </select>
        </div>
        <div class="property-group">
          <div class="property-label">Color</div>
          <input type="color" class="property-input" value="${block.properties.color}" onchange="updateBlockProperty('${block.id}', 'color', this.value)">
        </div>`;
      break;
      
    case 'image':
      const imageCount = (block.content.images && block.content.images.length) || 0;
      html += `
        <div class="property-group">
          <div class="property-label">Galería de Imágenes (${imageCount})</div>
          <button class="btn-primary" onclick="selectImagesForGallery('${block.id}')" style="width: 100%; margin-bottom: 8px;">
            <i class="fas fa-images"></i> Gestionar Imágenes
          </button>
          ${imageCount > 0 ? `<button class="btn-danger" onclick="clearGallery('${block.id}')" style="width: 100%;">Limpiar Galería</button>` : ''}
        </div>
        <div class="property-group">
          <div class="property-label">Leyenda General</div>
          <input type="text" class="property-input" value="${block.content.caption || ''}" onchange="updateBlockContentProperty('${block.id}', 'caption', this.value)" placeholder="Leyenda para toda la galería">
        </div>
        <div class="property-group">
          <div class="property-label">Columnas por Fila</div>
          <select class="property-input" onchange="updateBlockProperty('${block.id}', 'columns', parseInt(this.value))">
            <option value="1" ${block.properties.columns === 1 ? 'selected' : ''}>1 Columna</option>
            <option value="2" ${block.properties.columns === 2 ? 'selected' : ''}>2 Columnas</option>
            <option value="3" ${block.properties.columns === 3 ? 'selected' : ''}>3 Columnas</option>
            <option value="4" ${block.properties.columns === 4 ? 'selected' : ''}>4 Columnas</option>
          </select>
        </div>
        <div class="property-group">
          <div class="property-label">Espacio entre Imágenes</div>
          <input type="range" class="property-input" min="0" max="30" value="${block.properties.spacing}" onchange="updateBlockProperty('${block.id}', 'spacing', parseInt(this.value))">
          <div style="text-align: center; font-size: 12px; color: var(--text-secondary);">${block.properties.spacing}px</div>
        </div>
        <div class="property-group">
          <div class="property-label">Mostrar Leyendas</div>
          <select class="property-input" onchange="updateBlockProperty('${block.id}', 'showCaptions', this.value === 'true')">
            <option value="true" ${block.properties.showCaptions ? 'selected' : ''}>Sí</option>
            <option value="false" ${!block.properties.showCaptions ? 'selected' : ''}>No</option>
          </select>
        </div>
        <div class="property-group">
          <div class="property-label">Alineación</div>
          <select class="property-input" onchange="updateBlockProperty('${block.id}', 'alignment', this.value)">
            <option value="left" ${block.properties.alignment === 'left' ? 'selected' : ''}>Izquierda</option>
            <option value="center" ${block.properties.alignment === 'center' ? 'selected' : ''}>Centro</option>
            <option value="right" ${block.properties.alignment === 'right' ? 'selected' : ''}>Derecha</option>
          </select>
        </div>`;
      break;
      
    case 'video':
      html += `
        <div class="property-group">
          <div class="property-label">Seleccionar Video</div>
          <button class="btn-primary" onclick="selectVideo('${block.id}')" style="width: 100%;">Elegir Video</button>
          ${block.content.src ? `<div style="margin-top: 8px; font-size: 12px; color: var(--text-secondary);">Actual: ${block.content.src}</div>` : ''}
        </div>
        <div class="property-group">
          <div class="property-label">Leyenda</div>
          <input type="text" class="property-input" value="${block.content.caption || ''}" onchange="updateBlockContentProperty('${block.id}', 'caption', this.value)">
        </div>
        <div class="property-group">
          <div class="property-label">Ancho</div>
          <select class="property-input" onchange="updateBlockProperty('${block.id}', 'width', this.value)">
            <option value="50%" ${block.properties.width === '50%' ? 'selected' : ''}>50%</option>
            <option value="75%" ${block.properties.width === '75%' ? 'selected' : ''}>75%</option>
            <option value="100%" ${block.properties.width === '100%' ? 'selected' : ''}>100%</option>
          </select>
        </div>`;
      break;
      
    case 'columns':
      const columnContent = block.content.content || ['', ''];
      html += `
        <div class="property-group">
          <div class="property-label">Número de Columnas</div>
          <select class="property-input" onchange="updateColumnCount('${block.id}', this.value)">
            <option value="2" ${block.content.columns === 2 ? 'selected' : ''}>2 Columnas</option>
            <option value="3" ${block.content.columns === 3 ? 'selected' : ''}>3 Columnas</option>
            <option value="4" ${block.content.columns === 4 ? 'selected' : ''}>4 Columnas</option>
          </select>
        </div>
        <div class="property-group">
          <div class="property-label">Contenido de Columnas</div>`;
      columnContent.forEach((content, i) => {
        html += `
          <div style="margin-bottom: 12px;">
            <label style="display: block; margin-bottom: 4px; font-size: 12px; color: var(--text-secondary);">Columna ${i + 1}:</label>
            <textarea class="property-input" rows="3" onchange="updateColumnContent('${block.id}', ${i}, this.value)">${content}</textarea>
          </div>`;
      });
      html += `
        </div>
        <div class="property-group">
          <div class="property-label">Espacio entre Columnas</div>
          <input type="range" class="property-input" min="5" max="50" value="${block.properties.gap}" onchange="updateBlockProperty('${block.id}', 'gap', this.value)">
          <div style="text-align: center; font-size: 12px; color: var(--text-secondary);">${block.properties.gap}px</div>
        </div>`;
      break;
      
    case 'separator':
      html += `
        <div class="property-group">
          <div class="property-label">Color</div>
          <input type="color" class="property-input" value="${block.properties.color}" onchange="updateBlockProperty('${block.id}', 'color', this.value)">
        </div>
        <div class="property-group">
          <div class="property-label">Grosor</div>
          <input type="range" class="property-input" min="1" max="10" value="${block.properties.thickness}" onchange="updateBlockProperty('${block.id}', 'thickness', this.value)">
          <div style="text-align: center; font-size: 12px; color: var(--text-secondary);">${block.properties.thickness}px</div>
        </div>`;
      break;
      
    case 'spacer':
      html += `
        <div class="property-group">
          <div class="property-label">Altura</div>
          <input type="range" class="property-input" min="10" max="200" value="${block.content.height}" onchange="updateBlockContent('${block.id}', 'height', this.value)">
          <div style="text-align: center; font-size: 12px; color: var(--text-secondary);">${block.content.height}px</div>
        </div>
        <div class="property-group">
          <div class="property-label">Color de Fondo</div>
          <input type="color" class="property-input" value="${block.properties.backgroundColor}" onchange="updateBlockProperty('${block.id}', 'backgroundColor', this.value)">
        </div>`;
      break;
      
    case 'html':
      html += `
        <div class="property-group">
          <div class="property-label">Código HTML</div>
          <textarea class="property-input" rows="6" onchange="updateBlockContent('${block.id}', 'content', this.value)">${block.content}</textarea>
          <div style="font-size: 12px; color: var(--text-secondary); margin-top: 4px;">Puedes usar HTML y CSS personalizado</div>
        </div>`;
      break;
  }
  
  return html;
}

function updateBlockContent(blockId, property, value) {
  const block = currentBlocks.find(b => b.id === blockId);
  if (block) {
    if (block.type === 'spacer' && property === 'height') {
      block.content = { ...block.content, height: parseInt(value) };
    } else if (property === 'content') {
      block.content = value;
    }
    renderCanvas();
    selectBlock(blockId);
  }
}

function updateBlockProperty(blockId, property, value) {
  const block = currentBlocks.find(b => b.id === blockId);
  if (block) {
    block.properties[property] = value;
    renderCanvas();
    selectBlock(blockId);
  }
}

function updateBlockContentProperty(blockId, property, value) {
  const block = currentBlocks.find(b => b.id === blockId);
  if (block && typeof block.content === 'object') {
    block.content[property] = value;
    renderCanvas();
    selectBlock(blockId);
  }
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

// LIST FUNCTIONS
function updateListItem(blockId, index, value) {
  const block = currentBlocks.find(b => b.id === blockId);
  if (block && Array.isArray(block.content)) {
    block.content[index] = value;
    renderCanvas();
    selectBlock(blockId);
  }
}

function addListItem(blockId) {
  const block = currentBlocks.find(b => b.id === blockId);
  if (block) {
    if (!Array.isArray(block.content)) {
      block.content = ['Elemento 1'];
    } else {
      block.content.push('Nuevo elemento');
    }
    renderCanvas();
    selectBlock(blockId);
  }
}

function removeListItem(blockId, index) {
  const block = currentBlocks.find(b => b.id === blockId);
  if (block && Array.isArray(block.content) && block.content.length > 1) {
    block.content.splice(index, 1);
    renderCanvas();
    selectBlock(blockId);
  }
}

// GALLERY FUNCTIONS
function selectImagesForGallery(blockId) {
  const block = currentBlocks.find(b => b.id === blockId);
  if (!block) return;
  
  // Crear modal de galería
  const modal = document.createElement('div');
  modal.className = 'modal show';
  modal.innerHTML = `
    <div class="modal-content" style="max-width: 90%; width: 1000px;">
      <div class="modal-header">
        <h3><i class="fas fa-images"></i> Gestionar Galería de Imágenes</h3>
        <button class="close-modal" onclick="this.closest('.modal').remove()">&times;</button>
      </div>
      <div class="modal-body">
        <div style="margin-bottom: 20px;">
          <h4>Imágenes Seleccionadas (${(block.content.images || []).length})</h4>
          <div id="selected-images" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 10px; margin-bottom: 20px; min-height: 60px; border: 1px solid var(--border); border-radius: 8px; padding: 15px; background: var(--bg-primary);">
            ${(block.content.images || []).length === 0 ? '<div style="grid-column: 1/-1; text-align: center; color: var(--text-secondary); padding: 20px;">No hay imágenes seleccionadas</div>' : ''}
          </div>
        </div>
        <hr style="margin: 20px 0; border: none; height: 1px; background: var(--border);">
        <h4>Seleccionar Imágenes</h4>
        <div id="available-images" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 10px; max-height: 300px; overflow-y: auto; border: 1px solid var(--border); border-radius: 8px; padding: 15px; background: var(--bg-secondary);">
          <div style="text-align: center; color: #666; grid-column: 1/-1;">Cargando imágenes...</div>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn" onclick="this.closest('.modal').remove()">Cancelar</button>
        <button class="btn btn-primary" onclick="saveGalleryImages('${blockId}')">Guardar Galería</button>
      </div>
    </div>
  `;
  document.body.appendChild(modal);
  
  // Renderizar imágenes seleccionadas
  renderSelectedImages(blockId);
  
  // Cargar imágenes disponibles
  loadAvailableImages(blockId);
}

function renderSelectedImages(blockId) {
  const block = currentBlocks.find(b => b.id === blockId);
  const selectedContainer = document.getElementById('selected-images');
  if (!block || !selectedContainer) return;
  
  const images = block.content.images || [];
  
  if (images.length === 0) {
    selectedContainer.innerHTML = '<div style="grid-column: 1/-1; text-align: center; color: var(--text-secondary); padding: 20px;">No hay imágenes seleccionadas</div>';
    return;
  }
  
  selectedContainer.innerHTML = '';
  images.forEach((img, index) => {
    const imgDiv = document.createElement('div');
    imgDiv.style.cssText = 'position: relative; border-radius: 8px; overflow: hidden; background: var(--bg-secondary);';
    imgDiv.innerHTML = `
      <img src="../${img.src}" style="width: 100%; height: 80px; object-fit: cover;" 
           onerror="this.style.display='none'; this.nextElementSibling.style.display='flex'">
      <div style="display: none; width: 100%; height: 80px; align-items: center; justify-content: center; background: var(--bg-primary); color: var(--text-secondary);">
        <i class="fas fa-image"></i>
      </div>
      <div style="position: absolute; top: 2px; right: 2px;">
        <button onclick="removeImageFromGallery('${blockId}', ${index})" 
                style="background: var(--danger); border: none; color: white; width: 20px; height: 20px; border-radius: 50%; cursor: pointer; font-size: 10px;">
          ×
        </button>
      </div>
      <div style="position: absolute; bottom: 0; left: 0; right: 0; background: rgba(0,0,0,0.7); color: white; padding: 2px 4px; font-size: 10px;">
        ${img.alt || img.src.split('/').pop()}
      </div>
    `;
    selectedContainer.appendChild(imgDiv);
  });
}

function loadAvailableImages(blockId) {
  fetch('get-images.php')
    .then(response => response.json())
    .then(data => {
      console.log('Imágenes disponibles:', data);
      const container = document.getElementById('available-images');
      if (!container) return;
      
      if (data.success && data.images && data.images.length > 0) {
        container.innerHTML = '';
        data.images.forEach(imageObj => {
          const imagePath = imageObj.path || imageObj;
          const imgDiv = document.createElement('div');
          imgDiv.style.cssText = 'cursor: pointer; border: 2px solid transparent; border-radius: 8px; overflow: hidden; transition: all 0.2s; position: relative; background: var(--bg-primary);';
          imgDiv.innerHTML = `
            <img src="../${imagePath}" style="width: 100%; height: 100px; object-fit: cover;" 
                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex'">
            <div style="display: none; width: 100%; height: 100px; align-items: center; justify-content: center; color: var(--text-secondary);">
              <i class="fas fa-image"></i><br><small>Error</small>
            </div>
            <div style="position: absolute; bottom: 0; left: 0; right: 0; background: rgba(0,0,0,0.8); color: white; padding: 4px; font-size: 10px; text-align: center;">
              ${imageObj.filename || imagePath.split('/').pop()}
            </div>
          `;
          
          imgDiv.onclick = () => addImageToGallery(blockId, imagePath, imageObj.filename);
          imgDiv.onmouseenter = () => imgDiv.style.borderColor = 'var(--accent)';
          imgDiv.onmouseleave = () => imgDiv.style.borderColor = 'transparent';
          container.appendChild(imgDiv);
        });
      } else {
        container.innerHTML = '<div style="grid-column: 1/-1; text-align: center; color: #666; padding: 40px;">No hay imágenes disponibles<br><small>Sube imágenes desde la Galería</small></div>';
      }
    })
    .catch(error => {
      console.error('Error al cargar imágenes:', error);
      const container = document.getElementById('available-images');
      if (container) {
        container.innerHTML = '<div style="grid-column: 1/-1; text-align: center; color: #666; padding: 40px;">Error al cargar imágenes</div>';
      }
    });
}

function addImageToGallery(blockId, imagePath, filename) {
  const block = currentBlocks.find(b => b.id === blockId);
  if (!block) return;
  
  if (!block.content.images) {
    block.content.images = [];
  }
  
  // Verificar si la imagen ya está en la galería
  const exists = block.content.images.some(img => img.src === imagePath);
  if (exists) {
    alert('Esta imagen ya está en la galería');
    return;
  }
  
  // Agregar la imagen
  block.content.images.push({
    src: imagePath,
    alt: filename || imagePath.split('/').pop(),
    caption: ''
  });
  
  console.log('Imagen agregada a galería:', imagePath);
  renderSelectedImages(blockId);
  
  // Actualizar el contador en el título
  const title = document.querySelector('.modal h3');
  if (title) {
    title.innerHTML = `<i class="fas fa-images"></i> Gestionar Galería de Imágenes (${block.content.images.length})`;
  }
}

function removeImageFromGallery(blockId, index) {
  const block = currentBlocks.find(b => b.id === blockId);
  if (!block || !block.content.images) return;
  
  if (confirm('¿Eliminar esta imagen de la galería?')) {
    block.content.images.splice(index, 1);
    renderSelectedImages(blockId);
    
    // Actualizar el contador
    const title = document.querySelector('.modal h3');
    if (title) {
      title.innerHTML = `<i class="fas fa-images"></i> Gestionar Galería de Imágenes (${block.content.images.length})`;
    }
  }
}

function saveGalleryImages(blockId) {
  // Actualizar el canvas y cerrar modal
  renderCanvas();
  selectBlock(blockId);
  document.querySelector('.modal').remove();
  console.log('Galería guardada para bloque:', blockId);
}

function clearGallery(blockId) {
  if (confirm('¿Eliminar todas las imágenes de la galería?')) {
    const block = currentBlocks.find(b => b.id === blockId);
    if (block) {
      block.content.images = [];
      renderCanvas();
      selectBlock(blockId);
    }
  }
  
  // Cargar imágenes disponibles
  fetch('get-images.php')
    .then(response => response.json())
    .then(data => {
      console.log('Respuesta de get-images:', data);
      if (data.success && data.images && data.images.length > 0) {
        const gallery = modal.querySelector('#image-gallery');
        gallery.innerHTML = '';
        data.images.forEach(imageObj => {
          const imagePath = imageObj.path || imageObj; // Soporte para ambos formatos
          const imgDiv = document.createElement('div');
          imgDiv.style.cssText = 'cursor: pointer; border: 2px solid transparent; border-radius: 8px; overflow: hidden; transition: all 0.2s; position: relative;';
          imgDiv.innerHTML = `
            <img src="../${imagePath}" style="width: 100%; height: 100px; object-fit: cover;" 
                 onerror="this.style.display='none'; this.nextElementSibling.style.display='block'">
            <div style="display: none; padding: 20px; text-align: center; background: var(--bg-primary); color: var(--text-secondary);"
                <i class="fas fa-image"></i><br>Error al cargar
            </div>
            <div style="position: absolute; bottom: 0; left: 0; right: 0; background: rgba(0,0,0,0.7); color: white; padding: 4px; font-size: 10px; text-align: center;">
              ${imageObj.filename || imagePath.split('/').pop()}
            </div>
          `;
          imgDiv.onclick = () => {
            console.log('Imagen seleccionada:', imagePath);
            const block = currentBlocks.find(b => b.id === blockId);
            if (block) {
              if (!block.content || typeof block.content === 'string') {
                block.content = {};
              }
              block.content = {
                ...block.content,
                src: imagePath
              };
              console.log('Bloque actualizado:', block);
              renderCanvas();
              selectBlock(blockId);
            }
            modal.remove();
          };
          imgDiv.onmouseenter = () => imgDiv.style.borderColor = 'var(--accent)';
          imgDiv.onmouseleave = () => imgDiv.style.borderColor = 'transparent';
          gallery.appendChild(imgDiv);
        });
      } else {
        modal.querySelector('#image-gallery').innerHTML = '<div style="text-align: center; color: #666; padding: 40px;">No hay imágenes disponibles<br><small>Sube imágenes desde la Galería</small></div>';
      }
    })
    .catch(error => {
      console.error('Error al cargar imágenes:', error);
      modal.querySelector('#image-gallery').innerHTML = '<div style="text-align: center; color: #666; padding: 40px;">Error al cargar imágenes<br><small>' + error.message + '</small></div>';
    });
}

// VIDEO FUNCTIONS
function selectVideo(blockId) {
  const modal = document.createElement('div');
  modal.className = 'modal show';
  modal.innerHTML = `
    <div class="modal-content" style="max-width: 600px;">
      <div class="modal-header">
        <h3>Seleccionar Video</h3>
        <button class="close-modal" onclick="this.closest('.modal').remove()">&times;</button>
      </div>
      <div class="modal-body">
        <div class="property-group">
          <label class="property-label">URL del Video</label>
          <input type="text" id="videoUrl" class="property-input" placeholder="Ej: uploads/video.mp4">
        </div>
        <div style="text-align: center; margin-top: 20px;">
          <button class="btn-primary" onclick="setVideoUrl('${blockId}')">Usar Video</button>
        </div>
      </div>
    </div>
  `;
  document.body.appendChild(modal);
}

function setVideoUrl(blockId) {
  const url = document.getElementById('videoUrl').value;
  if (url) {
    const block = currentBlocks.find(b => b.id === blockId);
    if (block) {
      block.content = {
        ...block.content,
        src: url
      };
      renderCanvas();
      selectBlock(blockId);
    }
  }
  document.querySelector('.modal').remove();
}

// COLUMN FUNCTIONS
function updateColumnCount(blockId, count) {
  const block = currentBlocks.find(b => b.id === blockId);
  if (block) {
    const newCount = parseInt(count);
    const currentContent = block.content.content || [];
    
    // Ajustar el array de contenido
    if (newCount > currentContent.length) {
      // Agregar columnas vacías
      for (let i = currentContent.length; i < newCount; i++) {
        currentContent.push('Contenido de columna...');
      }
    } else if (newCount < currentContent.length) {
      // Remover columnas extras
      currentContent.splice(newCount);
    }
    
    block.content.columns = newCount;
    block.content.content = currentContent;
    
    renderCanvas();
    selectBlock(blockId);
  }
}

function updateColumnContent(blockId, columnIndex, content) {
  const block = currentBlocks.find(b => b.id === blockId);
  if (block && block.content.content) {
    block.content.content[columnIndex] = content;
    renderCanvas();
    selectBlock(blockId);
  }
}


// FUNCIÓN DE TEST PARA DRAG AND DROP
function testDragDrop() {
  console.log('\n=== 🧪 TEST COMPLETO DRAG AND DROP ===');
  
  const canvasArea = document.getElementById('canvas-area');
  const blockItems = document.querySelectorAll('.block-item');
  const addButtons = document.querySelectorAll('.add-block-btn');
  
  console.log('📍 Canvas area:', canvasArea);
  console.log('📦 Bloques encontrados:', blockItems.length);
  console.log('➕ Botones + encontrados:', addButtons.length);
  
  // Test detallado de cada bloque
  blockItems.forEach((item, i) => {
    const type = item.getAttribute('data-type');
    const draggable = item.getAttribute('draggable');
    const hasEvents = !!(item.ondragstart && item.ondragend);
    console.log(`🔸 Bloque ${i + 1}: ${type}, draggable=${draggable}, events=${hasEvents}`);
    
    // Test de eventos
    if (item.ondragstart) {
      console.log(`  ✅ ondragstart definido para ${type}`);
    } else {
      console.log(`  ❌ ondragstart NO definido para ${type}`);
    }
  });
  
  // Test del canvas
  if (canvasArea) {
    const hasDropEvents = !!(canvasArea.ondrop && canvasArea.ondragover);
    console.log('🎯 Canvas eventos:', hasDropEvents ? '✅ OK' : '❌ FALTAN');
    
    if (canvasArea.ondrop) console.log('  ✅ ondrop definido');
    else console.log('  ❌ ondrop NO definido');
    
    if (canvasArea.ondragover) console.log('  ✅ ondragover definido');
    else console.log('  ❌ ondragover NO definido');
  }
  
  // Test directo agregando un bloque
  console.log('🧪 Test directo: agregando bloque de texto...');
  addBlockToCanvas('text');
  
  console.log('✅ Test completado. Revisa la consola arriba.');
  
  // Mostrar alerta con diagnóstico
  const dragWorking = blockItems.length > 0 && blockItems[0].ondragstart && canvasArea && canvasArea.ondrop;
  
  alert(`🧪 DIAGNÓSTICO DRAG & DROP\n\n${dragWorking ? '✅ DRAG & DROP CONFIGURADO' : '❌ DRAG & DROP TIENE PROBLEMAS'}\n\nElementos encontrados:\n📦 Bloques: ${blockItems.length}\n🎯 Canvas: ${canvasArea ? 'OK' : 'NO'}\n➕ Botones +: ${addButtons.length}\n\n💡 ALTERNATIVAS:\n1. Botones verdes ➕ (funcionan siempre)\n2. Doble clic en bloques\n3. Botón Test D&D para agregar texto\n\nRevisa la consola (F12) para detalles.`);
}

// FUNCIÓN DE EMERGENCIA PARA AGREGAR BLOQUES
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

// FUNCIÓN PARA REINICIALIZAR DRAG AND DROP
function reinitializeDragDrop() {
  console.log('🔄 Reinicializando Drag and Drop...');
  
  // Limpiar eventos anteriores
  document.querySelectorAll('.block-item').forEach(item => {
    item.ondragstart = null;
    item.ondragend = null;
  });
  
  const canvasArea = document.getElementById('canvas-area');
  if (canvasArea) {
    canvasArea.ondragover = null;
    canvasArea.ondragenter = null;
    canvasArea.ondragleave = null;
    canvasArea.ondrop = null;
  }
  
  // Reinicializar
  setTimeout(() => {
    setupDragForBlocks();
    if (canvasArea) {
      setupDropForCanvas(canvasArea);
    }
    
    console.log('✅ Drag and Drop reinicializado');
    
    // Mostrar notificación
    const notification = document.createElement('div');
    notification.innerHTML = `🔄 Drag & Drop reinicializado`;
    notification.style.cssText = `
      position: fixed; top: 20px; right: 20px; z-index: 2000;
      background: var(--accent); color: white; padding: 12px 20px;
      border-radius: 8px; font-weight: 500; box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    `;
    document.body.appendChild(notification);
    
    setTimeout(() => notification.remove(), 3000);
  }, 100);
}

// Initialize
renderCanvas();
renderProperties();
</script>

</body>
</html>