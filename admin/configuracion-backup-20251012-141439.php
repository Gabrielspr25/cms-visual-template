<?php
session_start();
if (empty($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}

// Función helper
function h($str) { return htmlspecialchars($str, ENT_QUOTES, 'UTF-8'); }

// Cargar datos
$dataFile = __DIR__ . '/../data.json';
$data = file_exists($dataFile) ? json_decode(file_get_contents($dataFile), true) : [];
$brand = $data['brand'] ?? [];
$socials = $data['socials'] ?? [];
$footer = $data['footer'] ?? [];
$googleFonts = $data['googleFonts'] ?? [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración - Admin MomVision</title>
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

        /* FORM STYLES */
        .config-grid {
            display: grid; gap: 30px;
        }
        .config-card {
            background: var(--bg-card); border: 1px solid var(--border);
            border-radius: 12px; padding: 25px;
        }
        .config-card h3 {
            color: var(--accent); margin-bottom: 20px;
            display: flex; align-items: center; gap: 10px;
        }
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
        .btn-secondary { background: var(--bg-secondary); color: var(--text-primary); border: 1px solid var(--border); }
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

        /* SOCIAL LINKS */
        .social-item {
            display: flex; gap: 15px; align-items: center; padding: 15px;
            background: var(--bg-secondary); border-radius: 8px; margin-bottom: 10px;
        }
        .social-item i {
            width: 20px; color: var(--accent); font-size: 18px;
        }
        .social-item input {
            flex: 1;
        }
        .social-item button {
            padding: 8px 12px;
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

        /* TABS */
        .tabs {
            display: flex; gap: 5px; margin-bottom: 30px;
            border-bottom: 1px solid var(--border);
        }
        .tab {
            padding: 15px 25px; background: none; border: none;
            color: var(--text-secondary); cursor: pointer; transition: all 0.2s;
            border-bottom: 2px solid transparent;
        }
        .tab.active {
            color: var(--accent); border-bottom-color: var(--accent);
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }

        @media (max-width: 768px) {
            .sidebar { width: 100%; height: auto; position: relative; }
            .main { margin-left: 0; }
            .form-row { flex-direction: column; }
            .tabs { flex-wrap: wrap; }
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
        <h1>Configuración General</h1>
        <button class="btn btn-success" onclick="guardarConfiguracion()">
            <i class="fas fa-save"></i>
            Guardar Configuración
        </button>
    </div>

    <div class="content">
        <!-- TABS -->
        <div class="tabs">
            <button class="tab active" onclick="switchTab('brand')">
                <i class="fas fa-building"></i>
                Marca
            </button>
            <button class="tab" onclick="switchTab('socials')">
                <i class="fas fa-share-alt"></i>
                Redes Sociales
            </button>
            <button class="tab" onclick="switchTab('footer')">
                <i class="fas fa-info-circle"></i>
                Pie de Página
            </button>
            <button class="tab" onclick="switchTab('fonts')">
                <i class="fas fa-font"></i>
                Fuentes
            </button>
        </div>

        <!-- BRAND TAB -->
        <div id="brand" class="tab-content active">
            <div class="config-card">
                <h3><i class="fas fa-building"></i> Información de la Marca</h3>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Nombre de la Marca</label>
                        <input type="text" class="form-control" id="brandName" 
                               value="<?= h($brand['name'] ?? '') ?>" placeholder="MomVision">
                    </div>
                    <div class="form-group">
                        <label>Eslogan</label>
                        <input type="text" class="form-control" id="brandSlogan" 
                               value="<?= h($brand['slogan'] ?? '') ?>" placeholder="Tu eslogan aquí">
                    </div>
                </div>

                <div class="form-group">
                    <label>Descripción</label>
                    <textarea class="form-control" id="brandDescription" 
                              placeholder="Descripción de tu marca..."><?= h($brand['description'] ?? '') ?></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Color Principal</label>
                        <div class="color-picker-group">
                            <input type="color" class="color-input" id="brandPrimaryColor" 
                                   value="<?= h($brand['primaryColor'] ?? '#3b82f6') ?>">
                            <div class="color-preview" style="background: <?= h($brand['primaryColor'] ?? '#3b82f6') ?>"></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Color Secundario</label>
                        <div class="color-picker-group">
                            <input type="color" class="color-input" id="brandSecondaryColor" 
                                   value="<?= h($brand['secondaryColor'] ?? '#10b981') ?>">
                            <div class="color-preview" style="background: <?= h($brand['secondaryColor'] ?? '#10b981') ?>"></div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Logo</label>
                    <div class="file-upload-area" onclick="document.getElementById('logoInput').click()">
                        <?php if (!empty($brand['logo'])): ?>
                            <img src="../<?= h($brand['logo']) ?>" class="current-logo" alt="Logo actual">
                        <?php endif; ?>
                        <i class="fas fa-upload" style="font-size: 24px; color: var(--accent);"></i>
                        <p>Haz clic para cambiar el logo</p>
                        <input type="file" id="logoInput" accept="image/*" style="display: none;">
                    </div>
                </div>
            </div>
        </div>

        <!-- SOCIALS TAB -->
        <div id="socials" class="tab-content">
            <div class="config-card">
                <h3><i class="fas fa-share-alt"></i> Redes Sociales</h3>
                <div id="socialsList">
                    <?php foreach ($socials as $key => $social): ?>
                        <div class="social-item" data-key="<?= h($key) ?>">
                            <i class="fab fa-<?= h($social['icon'] ?? 'link') ?>"></i>
                            <input type="text" class="form-control" placeholder="Nombre" 
                                   value="<?= h($social['name']) ?>">
                            <input type="url" class="form-control" placeholder="https://..." 
                                   value="<?= h($social['url']) ?>">
                            <input type="text" class="form-control" placeholder="Icono" 
                                   value="<?= h($social['icon']) ?>" style="width: 100px;">
                            <button class="btn btn-danger" onclick="removeSocial('<?= h($key) ?>')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    <?php endforeach; ?>
                </div>
                <button class="btn btn-primary" onclick="addSocial()">
                    <i class="fas fa-plus"></i>
                    Agregar Red Social
                </button>
            </div>
        </div>

        <!-- FOOTER TAB -->
        <div id="footer" class="tab-content">
            <div class="config-card">
                <h3><i class="fas fa-info-circle"></i> Pie de Página</h3>
                
                <div class="form-group">
                    <label>Texto de Copyright</label>
                    <input type="text" class="form-control" id="footerCopyright" 
                           value="<?= h($footer['copyright'] ?? '') ?>" 
                           placeholder="© 2024 MomVision. Todos los derechos reservados.">
                </div>

                <div class="form-group">
                    <label>Información Adicional</label>
                    <textarea class="form-control" id="footerInfo" 
                              placeholder="Información adicional para el pie de página..."><?= h($footer['info'] ?? '') ?></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Teléfono</label>
                        <input type="tel" class="form-control" id="footerPhone" 
                               value="<?= h($footer['phone'] ?? '') ?>" placeholder="+1 234 567 890">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" class="form-control" id="footerEmail" 
                               value="<?= h($footer['email'] ?? '') ?>" placeholder="info@momvision.com">
                    </div>
                </div>

                <div class="form-group">
                    <label>Dirección</label>
                    <input type="text" class="form-control" id="footerAddress" 
                           value="<?= h($footer['address'] ?? '') ?>" placeholder="Tu dirección aquí">
                </div>
            </div>
        </div>

        <!-- FONTS TAB -->
        <div id="fonts" class="tab-content">
            <div class="config-card">
                <h3><i class="fas fa-font"></i> Fuentes Google</h3>
                <div id="fontsList">
                    <?php foreach ($googleFonts as $font): ?>
                        <div class="social-item">
                            <i class="fas fa-font"></i>
                            <input type="text" class="form-control" placeholder="Nombre de la fuente" 
                                   value="<?= h($font) ?>">
                            <button class="btn btn-danger" onclick="removeFont(this)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    <?php endforeach; ?>
                </div>
                <button class="btn btn-primary" onclick="addFont()">
                    <i class="fas fa-plus"></i>
                    Agregar Fuente
                </button>
                <p style="margin-top: 15px; color: var(--text-secondary); font-size: 14px;">
                    <i class="fas fa-info-circle"></i>
                    Ejemplos: Roboto, Open Sans, Lato, Montserrat
                </p>
            </div>
        </div>
    </div>
</div>

<script>
// TAB SWITCHING
function switchTab(tabName) {
    document.querySelectorAll('.tab').forEach(tab => tab.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
    
    event.target.classList.add('active');
    document.getElementById(tabName).classList.add('active');
}

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

// SOCIAL NETWORKS
function addSocial() {
    const container = document.getElementById('socialsList');
    const div = document.createElement('div');
    const key = 'social_' + Date.now();
    
    div.className = 'social-item';
    div.dataset.key = key;
    div.innerHTML = `
        <i class="fab fa-link"></i>
        <input type="text" class="form-control" placeholder="Nombre" value="">
        <input type="url" class="form-control" placeholder="https://..." value="">
        <input type="text" class="form-control" placeholder="Icono" value="link" style="width: 100px;">
        <button class="btn btn-danger" onclick="removeSocial('${key}')">
            <i class="fas fa-trash"></i>
        </button>
    `;
    
    container.appendChild(div);
}

function removeSocial(key) {
    document.querySelector(`[data-key="${key}"]`).remove();
}

// FONTS
function addFont() {
    const container = document.getElementById('fontsList');
    const div = document.createElement('div');
    
    div.className = 'social-item';
    div.innerHTML = `
        <i class="fas fa-font"></i>
        <input type="text" class="form-control" placeholder="Nombre de la fuente" value="">
        <button class="btn btn-danger" onclick="removeFont(this)">
            <i class="fas fa-trash"></i>
        </button>
    `;
    
    container.appendChild(div);
}

function removeFont(button) {
    button.parentElement.remove();
}

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
        socials: {},
        footer: {
            copyright: document.getElementById('footerCopyright').value,
            info: document.getElementById('footerInfo').value,
            phone: document.getElementById('footerPhone').value,
            email: document.getElementById('footerEmail').value,
            address: document.getElementById('footerAddress').value
        },
        googleFonts: []
    };

    // Gather socials
    document.querySelectorAll('#socialsList .social-item').forEach(item => {
        const key = item.dataset.key;
        const inputs = item.querySelectorAll('input');
        config.socials[key] = {
            name: inputs[0].value,
            url: inputs[1].value,
            icon: inputs[2].value
        };
    });

    // Gather fonts
    document.querySelectorAll('#fontsList .social-item input').forEach(input => {
        if (input.value.trim()) {
            config.googleFonts.push(input.value.trim());
        }
    });

    fetch('save-data.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ config: config })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Configuración guardada correctamente');
        } else {
            alert('Error: ' + data.error);
        }
    })
    .catch(error => {
        alert('Error al guardar configuración');
        console.error(error);
    });
}
</script>

</body>
</html>