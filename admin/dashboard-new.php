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

/* MAIN CONTENT */
.main { margin-left: 260px; min-height: 100vh; }
.header { 
  background: var(--bg-secondary); 
  padding: 20px 30px; 
  border-bottom: 1px solid var(--border); 
  display: flex; justify-content: space-between; align-items: center; 
}
.header h1 { margin: 0; font-size: 24px; }
.user-info { display: flex; align-items: center; gap: 15px; }
.btn { 
  padding: 10px 16px; border: none; border-radius: 6px; 
  cursor: pointer; font-weight: 500; transition: all 0.2s; 
  text-decoration: none; display: inline-flex; align-items: center; gap: 8px; 
}
.btn-primary { background: var(--accent); color: white; }
.btn-primary:hover { background: var(--accent-hover); }
.btn-success { background: var(--success); color: white; }
.btn-danger { background: var(--danger); color: white; }
.btn-warning { background: var(--warning); color: white; }

/* CONTENT AREA */
.content { padding: 30px; }
.section-card { 
  background: var(--bg-secondary); 
  border: 1px solid var(--border); 
  border-radius: 12px; 
  margin-bottom: 24px; 
}
.section-header { 
  padding: 20px; border-bottom: 1px solid var(--border); 
  display: flex; justify-content: space-between; align-items: center; 
}
.section-body { padding: 20px; }

/* FORM CONTROLS */
.form-row { 
  display: flex; gap: 20px; margin-bottom: 16px; 
  align-items: center; flex-wrap: wrap; 
}
.form-group { display: flex; flex-direction: column; gap: 6px; }
.form-group label { font-weight: 500; color: var(--text-secondary); }
.form-control { 
  padding: 10px 12px; border: 1px solid var(--border); 
  border-radius: 6px; background: var(--bg-primary); 
  color: var(--text-primary); 
}
.form-control:focus { outline: none; border-color: var(--accent); }

/* TOOLBAR */
.toolbar { 
  display: flex; gap: 8px; margin-bottom: 12px; 
  padding: 12px; background: var(--bg-primary); border-radius: 6px; 
}
.toolbar button { 
  background: var(--bg-card); border: 1px solid var(--border); 
  color: var(--text-primary); padding: 8px 12px; 
  border-radius: 4px; cursor: pointer; transition: all 0.2s; 
}
.toolbar button:hover { background: var(--accent); }
.editor { 
  min-height: 120px; padding: 12px; 
  border: 1px solid var(--border); border-radius: 6px; 
  background: var(--bg-primary); color: var(--text-primary); 
}
.editor[contenteditable]:focus { 
  outline: 2px solid var(--accent); outline-offset: 2px; 
}

/* COLLECTION GRID */
.collection-grid { 
  display: grid; 
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); 
  gap: 20px; 
}
.product-card { 
  background: var(--bg-primary); 
  border: 1px solid var(--border); 
  border-radius: 10px; overflow: hidden; 
}
.product-image { width: 100%; height: 180px; object-fit: cover; }
.product-body { padding: 16px; }
.product-title { 
  margin: 0 0 8px 0; font-size: 16px; font-weight: 600; 
}
.product-summary { 
  margin: 0 0 12px 0; color: var(--text-secondary); font-size: 14px; 
}
.product-actions { display: flex; gap: 8px; }
.btn-sm { padding: 6px 12px; font-size: 14px; }

/* DRAG & DROP */
.sortable { transition: all 0.3s; cursor: move; }
.sortable:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(0,0,0,0.3); }
.sortable.dragging { opacity: 0.7; transform: rotate(2deg) scale(1.02); }

/* MODAL */
.modal { 
  display: none; position: fixed; inset: 0; 
  background: rgba(0,0,0,0.8); z-index: 1000; 
  align-items: center; justify-content: center; padding: 20px; 
}
.modal.show { display: flex; }
.modal-content { 
  background: var(--bg-secondary); border-radius: 12px; 
  width: 100%; max-width: 600px; max-height: 90vh; overflow-y: auto;
  resize: both; min-width: 400px; min-height: 300px;
}
.modal-content.large {
  max-width: 95%; width: 1200px; height: 600px;
  resize: both; overflow: hidden;
}
.modal-header { 
  padding: 20px; border-bottom: 1px solid var(--border); 
  display: flex; justify-content: space-between; align-items: center; 
}
.modal-body { padding: 20px; }
.modal-footer { 
  padding: 20px; border-top: 1px solid var(--border); 
  display: flex; gap: 12px; justify-content: flex-end; 
}
.close-modal { 
  background: none; border: none; 
  color: var(--text-secondary); font-size: 24px; cursor: pointer; 
}

/* STATS */
.stats-grid { 
  display: grid; 
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); 
  gap: 20px; margin-bottom: 30px; 
}
.stat-card { 
  background: var(--bg-secondary); 
  border: 1px solid var(--border); 
  border-radius: 10px; padding: 20px; text-align: center; 
}
.stat-value { 
  font-size: 32px; font-weight: bold; color: var(--accent); 
}
.stat-label { color: var(--text-secondary); margin-top: 5px; }

/* COLOR SELECTOR */
.color-quick {
  width: 30px; height: 30px; border-radius: 6px; cursor: pointer;
  border: 2px solid transparent; transition: all 0.2s;
}
.color-quick:hover {
  transform: scale(1.1); border-color: var(--accent);
}
.color-quick.selected {
  border-color: var(--accent); transform: scale(1.1);
}

@media (max-width: 768px) {
  .sidebar { width: 100%; height: auto; position: relative; }
  .main { margin-left: 0; }
  .form-row { flex-direction: column; }
  .collection-grid { grid-template-columns: 1fr; }
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
    <a href="#dashboard" class="nav-item active">
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
    <div class="user-info">
      <button class="btn btn-success" onclick="guardarTodo()">
        <i class="fas fa-save"></i>
        Guardar Cambios
      </button>
      <span>Bienvenido, <?= h($_SESSION['usuario']) ?></span>
    </div>
  </div>

  <div class="content">
    <!-- STATS -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-value"><?= count($secciones) ?></div>
        <div class="stat-label">Secciones</div>
      </div>
      <div class="stat-card">
        <div class="stat-value"><?= count(glob(__DIR__ . '/../uploads/*')) ?></div>
        <div class="stat-label">Archivos</div>
      </div>
      <div class="stat-card">
        <div class="stat-value"><?= count(json_decode(file_get_contents(__DIR__ . '/mensajes.json') ?: '[]', true)) ?></div>
        <div class="stat-label">Mensajes</div>
      </div>
      <div class="stat-card">
        <div class="stat-value"><?= count($socials) ?></div>
        <div class="stat-label">Redes Sociales</div>
      </div>
    </div>

    <!-- VIDEO BANNER -->
    <div class="section-card">
      <div class="section-header">
        <h3><i class="fas fa-video"></i> Video Banner</h3>
        <button class="btn btn-primary" onclick="cambiarVideo()">
          <i class="fas fa-upload"></i>
          Cambiar Video
        </button>
      </div>
      <div class="section-body">
        <?php if ($video): ?>
          <video width="100%" height="200" controls style="border-radius: 8px;">
            <source src="../<?= h($video) ?>" type="video/mp4">
          </video>
        <?php endif; ?>
      </div>
    </div>

    <!-- SECCIONES -->
    <div id="secciones-container">
      <?php foreach ($secciones as $index => $seccion): ?>
        <div class="section-card sortable" data-section="<?= $index ?>">
          <div class="section-header">
            <h3>
              <i class="fas fa-grip-vertical" style="cursor: move; margin-right: 10px;"></i>
              <?= h($seccion['titulo'] ?? 'Sección') ?>
            </h3>
            <div>
              <button class="btn btn-danger btn-sm" onclick="eliminarSeccion(<?= $index ?>)">
                <i class="fas fa-trash"></i>
                Eliminar
              </button>
            </div>
          </div>
          <div class="section-body">
            <!-- CONTROLES DE SECCIÓN -->
            <div class="form-row">
              <div class="form-group" style="flex: 2;">
                <label>Título:</label>
                <input type="text" class="form-control" value="<?= h($seccion['titulo'] ?? '') ?>" onchange="actualizarSeccion(<?= $index ?>, 'titulo', this.value)">
              </div>
              <div class="form-group">
                <label>Tipo:</label>
                <select class="form-control" onchange="actualizarSeccion(<?= $index ?>, 'tipo', this.value)">
                  <option value="texto" <?= ($seccion['tipo'] ?? '') === 'texto' ? 'selected' : '' ?>>Texto</option>
                  <option value="coleccion" <?= ($seccion['tipo'] ?? '') === 'coleccion' ? 'selected' : '' ?>>Colección</option>
                </select>
              </div>
              <div class="form-group">
                <label>Color fondo:</label>
                <input type="color" class="form-control" value="<?= h($seccion['bg'] ?? '#ffffff') ?>" onchange="actualizarSeccion(<?= $index ?>, 'bg', this.value)">
              </div>
              <div class="form-group">
                <label>En menú:</label>
                <select class="form-control" onchange="actualizarSeccion(<?= $index ?>, 'show_in_menu', this.value === '1')">
                  <option value="1" <?= !empty($seccion['show_in_menu']) ? 'selected' : '' ?>>Sí</option>
                  <option value="0" <?= empty($seccion['show_in_menu']) ? 'selected' : '' ?>>No</option>
                </select>
              </div>
            </div>

            <?php if (($seccion['tipo'] ?? '') === 'coleccion'): ?>
              <!-- COLECCIÓN DE PRODUCTOS -->
              <div class="collection-grid">
                <?php foreach (($seccion['columns'] ?? []) as $i => $producto): ?>
                  <div class="product-card">
                    <?php if (!empty($producto['imagen'])): ?>
                      <img src="../<?= h($producto['imagen']) ?>" alt="" class="product-image">
                    <?php endif; ?>
                    <div class="product-body">
                      <h4 class="product-title"><?= h($producto['titulo'] ?? '') ?></h4>
                      <p class="product-summary"><?= h($producto['resumen'] ?? '') ?></p>
                      <div class="product-actions">
                        <button class="btn btn-primary btn-sm" onclick="editarProducto(<?= $index ?>, <?= $i ?>)">
                          <i class="fas fa-edit"></i>
                          Editar
                        </button>
                        <button class="btn btn-warning btn-sm" onclick="verDetalleProducto(<?= $index ?>, <?= $i ?>)">
                          <i class="fas fa-eye"></i>
                          Ver más
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="eliminarProducto(<?= $index ?>, <?= $i ?>)">
                          <i class="fas fa-trash"></i>
                          Eliminar
                        </button>
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>
                
                <div class="product-card" style="display: flex; align-items: center; justify-content: center; min-height: 200px; border: 2px dashed var(--border); cursor: pointer;" onclick="agregarProducto(<?= $index ?>)">
                  <div style="text-align: center; color: var(--text-secondary);">
                    <i class="fas fa-plus" style="font-size: 32px; margin-bottom: 10px;"></i>
                    <div>Agregar Producto</div>
                  </div>
                </div>
              </div>
              
            <?php else: ?>
              <!-- SECCIÓN DE TEXTO -->
              <div class="toolbar">
                <button type="button" onclick="formatText('bold')"><b>B</b></button>
                <button type="button" onclick="formatText('italic')"><i>I</i></button>
                <button type="button" onclick="formatText('underline')"><u>U</u></button>
                <button type="button" onclick="formatText('justifyLeft')">
                  <i class="fas fa-align-left"></i>
                </button>
                <button type="button" onclick="formatText('justifyCenter')">
                  <i class="fas fa-align-center"></i>
                </button>
                <button type="button" onclick="formatText('justifyRight')">
                  <i class="fas fa-align-right"></i>
                </button>
                <button type="button" onclick="formatText('insertUnorderedList')">
                  <i class="fas fa-list-ul"></i>
                </button>
                <button type="button" onclick="abrirEditorTexto(<?= $index ?>)">
                  <i class="fas fa-palette"></i>
                  Editor
                </button>
              </div>
              <div class="editor" 
                   contenteditable="true" 
                   data-section="<?= $index ?>"
                   onblur="actualizarContenido(<?= $index ?>, this.innerHTML)"><?= $seccion['contenido']['html'] ?? '' ?></div>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <!-- SECCIÓN CONTACTO -->
    <div class="section-card">
      <div class="section-header">
        <h3><i class="fas fa-envelope"></i> Formulario de Contacto</h3>
        <button class="btn btn-primary" onclick="editarContacto()">
          <i class="fas fa-edit"></i>
          Editar
        </button>
      </div>
      <div class="section-body">
        <div class="form-row">
          <div class="form-group" style="flex: 2;">
            <label>Título de Contacto:</label>
            <input type="text" class="form-control" value="<?= h($contacto['titulo'] ?? 'Contacto') ?>" onchange="actualizarContacto('titulo', this.value)">
          </div>
          <div class="form-group">
            <label>Color de fondo:</label>
            <input type="color" class="form-control" value="<?= h($contacto['bg'] ?? '#ffffff') ?>" onchange="actualizarContacto('bg', this.value)">
          </div>
          <div class="form-group">
            <label>Email destino:</label>
            <input type="email" class="form-control" value="<?= h($contacto['email'] ?? '') ?>" onchange="actualizarContacto('email', this.value)">
          </div>
        </div>
        <div class="form-group">
          <label>Texto de contacto:</label>
          <textarea class="form-control" rows="3" onchange="actualizarContacto('contenido', this.value)"><?= h($contacto['contenido'] ?? '') ?></textarea>
        </div>
        
        <h4 style="margin: 20px 0 10px 0; color: var(--text-secondary);">Campos del Formulario:</h4>
        <div id="form-fields">
          <?php $fields = $data['form']['fields'] ?? []; foreach($fields as $i => $field): ?>
          <div class="form-row">
            <div class="form-group">
              <label>Campo <?= $i+1 ?> - Nombre:</label>
              <input type="text" class="form-control" value="<?= h($field['name'] ?? '') ?>" onchange="actualizarFormField(<?= $i ?>, 'name', this.value)">
            </div>
            <div class="form-group" style="flex: 2;">
              <label>Placeholder:</label>
              <input type="text" class="form-control" value="<?= h($field['placeholder'] ?? '') ?>" onchange="actualizarFormField(<?= $i ?>, 'placeholder', this.value)">
            </div>
            <div class="form-group">
              <button class="btn btn-danger btn-sm" onclick="eliminarFormField(<?= $i ?>)">
                <i class="fas fa-trash"></i>
              </button>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
        <button class="btn btn-success btn-sm" onclick="agregarFormField()">
          <i class="fas fa-plus"></i> Agregar Campo
        </button>
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
              <h4 class="product-title"><?= h($red['nombre'] ?? '') ?> <?= $red['icono'] ?? '' ?></h4>
              <p class="product-summary"><?= h($red['url'] ?? '') ?></p>
              <div class="product-actions">
                <button class="btn btn-primary btn-sm" onclick="editarRedSocial(<?= $i ?>)">
                  <i class="fas fa-edit"></i>
                  Editar
                </button>
                <button class="btn btn-danger btn-sm" onclick="eliminarRedSocial(<?= $i ?>)">
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

    <!-- PIE DE PÁGINA -->
    <div class="section-card">
      <div class="section-header">
        <h3><i class="fas fa-copyright"></i> Pie de Página (Footer)</h3>
      </div>
      <div class="section-body">
        <?php $footer = $data['footer'] ?? []; ?>
        <div class="form-row">
          <div class="form-group" style="flex: 2;">
            <label>Dirección:</label>
            <input type="text" class="form-control" value="<?= h($footer['direccion'] ?? '') ?>" onchange="actualizarFooter('direccion', this.value)">
          </div>
          <div class="form-group">
            <label>Teléfono:</label>
            <input type="text" class="form-control" value="<?= h($footer['telefono'] ?? '') ?>" onchange="actualizarFooter('telefono', this.value)">
          </div>
          <div class="form-group">
            <label>Email:</label>
            <input type="email" class="form-control" value="<?= h($footer['email'] ?? '') ?>" onchange="actualizarFooter('email', this.value)">
          </div>
        </div>
        <div class="form-group">
          <label>Texto de copyright:</label>
          <input type="text" class="form-control" value="<?= h($footer['texto'] ?? '') ?>" onchange="actualizarFooter('texto', this.value)">
        </div>
      </div>
    </div>

    <!-- FUENTES GOOGLE -->
    <div class="section-card">
      <div class="section-header">
        <h3><i class="fas fa-font"></i> Fuentes Google</h3>
      </div>
      <div class="section-body">
        <?php $fonts = $data['fonts'] ?? []; ?>
        <div class="form-row">
          <div class="form-group">
            <label>Fuente del cuerpo:</label>
            <input type="text" class="form-control" value="<?= h($fonts['body']['family'] ?? 'Inter') ?>" onchange="actualizarFont('body', 'family', this.value)">
          </div>
          <div class="form-group">
            <label>Fuente de títulos:</label>
            <input type="text" class="form-control" value="<?= h($fonts['headings']['family'] ?? 'Poppins') ?>" onchange="actualizarFont('headings', 'family', this.value)">
          </div>
          <div class="form-group">
            <label>Fuente de firma:</label>
            <input type="text" class="form-control" value="<?= h($fonts['signature']['family'] ?? 'Great Vibes') ?>" onchange="actualizarFont('signature', 'family', this.value)">
          </div>
        </div>
      </div>
    </div>

    <!-- BOTÓN AGREGAR SECCIÓN -->
    <div class="section-card" style="border: 2px dashed var(--border); text-align: center; cursor: pointer;" onclick="agregarSeccion()">
      <div class="section-body">
        <i class="fas fa-plus" style="font-size: 32px; color: var(--accent); margin-bottom: 10px;"></i>
        <h3 style="color: var(--text-secondary); margin: 0;">Agregar Nueva Sección</h3>
      </div>
    </div>
  </div>
</div>

<!-- MODAL EDITAR PRODUCTO -->
<div id="modalEditarProducto" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <h3>Editar Producto</h3>
      <button class="close-modal" onclick="cerrarModal('modalEditarProducto')">&times;</button>
    </div>
    <div class="modal-body">
      <form id="formEditarProducto">
        <input type="hidden" id="editSectionIndex">
        <input type="hidden" id="editProductIndex">
        
        <div class="form-group">
          <label>Título del producto:</label>
          <input type="text" id="editTitulo" class="form-control" placeholder="Ej: Lentes Visión Sencilla">
        </div>
        
        <div class="form-group">
          <label>Imagen actual:</label>
          <div id="editImagenPreview" style="margin: 10px 0;"></div>
          <div style="display: flex; gap: 10px; align-items: end;">
            <div style="flex: 1;">
              <input type="text" id="editImagen" class="form-control" placeholder="uploads/imagen.jpg" onchange="actualizarPreviewImagen()">
              <small style="color: var(--text-secondary);">Ruta de la imagen en la carpeta uploads/</small>
            </div>
            <button type="button" class="btn btn-primary" onclick="seleccionarImagen()">
              <i class="fas fa-image"></i>
              Cambiar
            </button>
          </div>
        </div>
        
        <div class="form-group">
          <label>Resumen (descripción corta):</label>
          <textarea id="editResumen" class="form-control" rows="2" placeholder="Descripción breve para la tarjeta"></textarea>
        </div>
        
        <div class="form-group">
          <label>Detalle completo (para el modal):</label>
          <div class="toolbar">
            <button type="button" onclick="formatModalText('bold')"><b>B</b></button>
            <button type="button" onclick="formatModalText('italic')"><i>I</i></button>
            <button type="button" onclick="formatModalText('underline')"><u>U</u></button>
          </div>
          <div id="editDetalle" class="editor" contenteditable="true" style="min-height: 100px;"></div>
        </div>
      </form>
    </div>
    <div class="modal-footer">
      <button class="btn btn-primary" onclick="guardarProducto()">Guardar Cambios</button>
      <button class="btn" onclick="cerrarModal('modalEditarProducto')">Cancelar</button>
    </div>
  </div>
</div>

<!-- MODAL VER DETALLE PRODUCTO -->
<div id="modalDetalleProducto" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <h3 id="detalleTitulo">Título del Producto</h3>
      <button class="close-modal" onclick="cerrarModal('modalDetalleProducto')">&times;</button>
    </div>
    <div class="modal-body">
      <div id="detalleImagenContainer"></div>
      <div id="detalleContenido"></div>
    </div>
    <div class="modal-footer">
      <button class="btn" onclick="cerrarModal('modalDetalleProducto')">Cerrar</button>
    </div>
  </div>
</div>

<!-- MODAL SELECTOR DE IMÁGENES -->
<div id="modalSelectorImagen" class="modal">
  <div class="modal-content" style="max-width: 800px;">
    <div class="modal-header">
      <h3>Seleccionar Imagen</h3>
      <button class="close-modal" onclick="cerrarModal('modalSelectorImagen')">&times;</button>
    </div>
    <div class="modal-body">
      <div style="margin-bottom: 15px;">
        <input type="text" id="filtroImagen" class="form-control" placeholder="Buscar imagen..." onkeyup="filtrarImagenes()">
      </div>
      <div id="galeriaImagenes" class="collection-grid" style="max-height: 400px; overflow-y: auto;">
        <!-- Se llena dinámicamente con las imágenes -->
      </div>
      <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid var(--border);">
        <label><strong>O escribir ruta manualmente:</strong></label>
        <input type="text" id="rutaManual" class="form-control" placeholder="uploads/nueva-imagen.jpg">
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-primary" onclick="confirmarSeleccionImagen()">Seleccionar</button>
      <button class="btn" onclick="cerrarModal('modalSelectorImagen')">Cancelar</button>
    </div>
  </div>
</div>

<!-- MODAL SELECTOR DE COLOR Y FUENTE -->
<div id="modalColorFuente" class="modal">
  <div class="modal-content large">
    <div class="modal-header">
      <h3>Formato de Texto</h3>
      <button class="close-modal" onclick="cerrarModal('modalColorFuente')">&times;</button>
    </div>
    <div class="modal-body">
      <div style="display: grid; grid-template-columns: 350px 1fr; gap: 30px; height: 500px;">
        <!-- COLUMNA IZQUIERDA: CONTROLES -->
        <div>
          <!-- SELECTOR DE COLOR -->
          <div class="form-group">
            <label><strong>Color del texto:</strong></label>
            <div style="display: flex; gap: 15px; align-items: center; margin-top: 10px;">
              <input type="color" id="colorPicker" class="form-control" style="width: 60px; height: 40px; padding: 5px;" value="#3182ce">
              <input type="text" id="colorHex" class="form-control" style="width: 100px;" placeholder="#3182ce">
              <div id="colorPreview" style="width: 40px; height: 40px; border-radius: 8px; border: 1px solid var(--border); background: #3182ce;"></div>
            </div>
            <div style="margin-top: 10px;">
              <strong>Colores rápidos:</strong>
              <div style="display: flex; gap: 8px; margin-top: 8px; flex-wrap: wrap;">
                <div class="color-quick" style="background: #3182ce;" onclick="seleccionarColorRapido('#3182ce')"></div>
                <div class="color-quick" style="background: #e53e3e;" onclick="seleccionarColorRapido('#e53e3e')"></div>
                <div class="color-quick" style="background: #38a169;" onclick="seleccionarColorRapido('#38a169')"></div>
                <div class="color-quick" style="background: #d69e2e;" onclick="seleccionarColorRapido('#d69e2e')"></div>
                <div class="color-quick" style="background: #805ad5;" onclick="seleccionarColorRapido('#805ad5')"></div>
                <div class="color-quick" style="background: #dd6b20;" onclick="seleccionarColorRapido('#dd6b20')"></div>
                <div class="color-quick" style="background: #000000;" onclick="seleccionarColorRapido('#000000')"></div>
                <div class="color-quick" style="background: #ffffff; border: 2px solid #ccc;" onclick="seleccionarColorRapido('#ffffff')"></div>
              </div>
            </div>
          </div>
          
          <!-- SELECTOR DE FUENTE -->
          <div class="form-group" style="margin-top: 20px;">
            <label><strong>Fuente:</strong></label>
            <select id="fontSelector" class="form-control" onchange="previsualizarFuente()">
              <option value="inherit">Fuente por defecto</option>
              <optgroup label="Fuentes del sistema">
                <option value="Arial">Arial</option>
                <option value="Times New Roman">Times New Roman</option>
                <option value="Helvetica">Helvetica</option>
                <option value="Georgia">Georgia</option>
                <option value="Verdana">Verdana</option>
                <option value="Courier New">Courier New</option>
              </optgroup>
              <optgroup label="Fuentes Google (sitio web)">
                <option value="Inter">Inter (Cuerpo actual)</option>
                <option value="Poppins">Poppins (Títulos actuales)</option>
                <option value="Great Vibes">Great Vibes (Firma actual)</option>
                <option value="Roboto">Roboto</option>
                <option value="Open Sans">Open Sans</option>
                <option value="Lato">Lato</option>
                <option value="Montserrat">Montserrat</option>
                <option value="Nunito">Nunito</option>
              </optgroup>
            </select>
          </div>
          
          <!-- TAMAÑO DE FUENTE -->
          <div class="form-group" style="margin-top: 15px;">
            <label><strong>Tamaño:</strong></label>
            <div style="display: flex; gap: 10px; align-items: center;">
              <input type="range" id="fontSizeRange" min="10" max="48" value="14" class="form-control" style="flex: 1;" onchange="actualizarTamanoFuente()">
              <input type="number" id="fontSizeNumber" min="10" max="72" value="14" class="form-control" style="width: 70px;" onchange="actualizarTamanoFuenteManual()">px
            </div>
          </div>
        </div>
        
        <!-- COLUMNA DERECHA: EDITOR Y PREVIEW -->
        <div style="display: flex; flex-direction: column; height: 100%;">
          <!-- EDITOR DE TEXTO -->
          <div class="form-group" style="flex: 1;">
            <label><strong>Editar texto:</strong></label>
            <div class="toolbar" style="margin-top: 8px;">
              <button type="button" onclick="aplicarFormatoEditor('bold')"><b>B</b></button>
              <button type="button" onclick="aplicarFormatoEditor('italic')"><i>I</i></button>
              <button type="button" onclick="aplicarFormatoEditor('underline')"><u>U</u></button>
              <button type="button" onclick="aplicarFormatoEditor('justifyLeft')">
                <i class="fas fa-align-left"></i>
              </button>
              <button type="button" onclick="aplicarFormatoEditor('justifyCenter')">
                <i class="fas fa-align-center"></i>
              </button>
              <button type="button" onclick="aplicarFormatoEditor('justifyRight')">
                <i class="fas fa-align-right"></i>
              </button>
              <button type="button" onclick="aplicarFormatoEditor('insertUnorderedList')">
                <i class="fas fa-list-ul"></i>
              </button>
            </div>
            <div id="editorTextoModal" class="editor" contenteditable="true" style="height: 200px; margin-top: 8px;">
              <!-- El contenido se carga dinámicamente -->
            </div>
          </div>
          
          <!-- PREVIEW -->
          <div class="form-group" style="margin-top: 15px;">
            <label><strong>Vista previa con formato:</strong></label>
            <div id="textPreview" style="padding: 15px; border: 1px solid var(--border); border-radius: 8px; background: var(--bg-primary); color: #3182ce; font-family: inherit; font-size: 14px; margin-top: 8px; height: 150px; overflow-y: auto;">
              <!-- Se actualiza automáticamente -->
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-primary" onclick="aplicarFormatoTexto()">Aplicar Formato</button>
      <button class="btn" onclick="cerrarModal('modalColorFuente')">Cancelar</button>
    </div>
  </div>
</div>

<script>
let dataSecciones = <?= json_encode($secciones) ?>;
let dataContacto = <?= json_encode($contacto) ?>;
let dataForm = <?= json_encode($data['form'] ?? []) ?>;
let dataSocials = <?= json_encode($socials) ?>;
let dataFooter = <?= json_encode($data['footer'] ?? []) ?>;
let dataFonts = <?= json_encode($data['fonts'] ?? []) ?>;

// FUNCIONES PRINCIPALES
function guardarTodo() {
  fetch('save-data.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ 
      secciones: dataSecciones,
      contacto: dataContacto,
      form: dataForm,
      socials: dataSocials,
      footer: dataFooter,
      fonts: dataFonts
    })
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      alert('✅ Cambios guardados correctamente');
      location.reload();
    } else {
      alert('❌ Error al guardar: ' + data.error);
    }
  });
}

function actualizarSeccion(index, field, value) {
  if (!dataSecciones[index]) dataSecciones[index] = {};
  dataSecciones[index][field] = value;
}

function actualizarContenido(index, html) {
  if (!dataSecciones[index].contenido) dataSecciones[index].contenido = {};
  dataSecciones[index].contenido.html = html;
}

function formatText(command) {
  document.execCommand(command, false, null);
}

// Variable global para saber qué sección se está editando
let seccionEditandose = null;

function abrirEditorTexto(sectionIndex) {
  seccionEditandose = sectionIndex;
  const seccion = dataSecciones[sectionIndex];
  
  // Cargar contenido actual en el editor del modal
  const editorModal = document.getElementById('editorTextoModal');
  editorModal.innerHTML = seccion.contenido?.html || '';
  
  // Actualizar preview inicial
  actualizarVistaPrevia();
  
  // Abrir modal
  document.getElementById('modalColorFuente').classList.add('show');
}

function cambiarColor() {
  // Función legacy - ahora usa abrirEditorTexto
  if (seccionEditandose !== null) {
    abrirEditorTexto(seccionEditandose);
  } else {
    document.getElementById('modalColorFuente').classList.add('show');
  }
}

function agregarSeccion() {
  const titulo = prompt('Título de la nueva sección:', 'Nueva Sección');
  if (titulo) {
    dataSecciones.push({
      id: titulo.toLowerCase().replace(/\s+/g, '-'),
      titulo: titulo,
      tipo: 'texto',
      bg: '#ffffff',
      border: 1,
      contenido: { html: '' },
      show_in_menu: true
    });
    location.reload();
  }
}

function eliminarSeccion(index) {
  const seccion = dataSecciones[index];
  const titulo = seccion?.titulo || 'esta sección';
  
  if (confirm(`⚠️ ¿Estás seguro de ELIMINAR "${titulo}"?\n\n🚨 Esta acción NO se puede deshacer.\n📄 Se perderá todo el contenido de la sección.\n\n✅ Clic OK para confirmar\n❌ Clic Cancelar para conservar`)) {
    dataSecciones.splice(index, 1);
    alert(`✅ Sección "${titulo}" eliminada correctamente.\n\n💾 No olvides hacer clic en "Guardar Cambios" para confirmar.`);
    location.reload();
  }
}

function agregarProducto(sectionIndex) {
  const titulo = prompt('Título del producto:', '');
  if (titulo) {
    if (!dataSecciones[sectionIndex].columns) dataSecciones[sectionIndex].columns = [];
    dataSecciones[sectionIndex].columns.push({
      titulo: titulo,
      imagen: '',
      resumen: '',
      detalle: ''
    });
    location.reload();
  }
}

function editarProducto(sectionIndex, productIndex) {
  const producto = dataSecciones[sectionIndex].columns[productIndex];
  
  // Llenar el modal con los datos actuales
  document.getElementById('editSectionIndex').value = sectionIndex;
  document.getElementById('editProductIndex').value = productIndex;
  document.getElementById('editTitulo').value = producto.titulo || '';
  document.getElementById('editImagen').value = producto.imagen || '';
  document.getElementById('editResumen').value = producto.resumen || '';
  document.getElementById('editDetalle').innerHTML = producto.detalle || '';
  
  // Mostrar preview de imagen si existe
  const previewContainer = document.getElementById('editImagenPreview');
  if (producto.imagen) {
    previewContainer.innerHTML = `<img src="../${producto.imagen}" style="max-width: 200px; border-radius: 8px;">`;
  } else {
    previewContainer.innerHTML = '<p style="color: var(--text-secondary);">Sin imagen</p>';
  }
  
  // Mostrar modal
  document.getElementById('modalEditarProducto').classList.add('show');
}

function guardarProducto() {
  const sectionIndex = parseInt(document.getElementById('editSectionIndex').value);
  const productIndex = parseInt(document.getElementById('editProductIndex').value);
  
  const titulo = document.getElementById('editTitulo').value;
  const imagen = document.getElementById('editImagen').value;
  const resumen = document.getElementById('editResumen').value;
  const detalle = document.getElementById('editDetalle').innerHTML;
  
  if (!titulo.trim()) {
    alert('El título es obligatorio');
    return;
  }
  
  // Actualizar el producto en el array
  dataSecciones[sectionIndex].columns[productIndex] = {
    titulo: titulo,
    imagen: imagen,
    resumen: resumen,
    detalle: detalle
  };
  
  // Cerrar modal y recargar
  cerrarModal('modalEditarProducto');
  location.reload();
}

function eliminarProducto(sectionIndex, productIndex) {
  const producto = dataSecciones[sectionIndex].columns[productIndex];
  const titulo = producto?.titulo || 'este producto';
  
  if (confirm(`⚠️ ¿Eliminar el producto "${titulo}"?\n\n🚨 Se perderán la imagen, descripción y todo el contenido.\n\n✅ OK para eliminar\n❌ Cancelar para conservar`)) {
    dataSecciones[sectionIndex].columns.splice(productIndex, 1);
    alert(`✅ Producto "${titulo}" eliminado.\n\n💾 Recuerda hacer clic en "Guardar Cambios".`);
    location.reload();
  }
}

function verDetalleProducto(sectionIndex, productIndex) {
  const producto = dataSecciones[sectionIndex].columns[productIndex];
  
  // Llenar modal de detalle
  document.getElementById('detalleTitulo').textContent = producto.titulo || 'Producto';
  
  const imagenContainer = document.getElementById('detalleImagenContainer');
  if (producto.imagen) {
    imagenContainer.innerHTML = `<img src="../${producto.imagen}" style="width: 100%; max-width: 400px; border-radius: 10px; margin-bottom: 15px;">`;
  } else {
    imagenContainer.innerHTML = '';
  }
  
  document.getElementById('detalleContenido').innerHTML = `
    <div style="margin-bottom: 15px;">
      <strong>Resumen:</strong><br>
      ${producto.resumen || 'Sin resumen'}
    </div>
    <div>
      <strong>Detalle completo:</strong><br>
      ${producto.detalle || 'Sin detalle'}
    </div>
  `;
  
  // Mostrar modal
  document.getElementById('modalDetalleProducto').classList.add('show');
}

function cerrarModal(modalId) {
  document.getElementById(modalId).classList.remove('show');
}

function formatModalText(command) {
  document.execCommand(command, false, null);
}

// FUNCIONES PARA SELECTOR DE IMÁGENES
let imagenSeleccionada = '';

function seleccionarImagen() {
  // Cargar galería de imágenes
  fetch('get-images.php')
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        mostrarGaleriaImagenes(data.images);
        document.getElementById('modalSelectorImagen').classList.add('show');
      } else {
        alert('Error al cargar imágenes: ' + data.error);
      }
    })
    .catch(error => {
      alert('Error de conexión: ' + error);
    });
}

function mostrarGaleriaImagenes(images) {
  const galeria = document.getElementById('galeriaImagenes');
  galeria.innerHTML = '';
  
  if (images.length === 0) {
    galeria.innerHTML = '<p style="text-align: center; color: var(--text-secondary); padding: 20px;">No hay imágenes en la carpeta uploads/</p>';
    return;
  }
  
  images.forEach(img => {
    const card = document.createElement('div');
    card.className = 'product-card';
    card.style.cursor = 'pointer';
    card.onclick = () => seleccionarImagenItem(img.path);
    
    card.innerHTML = `
      <img src="../${img.path}" alt="${img.filename}" 
           style="width: 100%; height: 150px; object-fit: cover;" 
           onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjQiIGhlaWdodD0iMjQiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3QgeD0iMyIgeT0iMyIgd2lkdGg9IjE4IiBoZWlnaHQ9IjE4IiByeD0iMiIgcnk9IjIiIHN0cm9rZT0iIzk5OTk5OSIgc3Ryb2tlLXdpZHRoPSIyIiBmaWxsPSJub25lIi8+CjxjaXJjbGUgY3g9IjguNSIgY3k9IjguNSIgcj0iMS41IiBzdHJva2U9IiM5OTk5OTkiIHN0cm9rZS13aWR0aD0iMiIgZmlsbD0ibm9uZSIvPgo8cGF0aCBkPSJtOSAyMSAzLTMgMi0yIDMgMyIgc3Ryb2tlPSIjOTk5OTk5IiBzdHJva2Utd2lkdGg9IjIiIHN0cm9rZS1saW5lY2FwPSJyb3VuZCIgc3Ryb2tlLWxpbmVqb2luPSJyb3VuZCIvPgo8L3N2Zz4K'">
      <div style="padding: 10px;">
        <div style="font-weight: 600; font-size: 12px; margin-bottom: 4px;">${img.filename}</div>
        <div style="font-size: 11px; color: var(--text-secondary);">${formatFileSize(img.size)}</div>
      </div>
    `;
    
    galeria.appendChild(card);
  });
}

function seleccionarImagenItem(path) {
  imagenSeleccionada = path;
  document.getElementById('rutaManual').value = path;
  
  // Resaltar imagen seleccionada
  document.querySelectorAll('#galeriaImagenes .product-card').forEach(card => {
    card.style.border = '1px solid var(--border)';
  });
  event.target.closest('.product-card').style.border = '2px solid var(--accent)';
}

function confirmarSeleccionImagen() {
  const rutaFinal = document.getElementById('rutaManual').value.trim();
  if (!rutaFinal) {
    alert('Selecciona una imagen o escribe la ruta');
    return;
  }
  
  // Actualizar campo y preview
  document.getElementById('editImagen').value = rutaFinal;
  actualizarPreviewImagen();
  
  cerrarModal('modalSelectorImagen');
}

function actualizarPreviewImagen() {
  const rutaImagen = document.getElementById('editImagen').value;
  const previewContainer = document.getElementById('editImagenPreview');
  
  if (rutaImagen.trim()) {
    previewContainer.innerHTML = `<img src="../${rutaImagen}" style="max-width: 200px; border-radius: 8px;" onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjEwMCIgdmlld0JveD0iMCAwIDIwMCAxMDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSIyMDAiIGhlaWdodD0iMTAwIiBmaWxsPSIjMzMzMzMzIi8+Cjx0ZXh0IHg9IjEwMCIgeT0iNTAiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIxMiIgZmlsbD0iIzk5OTk5OSIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iPkltYWdlbiBubyBlbmNvbnRyYWRhPC90ZXh0Pgo8L3N2Zz4K'">`;
  } else {
    previewContainer.innerHTML = '<p style="color: var(--text-secondary);">Sin imagen</p>';
  }
}

function filtrarImagenes() {
  const filtro = document.getElementById('filtroImagen').value.toLowerCase();
  const cards = document.querySelectorAll('#galeriaImagenes .product-card');
  
  cards.forEach(card => {
    const filename = card.querySelector('div div').textContent.toLowerCase();
    card.style.display = filename.includes(filtro) ? 'block' : 'none';
  });
}

function formatFileSize(bytes) {
  if (bytes === 0) return '0 Bytes';
  const k = 1024;
  const sizes = ['Bytes', 'KB', 'MB', 'GB'];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

// FUNCIONES PARA MODAL DE COLOR Y FUENTE
function seleccionarColorRapido(color) {
  document.getElementById('colorPicker').value = color;
  document.getElementById('colorHex').value = color;
  document.getElementById('colorPreview').style.background = color;
  actualizarVistaPrevia();
  
  // Resaltar color seleccionado
  document.querySelectorAll('.color-quick').forEach(el => el.classList.remove('selected'));
  event.target.classList.add('selected');
}

function previsualizarFuente() {
  actualizarVistaPrevia();
}

function actualizarTamanoFuente() {
  const size = document.getElementById('fontSizeRange').value;
  document.getElementById('fontSizeNumber').value = size;
  actualizarVistaPrevia();
}

function actualizarTamanoFuenteManual() {
  const size = document.getElementById('fontSizeNumber').value;
  document.getElementById('fontSizeRange').value = size;
  actualizarVistaPrevia();
}

function aplicarFormatoEditor(command) {
  // Enfocar el editor del modal
  const editor = document.getElementById('editorTextoModal');
  editor.focus();
  
  // Aplicar comando
  document.execCommand(command, false, null);
  
  // Actualizar preview después de un breve delay
  setTimeout(actualizarVistaPrevia, 100);
}

function actualizarVistaPrevia() {
  const color = document.getElementById('colorPicker')?.value || '#3182ce';
  const font = document.getElementById('fontSelector')?.value || 'inherit';
  const size = document.getElementById('fontSizeRange')?.value || '14';
  const preview = document.getElementById('textPreview');
  const editorModal = document.getElementById('editorTextoModal');
  
  // Actualizar controles si existen
  const colorHex = document.getElementById('colorHex');
  const colorPreview = document.getElementById('colorPreview');
  if (colorHex) colorHex.value = color;
  if (colorPreview) colorPreview.style.background = color;
  
  // Copiar contenido del editor al preview
  if (editorModal && preview) {
    preview.innerHTML = editorModal.innerHTML;
    
    // Aplicar estilos globales al preview
    preview.style.color = color;
    preview.style.fontFamily = font === 'inherit' ? 'inherit' : font;
    preview.style.fontSize = size + 'px';
  }
}

// Actualizar preview cuando se escriba en el editor
document.addEventListener('DOMContentLoaded', function() {
  const editorModal = document.getElementById('editorTextoModal');
  if (editorModal) {
    editorModal.addEventListener('input', actualizarVistaPrevia);
    editorModal.addEventListener('keyup', actualizarVistaPrevia);
  }
});

function aplicarFormatoTexto() {
  if (seccionEditandose === null) {
    alert('Error: No hay sección seleccionada');
    return;
  }
  
  // Obtener contenido del editor del modal
  const editorModal = document.getElementById('editorTextoModal');
  const contenidoEditado = editorModal.innerHTML;
  
  // Actualizar la sección en el array
  if (!dataSecciones[seccionEditandose].contenido) {
    dataSecciones[seccionEditandose].contenido = {};
  }
  dataSecciones[seccionEditandose].contenido.html = contenidoEditado;
  
  // Actualizar el editor principal también
  const editorPrincipal = document.querySelector(`[data-section="${seccionEditandose}"]`);
  if (editorPrincipal) {
    editorPrincipal.innerHTML = contenidoEditado;
  }
  
  // Cerrar modal
  cerrarModal('modalColorFuente');
  
  // Mostrar mensaje de confirmación
  alert('✅ Texto actualizado. No olvides hacer clic en "Guardar Cambios" en el header.');
}

// Sincronizar color picker con hex input
document.addEventListener('DOMContentLoaded', function() {
  const colorPicker = document.getElementById('colorPicker');
  const colorHex = document.getElementById('colorHex');
  
  if (colorPicker && colorHex) {
    colorPicker.addEventListener('input', actualizarVistaPrevia);
    colorHex.addEventListener('input', function() {
      const hexValue = this.value;
      if (/^#[0-9A-F]{6}$/i.test(hexValue)) {
        colorPicker.value = hexValue;
        actualizarVistaPrevia();
      }
    });
  }
});

// DRAG & DROP PARA SECCIONES
let draggedElement = null;

document.querySelectorAll('.sortable').forEach(el => {
  el.draggable = true;
  
  el.addEventListener('dragstart', (e) => {
    draggedElement = el;
    el.classList.add('dragging');
  });
  
  el.addEventListener('dragend', (e) => {
    el.classList.remove('dragging');
    draggedElement = null;
  });
  
  el.addEventListener('dragover', (e) => {
    e.preventDefault();
  });
  
  el.addEventListener('drop', (e) => {
    e.preventDefault();
    if (draggedElement && draggedElement !== el) {
      const container = document.getElementById('secciones-container');
      const draggedIndex = Array.from(container.children).indexOf(draggedElement);
      const targetIndex = Array.from(container.children).indexOf(el);
      
      // Reordenar en el array
      const item = dataSecciones.splice(draggedIndex, 1)[0];
      dataSecciones.splice(targetIndex, 0, item);
      
      // Reordenar en el DOM
      if (targetIndex > draggedIndex) {
        el.parentNode.insertBefore(draggedElement, el.nextSibling);
      } else {
        el.parentNode.insertBefore(draggedElement, el);
      }
    }
  });
});

// FUNCIONES PARA CONTACTO
function actualizarContacto(field, value) {
  dataContacto[field] = value;
}

function actualizarFormField(index, field, value) {
  if (!dataForm.fields[index]) dataForm.fields[index] = {};
  dataForm.fields[index][field] = value;
}

function agregarFormField() {
  const name = prompt('Nombre del campo (nombre, email, mensaje, etc.):', '');
  const placeholder = prompt('Placeholder del campo:', '');
  if (name && placeholder) {
    if (!dataForm.fields) dataForm.fields = [];
    dataForm.fields.push({ name: name, placeholder: placeholder });
    location.reload();
  }
}

function eliminarFormField(index) {
  const field = dataForm.fields[index];
  const nombre = field?.name || `campo ${index + 1}`;
  
  if (confirm(`⚠️ ¿Eliminar el campo "${nombre}"?\n\n📝 Se quitará del formulario de contacto.\n\n✅ OK para eliminar\n❌ Cancelar para conservar`)) {
    dataForm.fields.splice(index, 1);
    alert(`✅ Campo "${nombre}" eliminado del formulario.`);
    location.reload();
  }
}

// FUNCIONES PARA REDES SOCIALES
function agregarRedSocial() {
  const nombre = prompt('Nombre de la red social:', '');
  const icono = prompt('Icono/emoji:', '📱');
  const url = prompt('URL completa:', 'https://');
  if (nombre && url) {
    dataSocials.push({ nombre: nombre, icono: icono, url: url });
    location.reload();
  }
}

function editarRedSocial(index) {
  const red = dataSocials[index];
  const nombre = prompt('Nombre:', red.nombre);
  const icono = prompt('Icono/emoji:', red.icono);
  const url = prompt('URL:', red.url);
  if (nombre && url) {
    dataSocials[index] = { nombre: nombre, icono: icono, url: url };
    location.reload();
  }
}

function eliminarRedSocial(index) {
  const red = dataSocials[index];
  const nombre = red?.nombre || 'esta red social';
  
  if (confirm(`⚠️ ¿Eliminar "${nombre}"?\n\n🔗 Se perderá la URL y configuración.\n\n✅ OK para eliminar\n❌ Cancelar para conservar`)) {
    dataSocials.splice(index, 1);
    alert(`✅ Red social "${nombre}" eliminada.`);
    location.reload();
  }
}

// FUNCIONES PARA FOOTER
function actualizarFooter(field, value) {
  dataFooter[field] = value;
}

// FUNCIONES PARA FUENTES
function actualizarFont(tipo, field, value) {
  if (!dataFonts[tipo]) dataFonts[tipo] = {};
  dataFonts[tipo][field] = value;
  dataFonts[tipo].type = 'google';
  dataFonts[tipo].weights = dataFonts[tipo].weights || '400';
}

</script>

</body>
</html>