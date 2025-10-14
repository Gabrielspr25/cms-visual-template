<?php
session_start();
if (empty($_SESSION['usuario'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'No autorizado']);
    exit;
}

header('Content-Type: application/json');

try {
    $input = file_get_contents('php://input');
    $nuevaData = json_decode($input, true);
    
    if (!$nuevaData) {
        throw new Exception('Datos inválidos');
    }
    
    // Leer data actual
    $dataFile = __DIR__ . '/../data.json';
    $data = file_exists($dataFile) ? json_decode(file_get_contents($dataFile), true) : [];
    if (!is_array($data)) $data = [];
    
    // Actualizar todas las secciones
    if (isset($nuevaData['secciones'])) {
        $data['secciones'] = $nuevaData['secciones'];
    }
    if (isset($nuevaData['contacto'])) {
        $data['contacto'] = $nuevaData['contacto'];
    }
    if (isset($nuevaData['form'])) {
        $data['form'] = $nuevaData['form'];
    }
    if (isset($nuevaData['socials'])) {
        $data['socials'] = $nuevaData['socials'];
    }
    if (isset($nuevaData['footer'])) {
        $data['footer'] = $nuevaData['footer'];
    }
    if (isset($nuevaData['fonts'])) {
        $data['fonts'] = $nuevaData['fonts'];
    }
    
    // Crear backup
    $backupFile = __DIR__ . '/../data_backup_' . date('Y-m-d_H-i-s') . '.json';
    if (file_exists($dataFile)) {
        copy($dataFile, $backupFile);
    }
    
    // Guardar con lock
    $tempFile = $dataFile . '.tmp';
    if (file_put_contents($tempFile, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX) !== false) {
        if (rename($tempFile, $dataFile)) {
            echo json_encode(['success' => true]);
        } else {
            throw new Exception('Error al mover archivo temporal');
        }
    } else {
        throw new Exception('Error al escribir archivo');
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>