<?php
// Script temporal para verificar y corregir navegación

echo "<h2>🔧 Diagnóstico de Navegación</h2>\n";

$files_to_check = [
    'dashboard.php' => 'Dashboard Principal',
    'dashboard-new.php' => 'Dashboard Nuevo', 
    'constructor.php' => 'Constructor',
    'configuracion.php' => 'Configuración',
    'mensajes.php' => 'Mensajes',
    'galeria.php' => 'Galería'
];

echo "<table border='1' style='border-collapse: collapse; width: 100%; background: white; color: black;'>\n";
echo "<tr><th>Archivo</th><th>Descripción</th><th>Existe</th><th>Tamaño</th><th>Estado</th></tr>\n";

foreach ($files_to_check as $file => $desc) {
    $exists = file_exists(__DIR__ . '/' . $file);
    $size = $exists ? filesize(__DIR__ . '/' . $file) : 0;
    $status = $exists ? '✅ OK' : '❌ NO EXISTE';
    
    echo "<tr>";
    echo "<td>$file</td>";
    echo "<td>$desc</td>";
    echo "<td>" . ($exists ? 'SÍ' : 'NO') . "</td>";
    echo "<td>" . number_format($size) . " bytes</td>";
    echo "<td>$status</td>";
    echo "</tr>\n";
}

echo "</table>\n";

// Verificar el constructor actual
echo "<h3>🔍 Constructor Actual</h3>\n";
$constructor_content = file_get_contents(__DIR__ . '/constructor.php');

if (strpos($constructor_content, 'agregarBloque') !== false) {
    echo "<p style='color: green;'>✅ Constructor tiene la función agregarBloque</p>";
} else {
    echo "<p style='color: red;'>❌ Constructor NO tiene la función agregarBloque</p>";
}

if (strpos($constructor_content, 'dashboard.php') !== false) {
    echo "<p style='color: green;'>✅ Constructor apunta a dashboard.php</p>";
} else {
    echo "<p style='color: orange;'>⚠️ Constructor no apunta a dashboard.php</p>";
}

// Mostrar líneas problemáticas en constructor
echo "<h3>🔍 Verificando JavaScript en Constructor</h3>\n";
$lines = explode("\n", $constructor_content);
$js_functions = ['agregarBloque', 'testFuncionalidad', 'actualizarCanvas'];

foreach ($js_functions as $func) {
    $found = false;
    foreach ($lines as $num => $line) {
        if (strpos($line, "function $func") !== false) {
            echo "<p style='color: green;'>✅ Función $func encontrada en línea " . ($num + 1) . "</p>";
            $found = true;
            break;
        }
    }
    if (!$found) {
        echo "<p style='color: red;'>❌ Función $func NO encontrada</p>";
    }
}

echo "<hr>";
echo "<p><strong>Para usar este diagnóstico:</strong></p>";
echo "<ol>";
echo "<li>Sube todos los archivos a tu servidor</li>";
echo "<li>Accede a: http://tu-dominio.com/admin/fix-navigation.php</li>";
echo "<li>Revisa los resultados</li>";
echo "<li>Borra este archivo después del diagnóstico</li>";
echo "</ol>";
?>

<style>
body { 
    font-family: Arial, sans-serif; 
    margin: 20px; 
    background: #f5f5f5; 
}
table { 
    background: white; 
    box-shadow: 0 2px 10px rgba(0,0,0,0.1); 
}
th { 
    background: #3182ce; 
    color: white; 
    padding: 10px; 
}
td { 
    padding: 8px; 
    border: 1px solid #ddd; 
}
</style>