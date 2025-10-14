# ⚡ Inicio Rápido - 10 Minutos

Crea un sitio web profesional para tu cliente en menos de 10 minutos.

## 🚀 Opción 1: Desarrollo Local

```bash
# 1. Clonar este template
git clone https://github.com/tu-usuario/cms-visual-template.git mi-proyecto-cliente
cd mi-proyecto-cliente

# 2. Ejecutar configuración automática
./setup.sh

# 3. Levantar con Docker
docker-compose up -d

# 4. ¡Listo!
# Sitio: http://localhost
# Admin: http://localhost/admin
```

## 🌐 Opción 2: Producción en DigitalOcean

```bash
# 1. Fork este repositorio en GitHub
# 2. Crear droplet en DigitalOcean ($6/mes)
# 3. Configurar secrets en GitHub (ver DEPLOY-DIGITALOCEAN.md)
# 4. git push origin main = ¡Sitio en producción!
```

## 📝 Configuración Básica

### 1. **Personalizar Empresa**
Edita `data.json`:
```json
{
    "brand": {
        "name": "Nombre del Cliente",
        "logo": "uploads/logo-cliente.jpg"
    },
    "contacto": {
        "email": "contacto@cliente.com"
    }
}
```

### 2. **Credenciales Admin**
Edita `.env`:
```
ADMIN_USERNAME="admin_cliente"
ADMIN_PASSWORD="password_segura"
```

### 3. **Subir Logo e Imágenes**
1. Ir a `/admin/galeria.php`
2. Subir logo del cliente
3. Subir imágenes para servicios/productos

## 🎨 Personalización

### **Panel Admin**: `/admin/`
- 📊 Dashboard con estadísticas
- 🎨 Constructor visual drag & drop
- 📁 Galería de archivos
- 📧 Mensajes de contacto

### **Tipos de Contenido**
- ✅ **Texto**: Páginas informativas
- ✅ **Colecciones**: Productos/servicios en grid
- ✅ **Multiformato**: Contenido mixto avanzado

## 💰 Precios Sugeridos

### **Para Clientes**
- **Setup inicial**: $500-2000
- **Hosting + mantenimiento**: $50-200/mes
- **Customizaciones**: $100-500/feature

### **Costos DigitalOcean**
- **Droplet básico**: $6/mes
- **Container Registry**: $5/mes
- **Total**: ~$11/mes

## 🎯 Casos de Uso

### ✅ **Sitio Corporativo**
- Sobre nosotros, servicios, contacto
- Timeline: 2-4 horas setup

### ✅ **Portfolio Profesional**
- Galería de trabajos, experiencia
- Timeline: 1-3 horas setup

### ✅ **Restaurante/Cafetería**
- Menú, galería, reservas
- Timeline: 2-4 horas setup

### ✅ **Clínica/Consultorio**
- Servicios médicos, equipo, citas
- Timeline: 3-5 horas setup

### ✅ **E-commerce Básico**
- Catálogo de productos, contacto
- Timeline: 4-8 horas setup

## 🔧 Características Técnicas

### **Frontend**
- ✅ Responsive design completo
- ✅ Performance optimizado (<2s load)
- ✅ SEO friendly
- ✅ Formulario de contacto funcional

### **Admin Panel**
- ✅ Editor visual sin código
- ✅ Galería multimedia
- ✅ Gestión de mensajes
- ✅ Backup automático

### **Deployment**
- ✅ Docker containerizado
- ✅ SSL automático (Let's Encrypt)
- ✅ CI/CD con GitHub Actions
- ✅ Monitoring incluido

## 📞 Soporte

### **Documentación Completa**
- 📋 `README.md` - Guía completa
- 🚀 `DEPLOY-DIGITALOCEAN.md` - Deploy en producción
- ⚙️ `setup.sh` - Configuración automática

### **Estructura del Proyecto**
```
cms-visual-template/
├── index.php                # Frontend principal
├── admin/                   # Panel administrativo
├── uploads/                 # Archivos multimedia
├── data.json               # Base de datos
├── docker-compose.yml      # Servicios Docker
├── .github/workflows/      # CI/CD automático
└── setup.sh               # Configuración inicial
```

---

## 🎉 ¡Empezar Ahora!

```bash
git clone https://github.com/tu-usuario/cms-visual-template.git
cd cms-visual-template
./setup.sh
```

**En 10 minutos tendrás un sitio web profesional corriendo.** 🚀

### **Próximo Paso**: Configurar tu primer cliente
1. Personalizar `data.json` con info del cliente
2. Subir logo e imágenes
3. Configurar dominio si es para producción
4. ¡Cobrar y entregar! 💰