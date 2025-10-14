<?php
session_start();
if (empty($_SESSION['ok'])) { http_response_code(403); exit('No autorizado'); }

// Campos a guardar en content.json
$fields = [
  // Hero
  'hero_title','hero_text','cta_text',
  // About
  'about_title','about_text',
  // Lentes (sección + 4 tarjetas)
  'products_title',
  'product1_title','product1_text',
  'product2_title','product2_text',
  'product3_title','product3_text',
  'product4_title','product4_text',
  // Tecnología
  'tech_title','tech_p1','tech_p2',
  // Contacto
  'contact_title','contact_text'
];

$out = [];
foreach ($fields as $f) {
  $out[$f] = isset($_POST[$f]) ? trim($_POST[$f]) : '';
}

$path = __DIR__ . '/../content.json';
$tmp  = $path . '.tmp';

if (file_put_contents($tmp, json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX) !== false) {
  rename($tmp, $path);
  header('Location: dashboard.php');
  exit;
} else {
  http_response_code(500);
  echo 'No se pudo guardar.';
}
