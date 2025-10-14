# 🚀 DEPLOY DIGITALOCEAN - COPIAR Y PEGAR

**Para Gabriel - Sin interpretaciones, solo comandos exactos**

---

## 📋 **PASO 1: PREPARAR EL CÓDIGO** (5 minutos)

### En PowerShell donde estás:
```powershell
git add .
```

```powershell
git commit -m "Cliente listo para deploy"
```

```powershell
git push origin main
```

---

## 🌐 **PASO 2: CREAR DROPLET EN DIGITALOCEAN** (5 minutos)

### Ve a: https://digitalocean.com
1. **Crear cuenta** (si no tienes)
2. **Create → Droplets**  
3. **Ubuntu 20.04 LTS**
4. **Basic Plan → $6/month** (1GB RAM, 25GB SSD)
5. **Crear SSH Key** (si no tienes):
   - En PowerShell: `ssh-keygen -t rsa -b 4096`
   - Copiar contenido de: `C:\Users\Gabriel\.ssh\id_rsa.pub`
6. **Create Droplet**
7. **Anotar la IP** del droplet (ej: 192.168.1.100)

---

## 🔐 **PASO 3: CONFIGURAR SECRETS EN GITHUB** (3 minutos)

### Ve a: https://github.com/Gabrielspr25/cms-visual-template
1. **Settings → Secrets and variables → Actions**
2. **New repository secret** (repetir para cada uno):

**Secret 1:**
- Name: `DIGITALOCEAN_ACCESS_TOKEN`  
- Value: `[Tu token de DigitalOcean API]`

**Secret 2:**
- Name: `DO_REGISTRY_NAME`
- Value: `cms-registry`

**Secret 3:**  
- Name: `DO_HOST`
- Value: `[IP de tu droplet]` (ej: 192.168.1.100)

**Secret 4:**
- Name: `DO_USERNAME` 
- Value: `root`

**Secret 5:**
- Name: `DO_SSH_KEY`
- Value: `[Contenido de C:\Users\Gabriel\.ssh\id_rsa]`

---

## 🖥️ **PASO 4: CONFIGURAR EL DROPLET** (10 minutos)

### Conectar al droplet:
```powershell
ssh root@[IP-DEL-DROPLET]
```

### Ejecutar estos comandos **UNO POR UNO**:

```bash
apt update && apt upgrade -y
```

```bash
curl -fsSL https://get.docker.com -o get-docker.sh
```

```bash
sudo sh get-docker.sh
```

```bash
sudo curl -L "https://github.com/docker/compose/releases/download/v2.20.2/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
```

```bash
sudo chmod +x /usr/local/bin/docker-compose
```

```bash
mkdir -p /var/www/{uploads,backups,logs}
```

```bash
touch /var/www/{data.json,mensajes.json}
```

```bash
chmod 666 /var/www/{data.json,mensajes.json}
```

```bash
chmod 777 /var/www/uploads
```

```bash
apt install -y nginx certbot python3-certbot-nginx
```

```bash
ufw allow OpenSSH
```

```bash
ufw allow 'Nginx Full'
```

```bash
ufw --force enable
```

---

## 🌐 **PASO 5: CONFIGURAR DOMINIO** (5 minutos)

### Si tienes dominio (ej: micliente.com):
1. **En tu proveedor DNS** (GoDaddy, Namecheap, etc.)
2. **Crear registro A:**
   - Name: `@`
   - Value: `[IP-DEL-DROPLET]`
3. **Crear registro A:**
   - Name: `www` 
   - Value: `[IP-DEL-DROPLET]`

---

## 📁 **PASO 6: CLONAR CÓDIGO EN EL DROPLET** (3 minutos)

### En el droplet (conectado por SSH):
```bash
cd /opt
```

```bash
git clone https://github.com/Gabrielspr25/cms-visual-template.git
```

```bash
cd cms-visual-template
```

```bash
cp .env.example .env
```

```bash
nano .env
```

### Editar .env (cambiar estos valores):
```
APP_NAME="Nombre del Cliente"
APP_URL="https://micliente.com"
ENVIRONMENT="production"
ADMIN_USERNAME="admin"
ADMIN_PASSWORD="password_super_segura"
CONTACT_EMAIL="contacto@micliente.com"
```
**Salir con: Ctrl+X, Y, Enter**

---

## 🚀 **PASO 7: LEVANTAR EL SITIO** (2 minutos)

### En el droplet:
```bash
docker-compose up -d
```

```bash
docker ps
```

### Verificar que funciona:
```bash
curl -I http://localhost
```

---

## 🔒 **PASO 8: CONFIGURAR SSL** (5 minutos)

### Solo si tienes dominio:
```bash
nano /etc/nginx/sites-available/micliente
```

### Copiar y pegar esta configuración:
```nginx
server {
    listen 80;
    server_name micliente.com www.micliente.com;
    
    location / {
        proxy_pass http://localhost;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```
**Salir con: Ctrl+X, Y, Enter**

```bash
ln -s /etc/nginx/sites-available/micliente /etc/nginx/sites-enabled/
```

```bash
nginx -t
```

```bash
systemctl reload nginx
```

```bash
certbot --nginx -d micliente.com -d www.micliente.com
```

---

## ✅ **PASO 9: VERIFICAR** (1 minuto)

### Abrir en navegador:
- **Con dominio**: https://micliente.com
- **Sin dominio**: http://[IP-DEL-DROPLET]
- **Admin**: https://micliente.com/admin (o http://[IP]/admin)

---

## 🔄 **DEPLOY AUTOMÁTICO FUTURO**

### Para actualizar el sitio:
```powershell
git add .
git commit -m "Actualización cliente"
git push origin main
```

**¡GitHub Actions hace el resto automáticamente!**

---

## 💰 **COSTOS TOTALES:**

- **DigitalOcean Droplet**: $6/mes
- **Dominio** (opcional): $12/año
- **Total**: ~$6-7/mes

---

## 🆘 **COMANDOS DE EMERGENCIA**

### Ver logs si algo falla:
```bash
docker logs cms-visual
```

### Reiniciar todo:
```bash
docker-compose restart
```

### Ver estado:
```bash
docker ps -a
```

---

## 🎯 **RESULTADO:**

✅ **Sitio web profesional en internet**  
✅ **Panel admin funcional**  
✅ **SSL automático** (si tienes dominio)  
✅ **Deploy automático** con git push  
✅ **Backup automático** diario  

**¡Listo para facturar $500-2500!** 💰