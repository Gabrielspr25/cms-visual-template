@echo off
chcp 65001 >nul
title 🎨 CMS Visual Template - Setup Automático

echo.
echo 🎨 CMS VISUAL TEMPLATE - SETUP AUTOMÁTICO
echo ==========================================
echo.
echo ✅ Este script configurará tu CMS en 5 minutos
echo ✅ Solo necesitas responder unas preguntas
echo ✅ Al final tendrás un sitio web profesional
echo.
echo ⚠️  IMPORTANTE: Asegúrate de tener Docker instalado
echo    Si no tienes Docker: https://www.docker.com/products/docker-desktop
echo.
pause

echo.
echo 🚀 Iniciando configuración automática...
echo.

REM Ejecutar script PowerShell con política de ejecución bypass
powershell.exe -ExecutionPolicy Bypass -File "setup.ps1"

if %ERRORLEVEL% NEQ 0 (
    echo.
    echo ❌ Hubo un error durante la configuración
    echo 💡 Posibles soluciones:
    echo    1. Instalar Docker Desktop
    echo    2. Reiniciar como Administrador
    echo    3. Verificar conexión a internet
    echo.
    pause
    exit /b 1
)

echo.
echo 🎉 ¡Configuración completada exitosamente!
echo.
echo 📋 PRÓXIMOS PASOS:
echo ==================
echo 1. 🌐 Abre: http://localhost (tu sitio web)
echo 2. 🔐 Abre: http://localhost/admin (panel admin)
echo 3. 📁 Sube logo del cliente en Galería
echo 4. 🎨 Personaliza contenido en Constructor
echo 5. 💰 ¡Cobra $500-2500 al cliente!
echo.
echo 💡 TIP: Lee MANUAL-PARA-ADOLESCENTES.md si tienes dudas
echo.
pause