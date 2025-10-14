# 👶 MANUAL PARA ADOLESCENTES - CMS Setup

**Para tu hijo de 15 años (o cualquier principiante)**

---

## 🎯 **LO QUE VAMOS A HACER:**
Configurar un sitio web profesional para un cliente en **10 minutos**.

## 🛠️ **LO QUE NECESITAS:**
- ✅ Una computadora con internet
- ✅ 10 minutos de tiempo
- ✅ Seguir EXACTAMENTE estos pasos

---

# 📋 **PASO A PASO - NO TE SALTES NADA**

## **PASO 1: Obtener el Código** ⏱️ 2 minutos

### 1.1 Abrir Terminal/PowerShell:
- **Windows**: Presiona `Win + R` → Escribe `powershell` → Enter
- Te abrirá una ventana negra con texto blanco

### 1.2 Navegar a tus Documentos:
```powershell
cd C:\Users\$env:USERNAME\Documentos
```
**Copia y pega EXACTAMENTE esto** ⬆️

### 1.3 Clonar el Template:
```powershell
git clone https://github.com/Gabrielspr25/cms-visual-template.git cliente-nuevo
```
**Copia y pega EXACTAMENTE esto** ⬆️

### 1.4 Entrar al directorio:
```powershell
cd cliente-nuevo
```

---

## **PASO 2: Configurar para el Cliente** ⏱️ 5 minutos

### 2.1 Ejecutar Setup Automático:
**En Windows:**
```powershell
powershell -ExecutionPolicy Bypass -File setup.ps1
```

**En Mac/Linux:**
```bash
./setup.sh
```

### 2.2 El script te preguntará:
```
Nombre de la empresa: Mi Cliente SA
Email de contacto: info@micliente.com  
Teléfono: +1 555-123-4567
Dirección: Ciudad, País
WhatsApp: 5551234567
Usuario admin: admin
Contraseña admin: password123
```

**¡Responde cada pregunta y presiona Enter!**

---

## **PASO 3: Levantar el Sitio Web** ⏱️ 2 minutos

### 3.1 Levantar con Docker:
```powershell
docker-compose up -d
```

### 3.2 Verificar que funciona:
- Abre tu navegador
- Ve a: **http://localhost**
- ¡Deberías ver el sitio web del cliente!

---

## **PASO 4: Panel de Administración** ⏱️ 1 minuto

### 4.1 Acceder al admin:
- Ve a: **http://localhost/admin**
- Usuario: `admin` (o el que pusiste)
- Contraseña: `password123` (o la que pusiste)

### 4.2 Lo que puedes hacer:
- 📊 **Dashboard**: Ver estadísticas
- 🎨 **Constructor**: Editar contenido
- 📁 **Galería**: Subir imágenes
- 📧 **Mensajes**: Ver contactos

---

# 🎨 **PERSONALIZAR PARA EL CLIENTE**

## **Cambiar Logo:**
1. Ve a `/admin/galeria.php`
2. Sube el logo del cliente
3. Ve a `/admin/configuracion.php`
4. Cambia la ruta del logo

## **Cambiar Textos:**
1. Ve a `/admin/constructor.php`
2. Click en "Editar" en cada sección
3. Cambia textos por los del cliente
4. Guarda

## **Cambiar Colores:**
1. Ve a `/admin/configuracion.php`
2. En "Personalización Visual"
3. Cambia colores principales
4. Guarda

---

# 🚀 **SUBIR A INTERNET (Opcional)**

## **Para DigitalOcean:**

### 1. Crear cuenta en DigitalOcean.com
### 2. Crear Droplet ($6/mes):
- Ubuntu 20.04
- Basic Plan  
- 1GB RAM

### 3. En tu computadora:
```powershell
# Configurar variables para producción
cp .env.example .env
# Editar .env con datos reales del cliente
```

### 4. Subir código:
```powershell
git add .
git commit -m "Cliente: [Nombre del cliente]"
git push origin main
```

### 5. En el servidor:
```bash
git pull origin main
docker-compose up -d
```

---

# ❗ **PROBLEMAS COMUNES Y SOLUCIONES**

## **"git no se reconoce como comando"**
**Solución:** Instalar Git desde https://git-scm.com/download/win

## **"docker no se reconoce como comando"**  
**Solución:** Instalar Docker Desktop desde https://www.docker.com/products/docker-desktop

## **"Puerto 80 ya está en uso"**
**Solución:**
```powershell
# Cambiar puerto en docker-compose.yml
# De: "80:80" 
# A: "8080:80"
# Luego acceder a http://localhost:8080
```

## **"Permiso denegado en setup.sh"**
**Solución:**
```powershell
powershell -ExecutionPolicy Bypass -File setup.ps1
```

## **El sitio no carga**
**Solución:**
1. Verificar que Docker esté corriendo
2. Ejecutar: `docker ps` (debería mostrar contenedores)
3. Si no hay contenedores: `docker-compose up -d`

---

# 💰 **PRECIOS PARA COBRARLE AL CLIENTE**

## **Tiempo Estimado por Tipo:**
- ✅ **Sitio básico**: 1-2 horas = $500-800
- ✅ **Sitio corporativo**: 2-4 horas = $800-1500  
- ✅ **Portfolio/Galería**: 2-3 horas = $600-1200
- ✅ **Restaurante**: 3-4 horas = $800-1500
- ✅ **E-commerce básico**: 4-6 horas = $1200-2500

## **Costos Mensuales:**
- **Hosting DigitalOcean**: $6-12/mes
- **Mantenimiento**: $50-200/mes
- **Tu ganancia hosting**: $40-180/mes por cliente

---

# 📞 **SI ALGO NO FUNCIONA**

## **Checklist de Verificación:**
- [ ] ¿Git está instalado? (`git --version`)
- [ ] ¿Docker está corriendo? (`docker --version`)
- [ ] ¿Estás en el directorio correcto? (`ls` debería mostrar archivos)
- [ ] ¿El puerto 80 está libre? (cerrar otros servidores web)

## **Comandos de Emergencia:**
```powershell
# Parar todo
docker-compose down

# Reiniciar todo
docker-compose up -d

# Ver logs si algo falla
docker-compose logs

# Verificar qué está corriendo
docker ps
```

---

# 🎉 **¡FELICIDADES!**

**Si llegaste hasta aquí, ya sabes:**
- ✅ Configurar un CMS profesional
- ✅ Personalizarlo para clientes
- ✅ Subirlo a producción
- ✅ Cobrar $500-2500 por proyecto
- ✅ Ganar $50-200/mes recurrente

**Próximo paso:** ¡Buscar tu primer cliente y facturar! 💰

---

**¿Dudas?** Lee este manual 3 veces antes de preguntar. **¡Todo está aquí!** 📚