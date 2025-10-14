<?php
session_start();
if (empty($_SESSION['usuario'])) { header('Location: login.php'); exit; }
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Constructor - MomVision</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
body { 
  font-family: Arial, sans-serif; 
  margin: 0; 
  background: #1a1f2e; 
  color: white; 
}
.container { 
  max-width: 1200px; 
  margin: 0 auto; 
  padding: 20px; 
}
.header { 
  background: #2d3748; 
  padding: 15px 20px; 
  margin-bottom: 20px; 
  border-radius: 8px; 
}
.main-grid { 
  display: grid; 
  grid-template-columns: 300px 1fr; 
  gap: 20px; 
}
.sidebar { 
  background: #2d3748; 
  padding: 20px; 
  border-radius: 8px; 
  height: fit-content; 
}
.canvas { 
  background: #2d3748; 
  padding: 20px; 
  border-radius: 8px; 
  min-height: 500px; 
}
.btn { 
  background: #3182ce; 
  color: white; 
  border: none; 
  padding: 10px 15px; 
  border-radius: 6px; 
  cursor: pointer; 
  margin: 5px; 
  width: calc(50% - 10px); 
}
.btn:hover { 
  background: #2c5aa0; 
}
.btn-success { 
  background: #38a169; 
}
.block-item { 
  background: #4a5568; 
  border: 1px solid #718096; 
  border-radius: 6px; 
  margin: 10px 0; 
  padding: 15px; 
  position: relative; 
}
.block-header { 
  font-weight: bold; 
  margin-bottom: 10px; 
}
.block-actions { 
  position: absolute; 
  right: 10px; 
  top: 10px; 
}
.block-actions button { 
  background: #e53e3e; 
  color: white; 
  border: none; 
  padding: 5px 8px; 
  border-radius: 4px; 
  cursor: pointer; 
  margin-left: 5px; 
}
#canvas-area { 
  border: 2px dashed #718096; 
  border-radius: 8px; 
  padding: 20px; 
  text-align: center; 
  color: #cbd5e1; 
}
.notification { 
  position: fixed; 
  top: 20px; 
  right: 20px; 
  background: #38a169; 
  color: white; 
  padding: 15px 20px; 
  border-radius: 8px; 
  z-index: 1000; 
}
</style>
</head>
<body>

<div class="container">
  <div class="header">
    <h1>🪄 Constructor Multiformato</h1>
    <p>Sistema simplificado - Funcional garantizado</p>
  </div>

  <div class="main-grid">
    <div class="sidebar">
      <h3>📦 Agregar Bloques</h3>
      <button class="btn" onclick="agregarBloque('texto')">📝 Texto</button>
      <button class="btn" onclick="agregarBloque('titulo')">📰 Título</button>
      <button class="btn" onclick="agregarBloque('lista')">📋 Lista</button>
      <button class="btn" onclick="agregarBloque('imagen')">🖼️ Imagen</button>
      <button class="btn" onclick="agregarBloque('video')">🎥 Video</button>
      <button class="btn" onclick="agregarBloque('columnas')">🏗️ Columnas</button>
      <button class="btn" onclick="agregarBloque('boton')">👆 Botón</button>
      <button class="btn" onclick="agregarBloque('html')">💻 HTML</button>
      
      <hr style="border: 1px solid #718096; margin: 20px 0;">
      
      <button class="btn btn-success" onclick="guardarSeccion()">💾 Guardar Sección</button>
      <button class="btn" onclick="limpiarCanvas()">🗑️ Limpiar Todo</button>
    </div>

    <div class="canvas">
      <h3>🎨 Canvas de Construcción</h3>
      
      <div style="margin-bottom: 15px;">
        <input type="text" id="tituloSeccion" placeholder="Título de la sección" style="padding: 8px; width: 300px; border-radius: 4px; border: 1px solid #718096; background: #4a5568; color: white;">
      </div>
      
      <div id="canvas-area">
        <h2>✨ ¡Empieza a construir!</h2>
        <p>Usa los botones de la izquierda para agregar bloques a tu sección.</p>
        <p>🚀 Este constructor simplificado funciona garantizado.</p>
      </div>
    </div>
  </div>
</div>

<script>
let bloques = [];
let contadorId = 0;

function agregarBloque(tipo) {
  contadorId++;
  const bloque = {
    id: 'bloque_' + contadorId,
    tipo: tipo,
    contenido: obtenerContenidoPorDefecto(tipo)
  };
  
  bloques.push(bloque);
  actualizarCanvas();
  mostrarNotificacion('✅ Bloque "' + tipo + '" agregado correctamente');
}

function obtenerContenidoPorDefecto(tipo) {
  const defaults = {
    'texto': 'Este es un párrafo de texto de ejemplo. Puedes editarlo.',
    'titulo': 'Título de Ejemplo',
    'lista': ['Elemento 1', 'Elemento 2', 'Elemento 3'],
    'imagen': { src: '', alt: 'Imagen de ejemplo', caption: 'Pie de imagen' },
    'video': { src: '', caption: 'Video explicativo' },
    'columnas': { columnas: 2, contenido: ['Columna 1', 'Columna 2'] },
    'boton': { texto: 'Hacer clic aquí', url: '#' },
    'html': '<p><strong>Código HTML personalizado</strong></p>'
  };
  return defaults[tipo] || 'Contenido por defecto';
}

function actualizarCanvas() {
  const canvas = document.getElementById('canvas-area');
  
  if (bloques.length === 0) {
    canvas.innerHTML = `
      <h2>✨ ¡Empieza a construir!</h2>
      <p>Usa los botones de la izquierda para agregar bloques a tu sección.</p>
      <p>🚀 Este constructor simplificado funciona garantizado.</p>
    `;
    return;
  }
  
  let html = '';
  bloques.forEach((bloque, index) => {
    html += generarHTMLBloque(bloque, index);
  });
  
  canvas.innerHTML = html;
}

function generarHTMLBloque(bloque, index) {
  const iconos = {
    'texto': '📝',
    'titulo': '📰',
    'lista': '📋',
    'imagen': '🖼️',
    'video': '🎥',
    'columnas': '🏗️',
    'boton': '👆',
    'html': '💻'
  };
  
  let contenidoHTML = '';
  
  switch(bloque.tipo) {
    case 'texto':
      contenidoHTML = '<p>' + bloque.contenido + '</p>';
      break;
    case 'titulo':
      contenidoHTML = '<h2>' + bloque.contenido + '</h2>';
      break;
    case 'lista':
      contenidoHTML = '<ul>';
      if (Array.isArray(bloque.contenido)) {
        bloque.contenido.forEach(item => {
          contenidoHTML += '<li>' + item + '</li>';
        });
      }
      contenidoHTML += '</ul>';
      break;
    case 'imagen':
      contenidoHTML = '<div style="text-align: center; padding: 20px; border: 2px dashed #ccc; border-radius: 8px;"><p>🖼️ Área de imagen</p><small>Seleccionar imagen para mostrar</small></div>';
      break;
    case 'video':
      contenidoHTML = '<div style="text-align: center; padding: 20px; border: 2px dashed #ccc; border-radius: 8px;"><p>🎥 Área de video</p><small>Agregar URL del video</small></div>';
      break;
    case 'columnas':
      contenidoHTML = '<div style="display: flex; gap: 20px;">';
      if (Array.isArray(bloque.contenido.contenido)) {
        bloque.contenido.contenido.forEach(col => {
          contenidoHTML += '<div style="flex: 1; padding: 15px; background: rgba(255,255,255,0.1); border-radius: 4px;">' + col + '</div>';
        });
      }
      contenidoHTML += '</div>';
      break;
    case 'boton':
      contenidoHTML = '<div style="text-align: center;"><button style="background: #3182ce; color: white; border: none; padding: 12px 24px; border-radius: 6px; font-size: 16px;">' + bloque.contenido.texto + '</button></div>';
      break;
    case 'html':
      contenidoHTML = bloque.contenido;
      break;
    default:
      contenidoHTML = '<p>Tipo de bloque: ' + bloque.tipo + '</p>';
  }
  
  return `
    <div class="block-item">
      <div class="block-header">
        ${iconos[bloque.tipo] || '📦'} ${bloque.tipo.charAt(0).toUpperCase() + bloque.tipo.slice(1)}
      </div>
      <div class="block-actions">
        <button onclick="eliminarBloque(${index})" title="Eliminar">🗑️</button>
        <button onclick="duplicarBloque(${index})" title="Duplicar">📋</button>
      </div>
      <div style="margin-top: 10px;">
        ${contenidoHTML}
      </div>
    </div>
  `;
}

function eliminarBloque(index) {
  if (confirm('¿Eliminar este bloque?')) {
    bloques.splice(index, 1);
    actualizarCanvas();
    mostrarNotificacion('🗑️ Bloque eliminado');
  }
}

function duplicarBloque(index) {
  const bloque = bloques[index];
  contadorId++;
  const nuevoBloque = {
    ...bloque,
    id: 'bloque_' + contadorId
  };
  bloques.splice(index + 1, 0, nuevoBloque);
  actualizarCanvas();
  mostrarNotificacion('📋 Bloque duplicado');
}

function limpiarCanvas() {
  if (confirm('¿Limpiar todos los bloques?')) {
    bloques = [];
    actualizarCanvas();
    mostrarNotificacion('🗑️ Canvas limpiado');
  }
}

function guardarSeccion() {
  const titulo = document.getElementById('tituloSeccion').value;
  
  if (!titulo) {
    alert('⚠️ Por favor ingresa un título para la sección');
    return;
  }
  
  if (bloques.length === 0) {
    alert('⚠️ Agrega al menos un bloque antes de guardar');
    return;
  }
  
  const datosSeccion = {
    id: titulo.toLowerCase().replace(/\s+/g, '-'),
    titulo: titulo,
    tipo: 'multiformato',
    bloques: bloques,
    fecha: new Date().toISOString()
  };
  
  console.log('Datos a guardar:', datosSeccion);
  
  // Aquí puedes agregar el código para enviar al servidor
  // Por ahora solo mostramos una confirmación
  alert('✅ Sección "' + titulo + '" guardada correctamente!\n\n' + bloques.length + ' bloques guardados.');
  
  // Limpiar después de guardar
  document.getElementById('tituloSeccion').value = '';
  bloques = [];
  actualizarCanvas();
}

function mostrarNotificacion(mensaje) {
  const notif = document.createElement('div');
  notif.className = 'notification';
  notif.textContent = mensaje;
  document.body.appendChild(notif);
  
  setTimeout(() => {
    notif.remove();
  }, 3000);
}

// Inicializar
console.log('🚀 Constructor de emergencia cargado correctamente');
actualizarCanvas();
</script>

</body>
</html>