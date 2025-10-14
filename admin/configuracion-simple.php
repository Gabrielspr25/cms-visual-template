<?php
session_start();
if (empty($_SESSION['usuario'])) { header('Location: login.php'); exit; }

$dataFile = __DIR__ . '/../data.json';
$data = file_exists($dataFile) ? json_decode(file_get_contents($dataFile), true) : [];
$brand = $data['brand'] ?? [];
$footer = $data['footer'] ?? [];

function h($str) { return htmlspecialchars($str, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración Técnica - Admin MomVision</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --bg-primary: #1a1f2e;
            --bg-secondary: #2d3748;
            --bg-card: #4a5568;
            --text-primary: #ffffff;
            --text-secondary: #cbd5e1;
            --accent: #3182ce;
            --success: #38a169;
            --danger: #ef4444;
            --warning: #d69e2e;
            --border: #718096;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', system-ui, sans-serif;
            background: var(--bg-primary); color: var(--text-primary);
            min-height: 100vh; display: flex;
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

        /* MAIN */
        .main-container {
            margin-left: 260px;
            flex: 1; display: flex; flex-direction: column;
        }
        .header {
            background: var(--bg-secondary); padding: 20px 30px;
            border-bottom: 1px solid var(--border); display: flex;
            justify-content: space-between; align-items: center;
        }
        .content { padding: 30px; flex: 1; overflow-y: auto; }

        /* WARNING BOX */
        .warning-box {
            background: var(--bg-card); padding: 20px; border-radius: 8px; 
            margin-bottom: 30px; border-left: 4px solid var(--warning);
        }
        .warning-box h4 {
            color: var(--warning); margin: 0 0 10px 0;
        }
        .warning-box p {
            margin: 0; color: var(--text-secondary); font-size: 14px;
        }

        /* CARDS */
        .config-card {
            background: var(--bg-card); border: 1px solid var(--border);
            border-radius: 12px; padding: 25px; margin-bottom: 20px;
        }
        .config-card h3 {
            color: var(--accent); margin-bottom: 20px;
            display: flex; align-items: center; gap: 10px;
        }

        /* FORM */
        .form-row {
            display: flex; gap: 20px; margin-bottom: 20px; align-items: end;
        }
        .form-group {
            flex: 1; display: flex; flex-direction: column;
        }
        .form-group label {
            margin-bottom: 8px; color: var(--text-secondary);
            font-size: 14px; font-weight: 500;
        }
        .form-control {
            padding: 12px; border: 1px solid var(--border);
            border-radius: 6px; background: var(--bg-secondary);
            color: var(--text-primary); transition: all 0.2s;
        }
        .form-control:focus {
            outline: none; border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        textarea.form-control {
            resize: vertical; min-height: 80px;
        }

        /* BUTTONS */
        .btn {
            padding: 12px 24px; border: none; border-radius: 6px;
            cursor: pointer; font-size: 14px; font-weight: 500;
            transition: all 0.2s; text-decoration: none;
            display: inline-flex; align-items: center; gap: 8px;
        }
        .btn-primary { background: var(--accent); color: white; }
        .btn-success { background: var(--success); color: white; }
        .btn-danger { background: var(--danger); color: white; }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.3); }

        /* COLOR PICKER */
        .color-picker-group {
            display: flex; gap: 10px; align-items: center;
        }
        .color-input {
            width: 50px; height: 40px; border: none; border-radius: 6px;
            cursor: pointer; background: none;
        }
        .color-preview {
            width: 40px; height: 40px; border-radius: 6px;
            border: 2px solid var(--border);
        }

        /* FILE UPLOAD */
        .file-upload-area {
            border: 2px dashed var(--border); border-radius: 8px;
            padding: 20px; text-align: center; cursor: pointer;
            transition: all 0.2s; position: relative;
        }
        .file-upload-area:hover {
            border-color: var(--accent); background: var(--bg-secondary);
        }
        .file-upload-area input {
            position: absolute; inset: 0; opacity: 0; cursor: pointer;
        }
        .current-logo {
            max-width: 100px; max-height: 100px; border-radius: 8px;
            margin-bottom: 15px;
        }

        .tech-note {
            background: var(--bg-secondary);
            border-left: 3px solid var(--accent);
            padding: 15px;
            margin: 15px 0;
            border-radius: 6px;
        }
        .tech-note small {
            color: var(--text-secondary);
            font-style: italic;
        }

        @media (max-width: 768px) {
            .sidebar { width: 100%; height: auto; position: relative; }
            .main-container { margin-left: 0; }
            .form-row { flex-direction: column; }
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
        <h3><?= h($brand['name'] ?? 'MomVision') ?></h3>
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
        <a href="galeria.php" class="nav-item">
            <i class="fas fa-images"></i>
            Galería
        </a>
        <a href="configuracion.php" class="nav-item active">
            <i class="fas fa-cogs"></i>
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
    <div class="header">
        <h1><i class="fas fa-cogs"></i> Configuración Técnica</h1>
        <button class="btn btn-success" onclick="guardarConfiguracion()">
            <i class="fas fa-save"></i>
            Guardar Configuración
        </button>
    </div>

    <div class="content">
        <!-- WARNING -->
        <div class="warning-box">
            <h4><i class="fas fa-exclamation-triangle"></i> Configuración Técnica</h4>
            <p>
                Esta página es para <strong>ajustes técnicos avanzados</strong>. 
                Para gestión diaria (secciones, redes sociales, contenido), usa el 
                <a href="dashboard.php" style="color: var(--accent); text-decoration: none;">
                    <i class="fas fa-external-link-alt"></i> Dashboard Principal
                </a>.
            </p>
        </div>

        <!-- MARCA Y COLORES -->
        <div class="config-card">
            <h3><i class="fas fa-palette"></i> Marca y Colores del Sistema</h3>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Nombre de la Marca</label>
                    <input type="text" class="form-control" id="brandName" 
                           value="<?= h($brand['name'] ?? 'MomVision') ?>">
                </div>
                <div class="form-group">
                    <label>Eslogan/Tagline</label>
                    <input type="text" class="form-control" id="brandSlogan" 
                           value="<?= h($brand['slogan'] ?? '') ?>" placeholder="Tu eslogan aquí">
                </div>
            </div>

            <div class="form-group">
                <label>Descripción de la Marca</label>
                <textarea class="form-control" id="brandDescription" 
                          placeholder="Descripción técnica de tu marca..."><?= h($brand['description'] ?? '') ?></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Color Principal (CSS Variables)</label>
                    <div class="color-picker-group">
                        <input type="color" class="color-input" id="brandPrimaryColor" 
                               value="<?= h($brand['primaryColor'] ?? '#3182ce') ?>">
                        <div class="color-preview" style="background: <?= h($brand['primaryColor'] ?? '#3182ce') ?>"></div>
                        <code style="font-size: 12px; color: var(--text-secondary);">--accent</code>
                    </div>
                    <div class="tech-note">
                        <small>Este color se aplica automáticamente en botones, links y acentos del sistema</small>
                    </div>
                </div>
                <div class="form-group">
                    <label>Color Secundario</label>
                    <div class="color-picker-group">
                        <input type="color" class="color-input" id="brandSecondaryColor" 
                               value="<?= h($brand['secondaryColor'] ?? '#38a169') ?>">
                        <div class="color-preview" style="background: <?= h($brand['secondaryColor'] ?? '#38a169') ?>"></div>
                        <code style="font-size: 12px; color: var(--text-secondary);">--success</code>
                    </div>
                    <div class="tech-note">
                        <small>Color para elementos de éxito, confirmaciones y botones secundarios</small>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>Logo del Sistema</label>
                <div class="file-upload-area" onclick="document.getElementById('logoInput').click()">
                    <?php if (!empty($brand['logo'])): ?>
                        <img src="../<?= h($brand['logo']) ?>" class="current-logo" alt="Logo actual">
                    <?php endif; ?>
                    <i class="fas fa-upload" style="font-size: 24px; color: var(--accent);"></i>
                    <p>Haz clic para cambiar el logo</p>
                    <input type="file" id="logoInput" accept="image/*" style="display: none;">
                </div>
                <div class="tech-note">
                    <small>Recomendado: PNG transparente, máximo 200x200px, optimizado para web</small>
                </div>
            </div>
        </div>

        <!-- FUENTES Y TIPOGRAFÍA -->
        <div class="config-card">
            <h3><i class="fas fa-font"></i> Tipografía del Sistema</h3>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Fuente Principal (Google Fonts)</label>
                    <input type="text" class="form-control" id="fontMain" 
                           value="<?= h($data['fonts']['main'] ?? 'Inter') ?>" placeholder="Inter">
                    <div class="tech-note">
                        <small>Para textos del cuerpo, párrafos y contenido general</small>
                    </div>
                </div>
                <div class="form-group">
                    <label>Fuente de Títulos</label>
                    <input type="text" class="form-control" id="fontHeadings" 
                           value="<?= h($data['fonts']['headings'] ?? 'Poppins') ?>" placeholder="Poppins">
                    <div class="tech-note">
                        <small>Para títulos H1, H2, H3 y elementos destacados</small>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>Fuente Decorativa/Firma</label>
                <input type="text" class="form-control" id="fontSignature" 
                       value="<?= h($data['fonts']['signature'] ?? 'Great Vibes') ?>" placeholder="Great Vibes">
                <div class="tech-note">
                    <small>Para elementos especiales, firmas o texto decorativo</small>
                </div>
            </div>
        </div>

        <!-- SEO Y METADATOS -->
        <div class="config-card">
            <h3><i class="fas fa-search"></i> SEO y Metadatos</h3>
            
            <div class="form-group">
                <label>Título SEO (Meta Title)</label>
                <input type="text" class="form-control" id="seoTitle" 
                       value="<?= h($data['seo']['title'] ?? '') ?>" 
                       placeholder="<?= h($brand['name'] ?? 'MomVision') ?> - Descripción principal">
                <div class="tech-note">
                    <small>Máximo 60 caracteres. Aparece en el título de la pestaña del navegador</small>
                </div>
            </div>

            <div class="form-group">
                <label>Descripción SEO (Meta Description)</label>
                <textarea class="form-control" id="seoDescription" 
                          placeholder="Descripción de tu sitio web para buscadores..."><?= h($data['seo']['description'] ?? '') ?></textarea>
                <div class="tech-note">
                    <small>Máximo 160 caracteres. Aparece en los resultados de búsqueda de Google</small>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Palabras Clave (Keywords)</label>
                    <input type="text" class="form-control" id="seoKeywords" 
                           value="<?= h($data['seo']['keywords'] ?? '') ?>" 
                           placeholder="palabra1, palabra2, palabra3">
                    <div class="tech-note">
                        <small>Separadas por comas, máximo 10 palabras clave relevantes</small>
                    </div>
                </div>
                <div class="form-group">
                    <label>Idioma del Sitio</label>
                    <select class="form-control" id="seoLang">
                        <option value="es" <?= ($data['seo']['lang'] ?? 'es') === 'es' ? 'selected' : '' ?>>Español (es)</option>
                        <option value="en" <?= ($data['seo']['lang'] ?? '') === 'en' ? 'selected' : '' ?>>English (en)</option>
                        <option value="pt" <?= ($data['seo']['lang'] ?? '') === 'pt' ? 'selected' : '' ?>>Português (pt)</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- CONFIGURACIÓN AVANZADA -->
        <div class="config-card">
            <h3><i class="fas fa-code"></i> Configuración Avanzada</h3>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Google Analytics ID</label>
                    <input type="text" class="form-control" id="gaId" 
                           value="<?= h($data['analytics']['ga_id'] ?? '') ?>" 
                           placeholder="G-XXXXXXXXXX">
                    <div class="tech-note">
                        <small>ID de Google Analytics 4 para seguimiento de visitas</small>
                    </div>
                </div>
                <div class="form-group">
                    <label>Google Search Console</label>
                    <input type="text" class="form-control" id="gscId" 
                           value="<?= h($data['analytics']['gsc_id'] ?? '') ?>" 
                           placeholder="google-site-verification=...">
                    <div class="tech-note">
                        <small>Meta tag de verificación de Google Search Console</small>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>CSS Personalizado</label>
                <textarea class="form-control" id="customCSS" rows="8" 
                          placeholder="/* CSS personalizado aquí */
:root {
  --custom-color: #your-color;
}

.custom-class {
  /* tus estilos */
}"><?= h($data['custom']['css'] ?? '') ?></textarea>
                <div class="tech-note">
                    <small>CSS que se aplicará globalmente en todo el sitio. Usar con precaución.</small>
                </div>
            </div>
        </div>

        <!-- INFORMACIÓN TÉCNICA -->
        <div class="config-card">
            <h3><i class="fas fa-info-circle"></i> Información del Sistema</h3>
            
            <div class="tech-note">
                <p><strong>Versión del Sistema:</strong> 2.0.1</p>
                <p><strong>Última Actualización:</strong> <?= date('d/m/Y H:i:s') ?></p>
                <p><strong>Ruta de Datos:</strong> <code><?= $dataFile ?></code></p>
                <p><strong>Tamaño de Datos:</strong> <?= file_exists($dataFile) ? number_format(filesize($dataFile)) : '0' ?> bytes</p>
            </div>
        </div>
    </div>
</div>

<script>
// COLOR PICKER UPDATE
document.querySelectorAll('.color-input').forEach(input => {
    input.addEventListener('change', function() {
        const preview = this.parentElement.querySelector('.color-preview');
        if (preview) {
            preview.style.background = this.value;
        }
    });
});

// LOGO UPLOAD
document.getElementById('logoInput').addEventListener('change', function(e) {
    if (e.target.files[0]) {
        const formData = new FormData();
        formData.append('file', e.target.files[0]);
        
        fetch('upload-image.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.error);
            }
        });
    }
});

// SAVE CONFIGURATION
function guardarConfiguracion() {
    const config = {
        brand: {
            name: document.getElementById('brandName').value,
            slogan: document.getElementById('brandSlogan').value,
            description: document.getElementById('brandDescription').value,
            primaryColor: document.getElementById('brandPrimaryColor').value,
            secondaryColor: document.getElementById('brandSecondaryColor').value,
            logo: '<?= h($brand['logo'] ?? '') ?>'
        },
        fonts: {
            main: document.getElementById('fontMain').value,
            headings: document.getElementById('fontHeadings').value,
            signature: document.getElementById('fontSignature').value
        },
        seo: {
            title: document.getElementById('seoTitle').value,
            description: document.getElementById('seoDescription').value,
            keywords: document.getElementById('seoKeywords').value,
            lang: document.getElementById('seoLang').value
        },
        analytics: {
            ga_id: document.getElementById('gaId').value,
            gsc_id: document.getElementById('gscId').value
        },
        custom: {
            css: document.getElementById('customCSS').value
        }
    };

    // Merge with existing data
    fetch('save-content.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(config)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✅ Configuración técnica guardada correctamente!');
        } else {
            alert('❌ Error al guardar: ' + data.error);
        }
    })
    .catch(error => {
        alert('Error de conexión: ' + error);
    });
}

console.log('🔧 Configuración técnica cargada');
</script>

</body>
</html>