# 🎨 CMS Visual Template

**Sistema Completo de Gestión de Contenidos** - Listo para usar con cualquier cliente en minutos.

[![Docker](https://img.shields.io/badge/docker-%230db7ed.svg?style=flat&logo=docker&logoColor=white)](https://www.docker.com/)
[![PHP](https://img.shields.io/badge/php-%23777BB4.svg?style=flat&logo=php&logoColor=white)](https://www.php.net/)
[![DigitalOcean](https://img.shields.io/badge/DigitalOcean-%230167ff.svg?style=flat&logo=digitalOcean&logoColor=white)](https://www.digitalocean.com/)
[![GitHub Actions](https://img.shields.io/badge/github%20actions-%232671E5.svg?style=flat&logo=githubactions&logoColor=white)](https://github.com/features/actions)

> 🚀 **Deploy automático en DigitalOcean** | ⚡ **Setup en 10 minutos** | 💰 **$11/mes hosting**

## ✨ Características Principales

### 🎯 **Sistema Completo**
- ✅ **Constructor Visual** - Editor drag & drop sin conocimientos técnicos
- ✅ **Panel Administrativo** - Dashboard completo con estadísticas
- ✅ **Galería Multimedia** - Gestión de imágenes y videos
- ✅ **Sistema de Mensajes** - Formulario de contacto integrado
- ✅ **Responsive Design** - Funciona en cualquier dispositivo

### 🚀 **Listo para Producción**
- ✅ **Docker** - Containerización completa
- ✅ **DigitalOcean** - Deploy automático configurado
- ✅ **SSL/HTTPS** - Certificados y seguridad
- ✅ **CI/CD** - GitHub Actions configurado
- ✅ **Backup Automático** - Respaldo de datos programado

### 🛠️ **Tecnologías**
- **Backend**: PHP 8.1, JSON Database
- **Frontend**: HTML5, CSS3, JavaScript Vanilla
- **Infraestructura**: Docker, Nginx, SSL
- **Deployment**: GitHub Actions, DigitalOcean

---

## 🚀 Instalación Rápida

### **Opción 1: Desarrollo Local**

```bash
# 1. Clonar el repositorio
git clone https://github.com/tu-usuario/cms-visual-template.git mi-proyecto
cd mi-proyecto

# 2. Configurar variables de entorno
cp .env.example .env
# Editar .env con tus datos

# 3. Usar Docker Compose
docker-compose up -d

# 4. Acceder al panel admin
# http://localhost/admin
# Usuario: admin | Contraseña: admin123
```

### **Opción 2: DigitalOcean (Producción)**

```bash
# 1. Fork/clonar en GitHub
# 2. Crear Droplet en DigitalOcean
# 3. Configurar DNS hacia tu droplet
# 4. Configurar secrets en GitHub:
#    - DIGITALOCEAN_ACCESS_TOKEN
#    - DO_HOST, DO_USERNAME, DO_SSH_KEY
#    - DO_REGISTRY_NAME

# 5. Push a main = deploy automático! 🚀
git push origin main
```

---

## ⚙️ Configuración Inicial

### 1. **Personalizar Datos Básicos**

Edita `data.json`:
```json
{
    "brand": {
        "name": "Tu Empresa",
        "logo": "uploads/tu-logo.jpg"
    },
    "contacto": {
        "email": "contacto@tuempresa.com",
        "titulo": "Contacto"
    },
    "footer": {
        "direccion": "Tu Ciudad, Tu País",
        "telefono": "+1 (555) 123-4567",
        "email": "contacto@tuempresa.com"
    }
}
```

### 2. **Configurar Variables de Entorno**

Copia `.env.example` a `.env` y personaliza:
```env
APP_NAME="Tu Empresa"
ADMIN_USERNAME="tu_admin"
ADMIN_PASSWORD="password_segura"
CONTACT_EMAIL="contacto@tuempresa.com"
WHATSAPP_NUMBER="1234567890"
```

### 3. **Subir Logo e Imágenes**

1. Ve a `/admin/galeria.php`
2. Sube tu logo como `logo.jpg`
3. Sube imágenes de ejemplo para las colecciones
4. Actualiza las rutas en `data.json`

---

## 📋 Tipos de Contenido

### **Secciones de Texto**
```json
{
    "tipo": "texto",
    "titulo": "Sobre Nosotros",
    "contenido": {
        "html": "<p>Tu contenido aquí</p>"
    }
}
```

### **Colecciones de Productos/Servicios**
```json
{
    "tipo": "coleccion",
    "titulo": "Nuestros Servicios",
    "columns": [
        {
            "titulo": "Servicio 1",
            "imagen": "uploads/servicio1.jpg",
            "resumen": "Descripción corta",
            "detalle": "Descripción completa"
        }
    ]
}
```

### **Multiformato (Avanzado)**
```json
{
    "tipo": "multiformato",
    "titulo": "Contenido Mixto",
    "bloques": [
        {"tipo": "titulo", "contenido": "Mi Título"},
        {"tipo": "texto", "contenido": "Mi párrafo"},
        {"tipo": "lista", "contenido": ["Item 1", "Item 2"]}
    ]
}
```

---

## 🎨 Personalización

### **Colores y Fuentes**

Edita el archivo `index.php` (líneas CSS):
```css
:root {
    --primary-color: #0e7ac7;      /* Color principal */
    --secondary-color: #f8f9fa;    /* Color secundario */
    --accent-color: #e8f4fd;       /* Color de acento */
    --text-color: #333;            /* Color del texto */
}
```

### **Fuentes Google**

En `data.json`:
```json
"fonts": {
    "body": {
        "type": "google",
        "family": "Inter",
        "weights": "400,500"
    },
    "headings": {
        "type": "google", 
        "family": "Poppins",
        "weights": "600,700"
    }
}
```

---

## 🔧 Gestión desde el Admin

### **Dashboard** (`/admin/`)
- 📊 Estadísticas del sitio
- 🔧 Gestión de secciones
- 📁 Acceso a herramientas
- 👀 Vista previa del sitio

### **Constructor** (`/admin/constructor.php`)
- 🎨 Editor visual drag & drop
- 📝 Tipos de contenido: texto, título, imagen, video, lista, botones
- 👁️ Vista previa en tiempo real
- 💾 Guardado automático

### **Galería** (`/admin/galeria.php`)
- 📤 Subida de archivos múltiple
- 🖼️ Gestión de imágenes y videos
- 🔍 Filtros por tipo de archivo
- 🗑️ Eliminación segura

### **Mensajes** (`/admin/mensajes.php`)
- 📧 Inbox de formulario de contacto
- 📋 Detalles completos de cada mensaje
- 📊 Estadísticas de contactos

### **Configuración** (`/admin/configuracion.php`)
- 🏢 Datos de la empresa
- 🌐 Redes sociales
- 🎨 Configuración visual
- ⚙️ Ajustes técnicos

---

## 🌐 Deploy en DigitalOcean

### **Requisitos**
1. Droplet Ubuntu 20.04+ 
2. Docker instalado
3. Dominio apuntando al droplet
4. GitHub Repository

### **Configuración Automática**

1. **Fork este repositorio**
2. **Configurar Secrets en GitHub:**
   ```
   DIGITALOCEAN_ACCESS_TOKEN=tu_token_do
   DO_REGISTRY_NAME=tu-registry
   DO_HOST=tu-ip-droplet
   DO_USERNAME=root
   DO_SSH_KEY=tu-private-key
   ```

3. **Primera configuración en el droplet:**
   ```bash
   # Instalar Docker
   curl -fsSL https://get.docker.com -o get-docker.sh
   sudo sh get-docker.sh
   
   # Crear directorios
   mkdir -p /var/www/{uploads,backups}
   touch /var/www/{data.json,mensajes.json}
   chmod 666 /var/www/{data.json,mensajes.json}
   ```

4. **Deploy automático:**
   - Cada push a `main` = deploy automático
   - GitHub Actions se encarga de todo
   - SSL con Let's Encrypt incluido

### **Comandos Útiles en Producción**

```bash
# Ver logs del contenedor
docker logs cms-visual

# Acceder al contenedor
docker exec -it cms-visual bash

# Backup manual
docker exec cms-visual tar -czf /tmp/backup.tar.gz -C /var/www/html uploads data.json mensajes.json

# Restart del servicio
docker restart cms-visual
```

---

## 🔒 Seguridad

### **Credenciales**
- ❗ **CAMBIAR** usuario y contraseña por defecto
- 🔐 Usar contraseñas seguras
- 🚫 No commitear archivos `.env`

### **Archivos Sensibles**
- `data.json` - Protegido por Nginx
- `mensajes.json` - Protegido por Nginx  
- `uploads/` - Sin ejecución de scripts
- `/admin/` - Rate limiting

### **SSL/HTTPS**
- ✅ Certificados automáticos con Let's Encrypt
- ✅ Redirección HTTP → HTTPS
- ✅ Headers de seguridad configurados

---

## 📞 Soporte y Personalización

### **Casos de Uso Comunes**

1. **Sitio Corporativo**: Sobre nosotros, servicios, contacto
2. **Portafolio**: Galería de trabajos, información personal
3. **Restaurante**: Menú, galería, reservas
4. **Clínica**: Servicios médicos, equipo, citas
5. **E-commerce Básico**: Catálogo de productos

### **Extensiones Disponibles**
- 🛒 **Módulo E-commerce** - Carrito y pagos
- 📊 **Analytics** - Google Analytics integrado
- 📧 **Email Marketing** - Newsletter automático
- 🔍 **SEO Avanzado** - Optimización completa
- 🌍 **Multiidioma** - Sitios en varios idiomas

---

## 📈 Performance

### **Optimizaciones Incluidas**
- ✅ **Compresión Gzip** - Archivos hasta 80% más pequeños
- ✅ **Cache de Imágenes** - Carga ultrarrápida
- ✅ **Minificación** - CSS/JS optimizados
- ✅ **CDN Ready** - Compatible con CloudFlare
- ✅ **Lazy Loading** - Carga bajo demanda

### **Benchmarks Típicos**
- **PageSpeed Score**: 95-100/100
- **Tiempo de Carga**: <2 segundos
- **Tamaño Inicial**: ~500KB
- **Requests**: <20 por página

---

## 🎯 Roadmap

### **Próximas Características**
- [ ] **API REST** - Acceso programático a datos
- [ ] **PWA Support** - App móvil automática
- [ ] **Base de Datos SQL** - Opción MySQL/PostgreSQL
- [ ] **Multi-tenancy** - Múltiples sitios por instalación
- [ ] **Tema Builder** - Editor visual de diseño
- [ ] **Plugins System** - Extensiones de terceros

---

## 💝 Licencia

**Uso Comercial Permitido** - Úsalo libremente en proyectos de clientes.

### **Lo que puedes hacer:**
✅ Usar en proyectos comerciales  
✅ Modificar y personalizar  
✅ Crear sitios para clientes  
✅ Vender como servicio  

### **Lo que no puedes hacer:**
❌ Revender como plantilla genérica  
❌ Quitar créditos del desarrollador  
❌ Reclamar autoría del código base  

---

## 🚀 **¡Empieza Ahora!**

```bash
git clone https://github.com/tu-usuario/cms-visual-template.git
cd cms-visual-template
docker-compose up -d
```

**→ Abre http://localhost/admin**  
**→ Usuario: `admin` | Contraseña: `admin123`**  
**→ ¡Crea tu primer sitio en minutos!** 🎉

---

**CMS Visual Template** - La forma más rápida de crear sitios web profesionales.

**¿Necesitas ayuda o personalización?** [Contacta al desarrollador](mailto:soporte@cmsvisual.com)