<?php
// test-json.php — Diagnóstico de data.json
$dataFile = __DIR__ . '/../data.json';
if (!file_exists($dataFile)) {
  die("❌ No se encontró el archivo data.json en la raíz del sitio.");
}
$content = file_get_contents($dataFile);
$data = json_decode($content, true);
if ($data === null) {
  echo "❌ Error en JSON: " . json_last_error_msg();
} else {
  echo "✅ JSON cargado correctamente con " . count($data['secciones'] ?? []) . " secciones.";
}
?>
