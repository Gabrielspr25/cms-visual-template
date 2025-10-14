<?php
// MOMVISION • GUARDAR CONTENIDO Y MENSAJES
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 0);

header('Cache-Control: no-store');
header('Content-Type: application/json; charset=utf-8');

// --- Si viene desde el formulario del sitio (sin sesión admin)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombre'], $_POST['email'], $_POST['mensaje'])) {
    $msgFile = __DIR__ . '/mensajes.json';
    if (!file_exists($msgFile)) file_put_contents($msgFile, '[]');
    $msgs = json_decode(file_get_contents($msgFile), true);
    if (!is_array($msgs)) $msgs = [];

    $entry = [
        'nombre' => trim($_POST['nombre']),
        'email'  => trim($_POST['email']),
        'mensaje'=> trim($_POST['mensaje']),
        'fecha'  => date('Y-m-d H:i:s'),
        'ip'     => $_SERVER['REMOTE_ADDR'] ?? ''
    ];
    $msgs[] = $entry;

    file_put_contents($msgFile, json_encode($msgs, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    echo json_encode(['ok'=>true]);
    exit;
}

// --- Solo admin puede guardar contenido del panel
if (!isset($_SESSION['usuario'])) {
    http_response_code(403);
    echo json_encode(['ok'=>false,'msg'=>'Acceso denegado']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok'=>false,'msg'=>'Método no permitido']);
    exit;
}

if (empty($_POST['data'])) {
    http_response_code(400);
    echo json_encode(['ok'=>false,'msg'=>'Sin datos']);
    exit;
}

$dataFile = __DIR__ . '/../data.json';
$json = json_decode($_POST['data'], true);

if (!is_array($json)) {
    http_response_code(400);
    echo json_encode(['ok'=>false,'msg'=>'Formato JSON inválido']);
    exit;
}

file_put_contents($dataFile, json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
echo json_encode(['ok'=>true]);
?>
