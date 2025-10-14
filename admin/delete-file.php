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
    
    if (!$data || empty($data['path'])) {
        throw new Exception('Ruta de archivo no proporcionada');
    }
    
    $filePath = __DIR__ . '/../' . $data['path'];
    
    // Verificar que el archivo está dentro del directorio uploads
    $uploadsDir = __DIR__ . '/../uploads/';
    $realFilePath = realpath($filePath);
    $realUploadsDir = realpath($uploadsDir);
    
    if (!$realFilePath || strpos($realFilePath, $realUploadsDir) !== 0) {
        throw new Exception('Ruta de archivo inválida');
    }
    
    if (!file_exists($filePath)) {
        throw new Exception('El archivo no existe');
    }
    
    if (unlink($filePath)) {
        echo json_encode(['success' => true, 'message' => 'Archivo eliminado correctamente']);
    } else {
        throw new Exception('No se pudo eliminar el archivo');
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>