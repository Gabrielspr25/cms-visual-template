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
    $data = json_decode($input, true);
    
    if (!$data || !isset($data['index'])) {
        throw new Exception('Índice de sección no proporcionado');
    }
    
    $index = intval($data['index']);
    
    // Leer data actual
    $dataFile = __DIR__ . '/../data.json';
    $siteData = file_exists($dataFile) ? json_decode(file_get_contents($dataFile), true) : [];
    if (!is_array($siteData)) $siteData = [];
    
    // Verificar que existe la sección
    if (!isset($siteData['secciones']) || !is_array($siteData['secciones'])) {
        throw new Exception('No hay secciones disponibles');
    }
    
    if (!isset($siteData['secciones'][$index])) {
        throw new Exception('Sección no encontrada');
    }
    
    // Obtener info de la sección antes de eliminar
    $seccionEliminada = $siteData['secciones'][$index];
    $tituloEliminado = $seccionEliminada['titulo'] ?? 'Sección sin título';
    
    // Eliminar la sección
    array_splice($siteData['secciones'], $index, 1);
    
    // Crear backup
    $backupFile = __DIR__ . '/../data_backup_delete_' . date('Y-m-d_H-i-s') . '.json';
    if (file_exists($dataFile)) {
        copy($dataFile, $backupFile);
    }
    
    // Guardar con lock
    $tempFile = $dataFile . '.tmp';
    if (file_put_contents($tempFile, json_encode($siteData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX) !== false) {
        if (rename($tempFile, $dataFile)) {
            echo json_encode([
                'success' => true, 
                'message' => "Sección \"$tituloEliminado\" eliminada correctamente",
                'sections_remaining' => count($siteData['secciones'])
            ]);
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