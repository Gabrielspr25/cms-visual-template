# 👦 INSTRUCCIONES SÚPER FÁCILES - PARA TU HIJO

**¡Hola! Estas son las instrucciones MÁS FÁCILES del mundo para configurar un sitio web profesional.**

---

## 🎯 **TU MISIÓN:** 
Crear un sitio web para un cliente en **10 minutos** y que papá cobre **$500-2500** 💰

---

## 📋 **LO QUE NECESITAS:**
- ✅ Una computadora
- ✅ Internet  
- ✅ 10 minutos
- ✅ Seguir EXACTAMENTE estos pasos

---

# 🚀 **MÉTODO 1: SÚPER FÁCIL (Recomendado)**

## **PASO 1:** Obtener el código (2 minutos)
1. Abre PowerShell:
   - Presiona `Windows + R`
   - Escribe `powershell`
   - Presiona Enter

2. Ve a Documentos:
   ```
   cd Documentos
   ```

3. Descarga el código:
   ```
   git clone https://github.com/Gabrielspr25/cms-visual-template.git cliente-nuevo
   ```

4. Entra al directorio:
   ```
   cd cliente-nuevo
   ```

## **PASO 2:** Ejecutar setup automático (5 minutos)
1. **Doble click** en `SETUP-FACIL.bat` ⭐ **¡ESTO ES LO MÁS FÁCIL!**

2. Sigue las preguntas que aparecen:
   ```
   Nombre de la empresa: [Escribe el nombre del cliente]
   Email de contacto: [Escribe el email del cliente]
   Teléfono: [Escribe el teléfono o presiona Enter]
   Dirección: [Escribe la dirección del cliente]
   WhatsApp: [Escribe solo números, ej: 5551234567]
   Usuario admin: admin
   Contraseña admin: [Inventa una contraseña]
   ```

3. **¡YA ESTÁ!** El script hace todo automáticamente

## **PASO 3:** Verificar (1 minuto)
- Se abrirán dos ventanas del navegador automáticamente:
  - `http://localhost` ← El sitio web del cliente
  - `http://localhost/admin` ← Panel de administración

---

# 🎨 **PERSONALIZAR PARA EL CLIENTE**

Una vez que el sitio esté funcionando:

## **Cambiar Logo:**
1. Ve a `http://localhost/admin`
2. Click en "Galería"
3. Sube el logo del cliente
4. Ve a "Configuración"
5. Cambia la ruta del logo

## **Cambiar Textos:**
1. Ve a "Constructor"
2. Click en "Editar" en cada sección
3. Cambia los textos por los del cliente
4. Click "Guardar"

## **Cambiar Colores:**
1. Ve a "Configuración"
2. Cambia los colores principales
3. Click "Guardar"

---

# ❗ **SI ALGO SALE MAL**

## **Error: "git no se reconoce"**
**Solución:** Ve a https://git-scm.com/download/win e instala Git

## **Error: "docker no se reconoce"**
**Solución:** Ve a https://www.docker.com/products/docker-desktop e instala Docker

## **Error: "Puerto 80 en uso"**
**Solución:**
1. Abre `docker-compose.yml` con Notepad
2. Cambia `"80:80"` por `"8080:80"`
3. Guarda el archivo
4. Ejecuta `docker-compose up -d` otra vez
5. Ahora ve a `http://localhost:8080`

## **El setup.ps1 no funciona**
**Solución:** En PowerShell ejecuta:
```powershell
Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope CurrentUser
```

---

# 💰 **PRECIOS PARA PAPÁ**

Dile a papá que puede cobrar:

- ✅ **Sitio básico**: $500-800 (1-2 horas trabajo)
- ✅ **Sitio corporativo**: $800-1500 (2-4 horas trabajo)  
- ✅ **Portfolio**: $600-1200 (2-3 horas trabajo)
- ✅ **Restaurante**: $800-1500 (3-4 horas trabajo)
- ✅ **E-commerce básico**: $1200-2500 (4-6 horas trabajo)

**PLUS mensual**: $50-200/mes por mantenimiento

---

# 🎉 **¡FELICIDADES!**

**Si llegaste hasta aquí, acabas de:**
- ✅ Crear un sitio web profesional
- ✅ Configurar un panel de administración
- ✅ Aprender tecnología moderna (Docker, PHP, etc.)
- ✅ Ayudar a papá a ganar $500-2500

**Eres un crack! 🏆**

---

# 📞 **AYUDA DE EMERGENCIA**

**Si NADA funciona:**

1. **Lee todo otra vez** (en serio, léelo todo)
2. **Verifica que tienes Docker instalado**
3. **Verifica que tienes Git instalado**  
4. **Reinicia la computadora**
5. **Prueba otra vez**

**Si aún no funciona:** Lee `MANUAL-PARA-ADOLESCENTES.md` (tiene más detalles)

---

**¡TÚ PUEDES! 💪**

**PD:** Cuando termines, dile a papá que tiene un hijo muy inteligente 😉