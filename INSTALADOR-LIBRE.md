# 👶 INSTALADOR LIBRE - MINI MANUAL PARA BEBÉS

**CMS Profesional en 10 comandos | Para Gabriel y cualquiera**

---

# 🏠 **OPCIÓN A: LOCAL** (Gratis - Solo tu computadora)

## **COPIAR Y PEGAR ESTOS 6 COMANDOS:**

### 1. Abre PowerShell (ventana negra)
**Windows+R → escribe `powershell` → Enter**

### 2. Ir a Documentos
```powershell
cd Documentos
```

### 3. Descargar CMS
```powershell
git clone https://github.com/Gabrielspr25/cms-visual-template.git mi-sitio
```

### 4. Entrar a la carpeta
```powershell
cd mi-sitio
```

### 5. Ejecutar instalador automático
```powershell
.\SETUP-FACIL.bat
```

### 6. Responder preguntas simples:
```
Nombre empresa: Mi Empresa
Email: info@miempresa.com
Teléfono: +1 555-123-4567
Dirección: Mi Ciudad
WhatsApp: 5551234567
Usuario admin: admin
Contraseña: mipassword123
```

## ✅ **RESULTADO LOCAL:**
- **Sitio**: http://localhost
- **Admin**: http://localhost/admin
- **Usuario**: admin | **Password**: mipassword123

---

# 🌐 **OPCIÓN B: INTERNET** ($6/mes - Todo el mundo puede verlo)

## **PARTE 1: Preparar código (2 comandos)**

### En PowerShell donde terminaste arriba:
```powershell
git add . && git commit -m "Mi sitio listo" && git push origin main
```

## **PARTE 2: Crear servidor (5 minutos en web)**

### 1. Ve a: **https://digitalocean.com**
- **Crear cuenta gratis**
- **Create → Droplets**
- **Ubuntu 20.04**
- **$6/month Basic Plan**
- **Crear SSH key**: En PowerShell: `ssh-keygen -t rsa -b 4096` (todo Enter)
- **Copiar**: `Get-Content C:\Users\Gabriel\.ssh\id_rsa.pub`
- **Pegar en DigitalOcean**
- **Create Droplet**
- **Anotar IP** (ej: 192.168.1.100)

## **PARTE 3: Conectar al servidor (1 comando)**
```powershell
ssh root@[LA-IP-DEL-DROPLET]
```

## **PARTE 4: Instalar todo (8 comandos - copiar uno por uno)**

```bash
apt update && apt upgrade -y
```

```bash
curl -fsSL https://get.docker.com | sh
```

```bash
curl -L "https://github.com/docker/compose/releases/download/v2.20.2/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose && chmod +x /usr/local/bin/docker-compose
```

```bash
mkdir -p /var/www/{uploads,backups} && touch /var/www/{data.json,mensajes.json} && chmod 666 /var/www/*.json && chmod 777 /var/www/uploads
```

```bash
cd /opt && git clone https://github.com/Gabrielspr25/cms-visual-template.git
```

```bash
cd cms-visual-template && cp .env.example .env
```

```bash
nano .env
```

### **Editar archivo .env** (cambiar solo estas líneas):
```
APP_NAME="Nombre del Cliente"
ADMIN_USERNAME="admin"  
ADMIN_PASSWORD="password_super_segura"
CONTACT_EMAIL="contacto@cliente.com"
```
**Salir: Ctrl+X, Y, Enter**

```bash
docker-compose up -d
```

## ✅ **RESULTADO INTERNET:**
- **Sitio**: http://[LA-IP-DEL-DROPLET]
- **Admin**: http://[LA-IP-DEL-DROPLET]/admin
- **Costo**: $6/mes
- **Visible**: Todo el mundo

---

# 🎨 **PERSONALIZAR SITIO**

## **Una vez funcionando (local o internet):**

### 1. Ir al admin:
- Local: http://localhost/admin
- Internet: http://[IP]/admin

### 2. Login:
- Usuario: admin
- Password: el que pusiste

### 3. Personalizar:
- **Galería**: Subir logo del cliente
- **Constructor**: Cambiar textos
- **Configuración**: Cambiar colores

---

# 💰 **PRECIOS PARA COBRAR**

## **Lo que puedes facturar:**
- ✅ **Sitio básico**: $500-800
- ✅ **Sitio empresarial**: $800-1500  
- ✅ **Portfolio**: $600-1200
- ✅ **Restaurante**: $800-1500
- ✅ **E-commerce**: $1200-2500

## **Mensual:**
- ✅ **Mantenimiento**: $50-200/mes
- ✅ **Hosting**: $15-30/mes (tu ganancia)

---

# 🆘 **SI ALGO FALLA**

## **Error: "git no funciona"**
```powershell
winget install Git.Git
```

## **Error: "docker no funciona"**
- Descargar: https://www.docker.com/products/docker-desktop

## **Error: "puerto ocupado"**
```powershell
# En docker-compose.yml cambiar "80:80" por "8080:80"
# Luego: http://localhost:8080
```

## **Error: "no puedo conectar SSH"**
```powershell
# Verificar IP correcta
# Verificar firewall DigitalOcean permite SSH
```

---

# 📋 **CHECKLIST RÁPIDO**

## **Para LOCAL:**
- [ ] PowerShell abierto
- [ ] Git instalado
- [ ] Docker instalado  
- [ ] 5 comandos ejecutados
- [ ] http://localhost funciona

## **Para INTERNET:**
- [ ] Droplet creado en DigitalOcean
- [ ] SSH conectado
- [ ] 8 comandos ejecutados
- [ ] http://[IP] funciona

---

# 🎉 **¡FELICIDADES!**

**Has creado un sitio web profesional que:**
- ✅ Se ve como WordPress pero carga 5x más rápido
- ✅ Tiene panel admin súper fácil
- ✅ No necesita actualizar nada nunca
- ✅ Te genera $500-2500 por cliente

## **PRÓXIMOS PASOS:**
1. **Personaliza** con datos del cliente
2. **Cobra** $500-2500
3. **Repite** con siguiente cliente
4. **Gana** $1000-5000/mes

---

# 📞 **SOPORTE GRATIS**

**¿No funciona algo?**

1. **Lee todo otra vez** (en serio)
2. **Verifica comandos** exactamente iguales
3. **Reinicia** la computadora
4. **Prueba otra vez**

**¿Aún no funciona?**
- Revisa que tienes Git y Docker instalados
- Verifica conexión a internet
- Asegúrate que PowerShell está como Administrador

---

**¡TÚ PUEDES! 💪 ¡ES MÁS FÁCIL DE LO QUE PARECE!**

---

**📧 Instalador creado por SS-Group - Uso libre para generar ingresos**