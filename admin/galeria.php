<?php
session_start();
if (empty($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}

// Función helper
function h($str) { return htmlspecialchars($str, ENT_QUOTES, 'UTF-8'); }

// Función para formatear bytes
function formatBytes($bytes, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    for ($i = 0; $bytes > 1024; $i++) {
        $bytes /= 1024;
    }
    return round($bytes, $precision) . ' ' . $units[$i];
}

// Cargar datos
$dataFile = __DIR__ . '/../data.json';
$data = file_exists($dataFile) ? json_decode(file_get_contents($dataFile), true) : [];
$brand = $data['brand'] ?? [];

// Obtener archivos de uploads
$uploadsDir = __DIR__ . '/../uploads/';
$files = [];
if (is_dir($uploadsDir)) {
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($uploadsDir));
    foreach ($iterator as $file) {
        if ($file->isFile()) {
            $relativePath = str_replace($uploadsDir, '', $file->getPathname());
            $files[] = [
                'name' => $file->getFilename(),
                'path' => 'uploads/' . str_replace('\\', '/', $relativePath),
                'size' => $file->getSize(),
                'modified' => $file->getMTime(),
                'type' => pathinfo($file->getFilename(), PATHINFO_EXTENSION)
            ];
        }
    }
}

// Ordenar por fecha de modificación
usort($files, function($a, $b) {
    return $b['modified'] - $a['modified'];
});
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galería - Admin MomVision</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --bg-primary: #0f0f23;
            --bg-secondary: #16213e;
            --bg-card: #1e293b;
            --text-primary: #e2e8f0;
            --text-secondary: #94a3b8;
            --accent: #3b82f6;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --border: #334155;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--bg-primary); color: var(--text-primary);
            min-height: 100vh; display: flex;
        }

        /* SIDEBAR */
        .sidebar {
            width: 250px; background: var(--bg-secondary);
            border-right: 1px solid var(--border); display: flex;
            flex-direction: column; padding: 20px 0;
        }
        .logo {
            padding: 0 20px 20px; border-bottom: 1px solid var(--border);
            text-align: center; margin-bottom: 20px;
        }
        .logo img { width: 40px; height: 40px; border-radius: 8px; margin-bottom: 8px; }
        .logo h3 { color: var(--accent); }
        .nav-menu { flex: 1; }
        .nav-item {
            display: flex; align-items: center; padding: 12px 20px;
            color: var(--text-secondary); text-decoration: none;
            transition: all 0.2s; border-left: 3px solid transparent;
        }
        .nav-item:hover, .nav-item.active {
            background: var(--bg-card); color: var(--text-primary);
            border-left-color: var(--accent);
        }
        .nav-item i { margin-right: 10px; width: 16px; }

        /* MAIN */
        .main { flex: 1; display: flex; flex-direction: column; }
        .header {
            background: var(--bg-secondary); padding: 20px 30px;
            border-bottom: 1px solid var(--border); display: flex;
            justify-content: space-between; align-items: center;
        }
        .content { padding: 30px; flex: 1; overflow-y: auto; }

        /* UPLOAD AREA */
        .upload-area {
            background: var(--bg-card); border: 2px dashed var(--border);
            border-radius: 12px; padding: 40px; text-align: center;
            margin-bottom: 30px; transition: all 0.2s; cursor: pointer;
        }
        .upload-area:hover {
            border-color: var(--accent); background: var(--bg-secondary);
        }
        .upload-area.dragover {
            border-color: var(--success); background: var(--bg-secondary);
        }

        /* GALLERY GRID */
        .gallery-grid {
            display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px; margin-top: 20px;
        }
        .file-card {
            background: var(--bg-card); border: 1px solid var(--border);
            border-radius: 12px; overflow: hidden; position: relative;
            transition: all 0.2s; cursor: pointer;
        }
        .file-card:hover {
            transform: translateY(-2px); box-shadow: 0 8px 25px rgba(0,0,0,0.3);
        }
        .file-preview {
            width: 100%; height: 150px; background: var(--bg-secondary);
            display: flex; align-items: center; justify-content: center;
            position: relative; overflow: hidden;
        }
        .file-preview img {
            width: 100%; height: 100%; object-fit: cover;
        }
        .file-preview .file-icon {
            font-size: 48px; color: var(--text-secondary);
        }
        .file-info {
            padding: 15px;
        }
        .file-name {
            font-weight: 600; margin-bottom: 5px; word-break: break-all;
            font-size: 14px;
        }
        .file-meta {
            color: var(--text-secondary); font-size: 12px;
            display: flex; justify-content: space-between; align-items: center;
        }
        .file-actions {
            position: absolute; top: 10px; right: 10px;
            opacity: 0; transition: all 0.2s;
        }
        .file-card:hover .file-actions { opacity: 1; }
        .file-actions button {
            background: rgba(0,0,0,0.7); border: none; color: white;
            padding: 8px; border-radius: 6px; cursor: pointer;
            margin-left: 5px; transition: all 0.2s;
        }
        .file-actions button:hover { background: var(--danger); }

        /* BUTTONS */
        .btn {
            padding: 10px 20px; border: none; border-radius: 6px;
            cursor: pointer; font-size: 14px; font-weight: 500;
            transition: all 0.2s; text-decoration: none;
            display: inline-flex; align-items: center; gap: 8px;
        }
        .btn-primary { background: var(--accent); color: white; }
        .btn-success { background: var(--success); color: white; }
        .btn-danger { background: var(--danger); color: white; }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.3); }

        /* FILTERS */
        .filters {
            display: flex; gap: 10px; margin-bottom: 20px; flex-wrap: wrap;
            align-items: center;
        }
        .filter-btn {
            padding: 8px 16px; border: 1px solid var(--border);
            background: var(--bg-card); color: var(--text-secondary);
            border-radius: 6px; cursor: pointer; transition: all 0.2s;
        }
        .filter-btn.active {
            background: var(--accent); color: white; border-color: var(--accent);
        }

        /* MODAL */
        .modal {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,0.8); z-index: 1000;
            align-items: center; justify-content: center; padding: 20px;
        }
        .modal.show { display: flex; }
        .modal-content {
            background: var(--bg-secondary); border-radius: 12px;
            width: 100%; max-width: 800px; max-height: 90vh; overflow-y: auto;
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

        @media (max-width: 768px) {
            .sidebar { width: 100%; height: auto; position: relative; }
            .main { margin-left: 0; }
            .gallery-grid { grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); }
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
        <a href="constructor.php" class="nav-item">
            <i class="fas fa-magic"></i>
            Constructor
        </a>
        <a href="mensajes.php" class="nav-item">
            <i class="fas fa-envelope"></i>
            Mensajes
        </a>
        <a href="galeria.php" class="nav-item active">
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
        <h1>Galería de Archivos</h1>
        <div>
            <button class="btn btn-primary" onclick="document.getElementById('fileInput').click()">
                <i class="fas fa-upload"></i>
                Subir Archivo
            </button>
        </div>
    </div>

    <div class="content">
        <!-- UPLOAD AREA -->
        <div class="upload-area" id="uploadArea" onclick="document.getElementById('fileInput').click()">
            <i class="fas fa-cloud-upload-alt" style="font-size: 48px; color: var(--accent); margin-bottom: 15px;"></i>
            <h3>Subir Archivos</h3>
            <p>Haz clic aquí o arrastra archivos para subirlos</p>
            <input type="file" id="fileInput" multiple accept="image/*,video/*" style="display: none;">
        </div>

        <!-- FILTERS -->
        <div class="filters">
            <span>Filtrar por tipo:</span>
            <button class="filter-btn active" data-filter="all">Todos</button>
            <button class="filter-btn" data-filter="image">Imágenes</button>
            <button class="filter-btn" data-filter="video">Videos</button>
            <button class="filter-btn" data-filter="other">Otros</button>
        </div>

        <!-- GALLERY -->
        <div class="gallery-grid" id="galleryGrid">
            <?php foreach ($files as $file): ?>
                <div class="file-card" data-type="<?= h($file['type']) ?>">
                    <div class="file-preview">
                        <?php if (in_array(strtolower($file['type']), ['jpg', 'jpeg', 'png', 'gif', 'webp'])): ?>
                            <img src="../<?= h($file['path']) ?>" alt="<?= h($file['name']) ?>" loading="lazy">
                        <?php elseif (in_array(strtolower($file['type']), ['mp4', 'webm', 'ogg'])): ?>
                            <i class="fas fa-play-circle file-icon"></i>
                        <?php else: ?>
                            <i class="fas fa-file file-icon"></i>
                        <?php endif; ?>
                        
                        <div class="file-actions">
                            <button onclick="copyUrl('<?= h($file['path']) ?>')" title="Copiar URL">
                                <i class="fas fa-copy"></i>
                            </button>
                            <button onclick="deleteFile('<?= h($file['path']) ?>')" title="Eliminar">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    <div class="file-info">
                        <div class="file-name"><?= h($file['name']) ?></div>
                        <div class="file-meta">
                            <span><?= formatBytes($file['size']) ?></span>
                            <span><?= date('d/m/Y', $file['modified']) ?></span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- MODAL PREVIEW -->
<div class="modal" id="previewModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Vista Previa</h3>
            <button class="close-modal" onclick="closeModal()">&times;</button>
        </div>
        <div class="modal-body" id="previewContent">
            <!-- Preview content goes here -->
        </div>
        <div class="modal-footer">
            <button class="btn btn-danger" onclick="closeModal()">Cerrar</button>
        </div>
    </div>
</div>

<script>
// UPLOAD FUNCTIONALITY
const uploadArea = document.getElementById('uploadArea');
const fileInput = document.getElementById('fileInput');

uploadArea.addEventListener('dragover', (e) => {
    e.preventDefault();
    uploadArea.classList.add('dragover');
});

uploadArea.addEventListener('dragleave', () => {
    uploadArea.classList.remove('dragover');
});

uploadArea.addEventListener('drop', (e) => {
    e.preventDefault();
    uploadArea.classList.remove('dragover');
    const files = e.dataTransfer.files;
    uploadFiles(files);
});

fileInput.addEventListener('change', (e) => {
    uploadFiles(e.target.files);
});

function uploadFiles(files) {
    console.log('Subiendo archivos:', files.length);
    const formData = new FormData();
    Array.from(files).forEach(file => {
        formData.append('files[]', file);
    });

    // Mostrar indicador de carga
    const uploadArea = document.getElementById('uploadArea');
    const originalContent = uploadArea.innerHTML;
    uploadArea.innerHTML = `
        <div style="text-align: center; padding: 40px; color: var(--accent);">
            <i class="fas fa-spinner fa-spin" style="font-size: 48px; margin-bottom: 15px;"></i>
            <h3>Subiendo archivos...</h3>
            <p>Por favor espera</p>
        </div>
    `;

    fetch('upload-files.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        console.log('Respuesta del servidor:', data);
        uploadArea.innerHTML = originalContent;
        
        if (data.success) {
            let message = data.message;
            if (data.errors && data.errors.length > 0) {
                message += '\n\nAdvertencias:\n' + data.errors.join('\n');
            }
            alert('✅ ' + message);
            location.reload();
        } else {
            alert('❌ Error: ' + data.error);
        }
    })
    .catch(error => {
        console.error('Error en upload:', error);
        uploadArea.innerHTML = originalContent;
        alert('❌ Error de conexión al subir archivos');
    });
}

// FILTERS
document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        
        const filter = btn.dataset.filter;
        const cards = document.querySelectorAll('.file-card');
        
        cards.forEach(card => {
            const type = card.dataset.type.toLowerCase();
            if (filter === 'all') {
                card.style.display = 'block';
            } else if (filter === 'image' && ['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(type)) {
                card.style.display = 'block';
            } else if (filter === 'video' && ['mp4', 'webm', 'ogg'].includes(type)) {
                card.style.display = 'block';
            } else if (filter === 'other' && !['jpg', 'jpeg', 'png', 'gif', 'webp', 'mp4', 'webm', 'ogg'].includes(type)) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    });
});

// COPY URL
function copyUrl(path) {
    const url = window.location.origin + '/' + path;
    navigator.clipboard.writeText(url).then(() => {
        alert('URL copiada al portapapeles');
    });
}

// DELETE FILE
function deleteFile(path) {
    if (confirm('¿Seguro que quieres eliminar este archivo?')) {
        fetch('delete-file.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ path: path })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Archivo eliminado');
                location.reload();
            } else {
                alert('Error: ' + data.error);
            }
        });
    }
}

// MODAL
function closeModal() {
    document.getElementById('previewModal').classList.remove('show');
}
</script>

</body>
</html>
