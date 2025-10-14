<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json; charset=utf-8');

$file = __DIR__ . '/mensajes.json';  // ajustar ruta si está en otra carpeta
$data = file_exists($file) ? json_decode(file_get_contents($file), true) : [];
if (!is_array($data)) $data = [];

$name   = trim($_POST['nombre'] ?? '');
$email  = trim($_POST['correo'] ?? $_POST['email'] ?? '');
$message = trim($_POST['mensaje'] ?? '');

if ($name === '' || $email === '' || $message === '') {
    echo json_encode(['ok' => false, 'error' => 'Por favor, completa todos los campos.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['ok' => false, 'error' => 'Correo inválido.']);
    exit;
}

$entry = [
    'nombre' => $name,
    'email'  => $email,
    'mensaje' => $message,
    'fecha'  => date('Y-m-d H:i:s')
];
$data[] = $entry;

if (false === file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))) {
    echo json_encode(['ok' => false, 'error' => 'No se pudo guardar el mensaje.']);
    exit;
}

// Envío de email opcional
$to = "info@momvision.com";
$subject = "Nuevo mensaje desde el sitio";
$body = "Nombre: $name\nEmail: $email\nMensaje:\n$message\nFecha: " . date('Y-m-d H:i:s');
$headers = "From: " . $email . "\r\n" .
           "Reply-To: " . $email . "\r\n" .
           "Content-Type: text/plain; charset=UTF-8";

@mail($to, $subject, $body, $headers);

echo json_encode(['ok' => true, 'msg' => 'Gracias por contactarnos. Te responderemos pronto.']);
