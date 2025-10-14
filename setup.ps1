# Script PowerShell para Setup CMS Visual Template
# Autor: SS-Group
# Versión: 1.0 - Para Windows

Write-Host "🎨 Configurando CMS Visual Template..." -ForegroundColor Cyan
Write-Host "=====================================" -ForegroundColor Cyan

# Verificar si estamos en el directorio correcto
if (-not (Test-Path "data.json")) {
    Write-Host "❌ Error: No se encontró data.json. ¿Estás en el directorio correcto?" -ForegroundColor Red
    Read-Host "Presiona Enter para salir..."
    exit 1
}

# Función para leer entrada del usuario
function Read-Input {
    param($Prompt)
    do {
        $value = Read-Host $Prompt
    } while ([string]::IsNullOrWhiteSpace($value))
    return $value
}

# Función para leer entrada con valor por defecto
function Read-InputDefault {
    param($Prompt, $Default)
    $value = Read-Host "$Prompt [$Default]"
    if ([string]::IsNullOrWhiteSpace($value)) {
        return $Default
    }
    return $value
}

Write-Host ""
Write-Host "📋 Información Básica de la Empresa" -ForegroundColor Yellow
Write-Host "====================================" -ForegroundColor Yellow

# Recopilar información básica
$COMPANY_NAME = Read-Input "Nombre de la empresa"
$CONTACT_EMAIL = Read-Input "Email de contacto"
$CONTACT_PHONE = Read-InputDefault "Teléfono" "+1 (555) 123-4567"
$CONTACT_ADDRESS = Read-Input "Dirección"
$WHATSAPP_NUMBER = Read-InputDefault "Número de WhatsApp (solo números)" "1234567890"
$DOMAIN_NAME = Read-InputDefault "Dominio (ej: miempresa.com)" "localhost"

Write-Host ""
Write-Host "🔐 Credenciales de Administrador" -ForegroundColor Yellow
Write-Host "=================================" -ForegroundColor Yellow

$ADMIN_USER = Read-InputDefault "Usuario admin" "admin"
$ADMIN_PASS = Read-Input "Contraseña admin"

Write-Host ""
Write-Host "🌐 Redes Sociales (opcional)" -ForegroundColor Yellow
Write-Host "============================" -ForegroundColor Yellow

$FACEBOOK_URL = Read-InputDefault "URL Facebook" "https://facebook.com/$COMPANY_NAME"
$INSTAGRAM_URL = Read-InputDefault "URL Instagram" "https://instagram.com/$COMPANY_NAME"

# Crear archivo .env
Write-Host ""
Write-Host "⚙️ Creando archivo de configuración..." -ForegroundColor Green

$envContent = @"
# Configuración del CMS Visual
# Generado automáticamente por setup.ps1

# Configuración de la aplicación
APP_NAME="$COMPANY_NAME"
APP_URL="https://$DOMAIN_NAME"
ENVIRONMENT="production"

# Configuración del admin
ADMIN_USERNAME="$ADMIN_USER"
ADMIN_PASSWORD="$ADMIN_PASS"

# Configuración de contacto
CONTACT_EMAIL="$CONTACT_EMAIL"
CONTACT_PHONE="$CONTACT_PHONE"
CONTACT_ADDRESS="$CONTACT_ADDRESS"

# Redes sociales
WHATSAPP_NUMBER="$WHATSAPP_NUMBER"
FACEBOOK_URL="$FACEBOOK_URL"
INSTAGRAM_URL="$INSTAGRAM_URL"

# Configuración de archivos
UPLOAD_MAX_SIZE="50M"
ALLOWED_FILE_TYPES="jpg,jpeg,png,gif,mp4,mov,avi,pdf,doc,docx"

# Configuración de backup
BACKUP_ENABLED="true"
BACKUP_RETENTION_DAYS="7"

# SSL/TLS
SSL_ENABLED="true"
SSL_CERT_PATH="/etc/ssl/certs/cert.pem"
SSL_KEY_PATH="/etc/ssl/private/key.pem"
"@

$envContent | Out-File -FilePath ".env" -Encoding UTF8

# Actualizar data.json con la información básica
Write-Host ""
Write-Host "📝 Actualizando configuración del sitio..." -ForegroundColor Green

# Crear un backup del data.json original
Copy-Item "data.json" "data.json.backup" -Force

# Leer el JSON actual
$jsonContent = Get-Content "data.json" -Raw | ConvertFrom-Json

# Actualizar los valores
$jsonContent.brand.name = $COMPANY_NAME
$jsonContent.contacto.email = $CONTACT_EMAIL
$jsonContent.footer.telefono = $CONTACT_PHONE
$jsonContent.footer.email = $CONTACT_EMAIL
$jsonContent.footer.direccion = $CONTACT_ADDRESS
$jsonContent.footer.texto = "© 2024 $COMPANY_NAME. Todos los derechos reservados."

# Actualizar redes sociales
$jsonContent.redes[0].url = "https://wa.me/$WHATSAPP_NUMBER"
if ($jsonContent.redes.Count -gt 1) {
    $jsonContent.redes[1].url = $FACEBOOK_URL
}
if ($jsonContent.redes.Count -gt 2) {
    $jsonContent.redes[2].url = $INSTAGRAM_URL
}

$jsonContent.socials[0].url = "https://wa.me/$WHATSAPP_NUMBER"
if ($jsonContent.socials.Count -gt 1) {
    $jsonContent.socials[1].url = "mailto:$CONTACT_EMAIL"
}

# Guardar el JSON actualizado
$jsonContent | ConvertTo-Json -Depth 10 | Out-File -FilePath "data.json" -Encoding UTF8

# Verificar si Docker está instalado
Write-Host ""
Write-Host "🐳 Verificando Docker..." -ForegroundColor Green

$dockerInstalled = $false
try {
    $dockerVersion = docker --version 2>$null
    if ($dockerVersion) {
        Write-Host "✅ Docker encontrado: $dockerVersion" -ForegroundColor Green
        $dockerInstalled = $true
    }
}
catch {
    Write-Host "❌ Docker no encontrado" -ForegroundColor Red
}

if ($dockerInstalled) {
    # Preguntar si quiere levantar el contenedor
    Write-Host ""
    $levantarDocker = Read-Host "¿Quieres levantar el contenedor ahora? (s/n)"
    if ($levantarDocker -eq "s" -or $levantarDocker -eq "S" -or $levantarDocker -eq "yes" -or $levantarDocker -eq "y") {
        Write-Host "🚀 Levantando contenedor..." -ForegroundColor Green
        try {
            docker-compose up -d
            if ($LASTEXITCODE -eq 0) {
                Write-Host ""
                Write-Host "🎉 ¡Configuración completa!" -ForegroundColor Green
                Write-Host "=========================" -ForegroundColor Green
                Write-Host ""
                Write-Host "✅ Sitio web: http://localhost" -ForegroundColor Cyan
                Write-Host "✅ Panel admin: http://localhost/admin" -ForegroundColor Cyan
                Write-Host "✅ Usuario: $ADMIN_USER" -ForegroundColor Cyan
                Write-Host "✅ Contraseña: $ADMIN_PASS" -ForegroundColor Cyan
                Write-Host ""
                Write-Host "📝 Próximos pasos:" -ForegroundColor Yellow
                Write-Host "1. Sube tu logo en /admin/galeria.php" -ForegroundColor White
                Write-Host "2. Personaliza las secciones en /admin/constructor.php" -ForegroundColor White
                Write-Host "3. Configura tu dominio y SSL para producción" -ForegroundColor White
                Write-Host ""
                
                # Abrir el navegador automáticamente
                Start-Process "http://localhost"
                Start-Sleep -Seconds 3
                Start-Process "http://localhost/admin"
            }
            else {
                Write-Host "❌ Error al levantar el contenedor. Verifica docker-compose.yml" -ForegroundColor Red
            }
        }
        catch {
            Write-Host "❌ Error al ejecutar docker-compose: $($_.Exception.Message)" -ForegroundColor Red
        }
    }
}
else {
    Write-Host "⚠️ Docker no encontrado." -ForegroundColor Yellow
    Write-Host ""
    Write-Host "📝 Para continuar:" -ForegroundColor Yellow
    Write-Host "1. Instala Docker Desktop desde: https://www.docker.com/products/docker-desktop" -ForegroundColor White
    Write-Host "2. Reinicia tu computadora" -ForegroundColor White
    Write-Host "3. Ejecuta: docker-compose up -d" -ForegroundColor White
    Write-Host "4. Accede a http://localhost/admin" -ForegroundColor White
    Write-Host ""
}

# Mostrar información de archivos creados
Write-Host ""
Write-Host "📄 Archivos generados:" -ForegroundColor Green
Write-Host "======================" -ForegroundColor Green
Write-Host "✅ .env - Configuración de variables de entorno" -ForegroundColor White
Write-Host "✅ data.json.backup - Respaldo de configuración original" -ForegroundColor White
Write-Host ""

# Información de seguridad
Write-Host ""
Write-Host "🔒 Recordatorios de Seguridad:" -ForegroundColor Red
Write-Host "===============================" -ForegroundColor Red
Write-Host "❗ Cambia las contraseñas por defecto antes de producción" -ForegroundColor Yellow
Write-Host "❗ No subas el archivo .env a Git" -ForegroundColor Yellow
Write-Host "❗ Configura SSL/HTTPS en producción" -ForegroundColor Yellow
Write-Host "❗ Actualiza regularmente el sistema" -ForegroundColor Yellow
Write-Host ""

Write-Host "🎯 ¡Setup completado! Tu CMS está listo para usar." -ForegroundColor Green

# Pausa para que el usuario pueda leer todo
Write-Host ""
Read-Host "Presiona Enter para continuar..."