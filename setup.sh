#!/bin/bash

# Script de configuración inicial para CMS Visual Template
# Autor: SS-Group
# Versión: 1.0

echo "🎨 Configurando CMS Visual Template..."
echo "======================================"

# Verificar si estamos en el directorio correcto
if [ ! -f "data.json" ]; then
    echo "❌ Error: No se encontró data.json. ¿Estás en el directorio correcto?"
    exit 1
fi

# Función para leer entrada del usuario
read_input() {
    read -p "$1: " value
    echo "$value"
}

# Función para leer entrada con valor por defecto
read_input_default() {
    read -p "$1 [$2]: " value
    echo "${value:-$2}"
}

echo ""
echo "📋 Información Básica de la Empresa"
echo "===================================="

# Recopilar información básica
COMPANY_NAME=$(read_input "Nombre de la empresa")
CONTACT_EMAIL=$(read_input "Email de contacto")
CONTACT_PHONE=$(read_input_default "Teléfono" "+1 (555) 123-4567")
CONTACT_ADDRESS=$(read_input "Dirección")
WHATSAPP_NUMBER=$(read_input_default "Número de WhatsApp (solo números)" "1234567890")
DOMAIN_NAME=$(read_input_default "Dominio (ej: miempresa.com)" "localhost")

echo ""
echo "🔐 Credenciales de Administrador"
echo "================================="

ADMIN_USER=$(read_input_default "Usuario admin" "admin")
ADMIN_PASS=$(read_input "Contraseña admin")

echo ""
echo "🌐 Redes Sociales (opcional)"
echo "============================"

FACEBOOK_URL=$(read_input_default "URL Facebook" "https://facebook.com/$COMPANY_NAME")
INSTAGRAM_URL=$(read_input_default "URL Instagram" "https://instagram.com/$COMPANY_NAME")

# Crear archivo .env
echo ""
echo "⚙️ Creando archivo de configuración..."

cat > .env << EOF
# Configuración del CMS Visual
# Generado automáticamente por setup.sh

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

EOF

# Actualizar data.json con la información básica
echo ""
echo "📝 Actualizando configuración del sitio..."

# Crear un backup del data.json original
cp data.json data.json.backup

# Usar jq para actualizar el JSON si está disponible, sino usar sed
if command -v jq &> /dev/null; then
    # Usar jq para actualizar JSON de forma segura
    jq --arg name "$COMPANY_NAME" \
       --arg email "$CONTACT_EMAIL" \
       --arg phone "$CONTACT_PHONE" \
       --arg address "$CONTACT_ADDRESS" \
       --arg whatsapp "https://wa.me/$WHATSAPP_NUMBER" \
       --arg facebook "$FACEBOOK_URL" \
       --arg instagram "$INSTAGRAM_URL" \
       '
       .brand.name = $name |
       .contacto.email = $email |
       .footer.telefono = $phone |
       .footer.email = $email |
       .footer.direccion = $address |
       .footer.texto = "© 2024 " + $name + ". Todos los derechos reservados." |
       .redes[0].url = $whatsapp |
       .redes[1].url = $facebook |
       .redes[2].url = $instagram |
       .socials[0].url = $whatsapp |
       .socials[1].url = ("mailto:" + $email)
       ' data.json > data.json.tmp && mv data.json.tmp data.json
else
    echo "⚠️  jq no encontrado. Actualiza manualmente data.json con tu información."
fi

# Verificar si Docker está instalado
echo ""
echo "🐳 Verificando Docker..."

if command -v docker &> /dev/null; then
    echo "✅ Docker encontrado"
    
    # Preguntar si quiere levantar el contenedor
    echo ""
    read -p "¿Quieres levantar el contenedor ahora? (y/n): " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        echo "🚀 Levantando contenedor..."
        docker-compose up -d
        
        if [ $? -eq 0 ]; then
            echo ""
            echo "🎉 ¡Configuración completa!"
            echo "========================="
            echo ""
            echo "✅ Sitio web: http://localhost"
            echo "✅ Panel admin: http://localhost/admin"
            echo "✅ Usuario: $ADMIN_USER"
            echo "✅ Contraseña: $ADMIN_PASS"
            echo ""
            echo "📝 Próximos pasos:"
            echo "1. Sube tu logo en /admin/galeria.php"
            echo "2. Personaliza las secciones en /admin/constructor.php"
            echo "3. Configura tu dominio y SSL para producción"
            echo ""
        else
            echo "❌ Error al levantar el contenedor. Verifica docker-compose.yml"
        fi
    fi
else
    echo "⚠️  Docker no encontrado."
    echo ""
    echo "📝 Para continuar:"
    echo "1. Instala Docker: https://docs.docker.com/get-docker/"
    echo "2. Ejecuta: docker-compose up -d"
    echo "3. Accede a http://localhost/admin"
    echo ""
fi

# Mostrar información de archivos creados
echo ""
echo "📄 Archivos generados:"
echo "======================"
echo "✅ .env - Configuración de variables de entorno"
echo "✅ data.json.backup - Respaldo de configuración original"
echo ""

# Información de seguridad
echo ""
echo "🔒 Recordatorios de Seguridad:"
echo "==============================="
echo "❗ Cambia las contraseñas por defecto"
echo "❗ No subas el archivo .env a Git"
echo "❗ Configura SSL/HTTPS en producción"
echo "❗ Actualiza regularmente el sistema"
echo ""

echo "🎯 ¡Setup completado! Tu CMS está listo para usar."