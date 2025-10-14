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
    
    if (!isset($sectionData['editIndex'])) {
        throw new Exception('Índice de sección a editar no proporcionado');
    }
    
    $editIndex = intval($sectionData['editIndex']);
    
    // Leer data actual
    $dataFile = __DIR__ . '/../data.json';
    $data = file_exists($dataFile) ? json_decode(file_get_contents($dataFile), true) : [];
    if (!is_array($data)) $data = [];
    
    // Verificar que existe la sección
    if (!isset($data['secciones']) || !is_array($data['secciones'])) {
        throw new Exception('No hay secciones disponibles');
    }
    
    if (!isset($data['secciones'][$editIndex])) {
        throw new Exception('Sección a editar no encontrada');
    }
    
    // Preservar campos importantes de la sección original
    $seccionOriginal = $data['secciones'][$editIndex];
    
    // Procesar sección especial: si es una sección con un solo bloque tipo colección,
    // convertirla al formato anterior para compatibilidad con el frontend
    if (count($sectionData['bloques']) === 1 && $sectionData['bloques'][0]['tipo'] === 'coleccion') {
        $bloqueColeccion = $sectionData['bloques'][0];
        
        $seccionActualizada = [
            'id' => $sectionData['id'],
            'titulo' => $sectionData['titulo'],
            'tipo' => 'coleccion',
            'bg' => $sectionData['color_fondo'] ?? '#ffffff',
            'border' => 1,
            'columns' => $bloqueColeccion['contenido']['columns'] ?? [],
            'show_in_menu' => $sectionData['mostrar_menu'] ?? true,
            'font' => 'body',
            'fecha_creacion' => $seccionOriginal['fecha_creacion'] ?? date('c'),
            'fecha_modificacion' => $sectionData['fecha_modificacion'] ?? date('c')
        ];
    } else {
        // Preparar datos de la sección multiformato actualizada
        $seccionActualizada = [
            'id' => $sectionData['id'],
            'titulo' => $sectionData['titulo'],
            'tipo' => 'multiformato',
            'color_fondo' => $sectionData['color_fondo'],
            'mostrar_menu' => $sectionData['mostrar_menu'],
            'bloques' => $sectionData['bloques'],
            'fecha_creacion' => $seccionOriginal['fecha_creacion'] ?? date('c'),
            'fecha_modificacion' => $sectionData['fecha_modificacion'] ?? date('c')
        ];
    }
    
    // Reemplazar la sección
    $data['secciones'][$editIndex] = $seccionActualizada;
    
    // Crear backup
    $backupFile = __DIR__ . '/../data_backup_update_' . date('Y-m-d_H-i-s') . '.json';
    if (file_exists($dataFile)) {
        copy($dataFile, $backupFile);
    }
    
    // Guardar con lock
    $tempFile = $dataFile . '.tmp';
    if (file_put_contents($tempFile, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX) !== false) {
        if (rename($tempFile, $dataFile)) {
            echo json_encode([
                'success' => true, 
                'message' => 'Sección actualizada correctamente',
                'section_title' => $sectionData['titulo'],
                'edit_index' => $editIndex
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