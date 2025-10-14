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
    $sectionData = json_decode($input, true);
    
    if (!$sectionData) {
        throw new Exception('Datos de sección inválidos');
    }
    
    // Validar campos requeridos
    if (empty($sectionData['titulo']) || empty($sectionData['bloques'])) {
        throw new Exception('Título y bloques son requeridos');
    }
    
    // Leer data actual
    $dataFile = __DIR__ . '/../data.json';
    $data = file_exists($dataFile) ? json_decode(file_get_contents($dataFile), true) : [];
    if (!is_array($data)) $data = [];
    
    // Inicializar secciones si no existen
    if (!isset($data['secciones'])) {
        $data['secciones'] = [];
    }
    
    // Procesar sección especial: si es una sección con un solo bloque tipo colección,
    // convertirla al formato anterior para compatibilidad con el frontend
    if (count($sectionData['bloques']) === 1 && $sectionData['bloques'][0]['tipo'] === 'coleccion') {
        $bloqueColeccion = $sectionData['bloques'][0];
        
        $seccionConvertida = [
            'id' => $sectionData['id'],
            'titulo' => $sectionData['titulo'],
            'tipo' => 'coleccion',
            'bg' => $sectionData['color_fondo'] ?? '#ffffff',
            'border' => 1,
            'columns' => $bloqueColeccion['contenido']['columns'] ?? [],
            'show_in_menu' => $sectionData['mostrar_menu'] ?? true,
            'font' => 'body'
        ];
        
        $data['secciones'][] = $seccionConvertida;
    } else {
        // Guardar como multiformato normal
        $data['secciones'][] = $sectionData;
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
            echo json_encode([
                'success' => true, 
                'message' => 'Sección multiformato guardada correctamente',
                'section_id' => $sectionData['id']
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