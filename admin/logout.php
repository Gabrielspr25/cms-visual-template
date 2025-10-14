<?php
// logout.php — cierra la sesión y vuelve al login (sin caché)
session_start();

// Vaciar todas las variables de sesión
$_SESSION = [];

// Borrar la cookie de la sesión (si existe)
if (ini_get('session.use_cookies')) {
  $p = session_get_cookie_params();
  setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
}

// Destruir la sesión
session_destroy();

// Evitar caché del navegador (por si vuelve atrás)
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

// Redirigir al login (ruta absoluta)
header('Location: /admin/login.php');
exit;
