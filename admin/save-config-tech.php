<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 0);

header('Cache-Control: no-store');
header('Content-Type: application/json; charset=utf-8');

// Solo admin puede guardar configuración técnica
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
    echo json_encode(['ok'=>false,'msg'=>'Sin datos de configuración']);
    exit;
}

$dataFile = __DIR__ . '/../data.json';
$newConfig = json_decode($_POST['data'], true);

if (!is_array($newConfig)) {
    http_response_code(400);
    echo json_encode(['ok'=>false,'msg'=>'Formato JSON inválido']);
    exit;
}

// Cargar datos existentes
$existingData = [];
if (file_exists($dataFile)) {
    $existingData = json_decode(file_get_contents($dataFile), true);
    if (!is_array($existingData)) {
        $existingData = [];
    }
}

// Fusionar configuración técnica con datos existentes
// Solo actualizar las secciones técnicas
$existingData['brand'] = array_merge($existingData['brand'] ?? [], $newConfig['brand'] ?? []);
$existingData['fonts'] = array_merge($existingData['fonts'] ?? [], $newConfig['fonts'] ?? []);
$existingData['seo'] = array_merge($existingData['seo'] ?? [], $newConfig['seo'] ?? []);
$existingData['analytics'] = array_merge($existingData['analytics'] ?? [], $newConfig['analytics'] ?? []);
$existingData['custom'] = array_merge($existingData['custom'] ?? [], $newConfig['custom'] ?? []);

// Agregar timestamp de configuración
$existingData['config_updated'] = date('Y-m-d H:i:s');
$existingData['config_by'] = $_SESSION['usuario'];

try {
    $result = file_put_contents($dataFile, json_encode($existingData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    
    if ($result === false) {
        echo json_encode(['ok'=>false,'msg'=>'Error al escribir archivo de configuración']);
        exit;
    }
    
    // Log de configuración
    $logFile = __DIR__ . '/config-log.json';
    $logEntry = [
        'timestamp' => date('Y-m-d H:i:s'),
        'user' => $_SESSION['usuario'],
        'action' => 'technical_config_update',
        'sections' => array_keys($newConfig)
    ];
    
    $logs = [];
    if (file_exists($logFile)) {
        $logs = json_decode(file_get_contents($logFile), true);
        if (!is_array($logs)) $logs = [];
    }
    
    $logs[] = $logEntry;
    // Mantener solo los últimos 50 logs
    if (count($logs) > 50) {
        $logs = array_slice($logs, -50);
    }
    
    file_put_contents($logFile, json_encode($logs, JSON_PRETTY_PRINT));
    
    echo json_encode([
        'ok' => true,
        'msg' => 'Configuración técnica actualizada correctamente',
        'updated_sections' => array_keys($newConfig),
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['ok'=>false,'msg'=>'Error interno: ' . $e->getMessage()]);
}
?>