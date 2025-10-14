<?php
// contact.php — Envía a info@momvision.com y GUARDA (simple) en admin/mensajes.json
mb_internal_encoding('UTF-8');
date_default_timezone_set('America/Puerto_Rico');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  exit('Método no permitido');
}

$name     = trim($_POST['name'] ?? '');
$email    = trim($_POST['email'] ?? '');
$message  = trim($_POST['message'] ?? '');
$honeypot = trim($_POST['website'] ?? ''); // campo oculto anti-bots

// Validaciones mínimas
if ($honeypot !== '') { http_response_code(400); exit('Solicitud inválida'); }
if ($name === '' || $email === '' || $message === '') { http_response_code(422); exit('Faltan campos'); }
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { http_response_code(422); exit('Email inválido'); }

// ----- GUARDAR EN JSON (simple) -----
$storePath = __DIR__ . '/admin/mensajes.json';
$reg = [
  'fecha'   => date('Y-m-d H:i:s'),
  'ip'      => $_SERVER['REMOTE_ADDR'] ?? 'desconocida',
  'nombre'  => $name,
  'email'   => $email,
  'mensaje' => $message
];

// Lee el archivo si existe; si no, arranca con []
$lista = [];
if (is_file($storePath)) {
  $contenido = @file_get_contents($storePath);
  $tmp = json_decode($contenido, true);
  if (is_array($tmp)) { $lista = $tmp; }
}
$lista[] = $reg;

// Intenta guardar (una sola operación)
$guardado = @file_put_contents(
  $storePath,
  json_encode($lista, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
  LOCK_EX
);

// ----- ENVIAR CORREO -----
$to = 'info@momvision.com';
$subject = 'Nuevo mensaje de contacto — MomVision';
$body  = "Has recibido un nuevo mensaje:\n\n";
$body .= "Nombre: {$name}\nEmail: {$email}\nFecha: ".date('Y-m-d H:i:s')."\n\n";
$body .= "Mensaje:\n{$message}\n";
$headers = [
  'MIME-Version: 1.0',
  'Content-Type: text/plain; charset=UTF-8',
  'From: MomVision <no-reply@momvision.com>',
  'Reply-To: ' . $email
];
@mail($to, '=?UTF-8?B?'.base64_encode($subject).'?=', $body, implode("\r\n", $headers));

// ----- RESPUESTA -----
echo '<!doctype html><meta charset="utf-8"><title>Gracias</title>
<style>body{font-family:system-ui;background:#0b0f14;color:#e5e7eb;display:grid;place-items:center;height:100vh}
.card{background:#111827;border:1px solid #1f2937;padding:24px;border-radius:14px;max-width:560px}
a{color:#93c5fd}</style>
<div class="card">
  <h2>¡Mensaje enviado!</h2>
  <p>Gracias, '.htmlspecialchars($name).'. Te responderemos pronto.</p>';

if (!$guardado) {
  echo '<p style="color:#fca5a5">Aviso: el mensaje no pudo guardarse localmente. Luego lo ajustamos.</p>';
} else {
  echo '<p>Se guardó una copia local.</p>';
}

echo '<p><a href="/">Volver al sitio</a></p></div>';
