<?php
session_start();
if (empty($_SESSION['usuario'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'No autorizado']);
    exit;
}

header('Content-Type: application/json');

try {
    $uploadDir = __DIR__ . '/../uploads/';
    
    // Crear directorio si no existe
    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0777, true)) {
            throw new Exception('No se pudo crear el directorio uploads');
        }
    }
    
    // Verificar permisos
    if (!is_writable($uploadDir)) {
        chmod($uploadDir, 0777);
        if (!is_writable($uploadDir)) {
            throw new Exception('Directorio uploads sin permisos de escritura');
        }
    }
    
    $uploadedFiles = [];
    $errors = [];
    
    // Manejar múltiples archivos
    if (isset($_FILES['files'])) {
        $files = $_FILES['files'];
        $fileCount = count($files['name']);
        
        for ($i = 0; $i < $fileCount; $i++) {
            $fileName = $files['name'][$i];
            $tmpName = $files['tmp_name'][$i];
            $error = $files['error'][$i];
            $size = $files['size'][$i];
            
            // Verificar errores
            if ($error !== UPLOAD_ERR_OK) {
                $errors[] = "Error en archivo $fileName: " . $error;
                continue;
            }
            
            // Verificar tamaño (max 10MB)
            if ($size > 10 * 1024 * 1024) {
                $errors[] = "Archivo $fileName demasiado grande (max 10MB)";
                continue;
            }
            
            // Verificar extensión
            $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg', 'mp4', 'webm', 'ogg'];
            $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            if (!in_array($ext, $allowed)) {
                $errors[] = "Formato no permitido para $fileName (permitidos: " . implode(', ', $allowed) . ")";
                continue;
            }
            
            // Generar nombre único
            $newName = 'file_' . date('Ymd_His') . '_' . mt_rand(1000, 9999) . '.' . $ext;
            $targetPath = $uploadDir . $newName;
            
            // Mover archivo
            if (move_uploaded_file($tmpName, $targetPath)) {
                $uploadedFiles[] = [
                    'original_name' => $fileName,
                    'new_name' => $newName,
                    'path' => 'uploads/' . $newName,
                    'size' => $size,
                    'type' => $ext
                ];
            } else {
                $errors[] = "No se pudo guardar $fileName";
            }
        }
    }
    // Manejar archivo único
    elseif (isset($_FILES['file'])) {
        $file = $_FILES['file'];
        
        // Verificar errores
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('Error en la subida: ' . $file['error']);
        }
        
        // Verificar tamaño
        if ($file['size'] > 10 * 1024 * 1024) {
            throw new Exception('Archivo demasiado grande (max 10MB)');
        }
        
        // Verificar extensión
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg', 'mp4', 'webm', 'ogg'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed)) {
            throw new Exception('Formato no permitido (permitidos: ' . implode(', ', $allowed) . ')');
        }
        
        // Generar nombre único
        $newName = 'file_' . date('Ymd_His') . '_' . mt_rand(1000, 9999) . '.' . $ext;
        $targetPath = $uploadDir . $newName;
        
        // Mover archivo
        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            $uploadedFiles[] = [
                'original_name' => $file['name'],
                'new_name' => $newName,
                'path' => 'uploads/' . $newName,
                'size' => $file['size'],
                'type' => $ext
            ];
        } else {
            throw new Exception('No se pudo guardar el archivo');
        }
    } else {
        throw new Exception('No se recibieron archivos');
    }
    
    // Respuesta
    if (!empty($uploadedFiles)) {
        echo json_encode([
            'success' => true,
            'message' => count($uploadedFiles) . ' archivo(s) subido(s) correctamente',
            'files' => $uploadedFiles,
            'errors' => $errors
        ]);
    } else {
        throw new Exception('No se pudo subir ningún archivo: ' . implode(', ', $errors));
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>