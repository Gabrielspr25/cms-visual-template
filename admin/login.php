<?php
// ========================================
// MOMVISION - LOGIN COMPLETO (v3)
// ========================================
session_start();
require_once("config.php");

error_reporting(E_ALL);
ini_set('display_errors', 1);

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($usuario === $usuario_admin && $password === $password_admin) {
        $_SESSION['usuario'] = $usuario;
        header("Location: dashboard.php");
        exit;
    } else {
        $mensaje = "❌ Usuario o contraseña incorrectos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Panel MomVision - Iniciar Sesión</title>
  <style>
    body {
      background: #0e1525;
      color: #fff;
      font-family: Poppins, sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }
    .login-box {
      background: #1e2a3a;
      padding: 30px;
      border-radius: 12px;
      width: 320px;
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.4);
    }
    h2 {
      text-align: center;
      color: #35e0c2;
      margin-bottom: 24px;
    }
    label {
      display: block;
      font-weight: 600;
      margin-bottom: 6px;
    }
    input {
      width: 100%;
      padding: 10px;
      border-radius: 8px;
      border: 1px solid #2c3a4f;
      background: #162334;
      color: #fff;
      margin-bottom: 12px;
    }
    button {
      width: 100%;
      background: #35e0c2;
      border: none;
      padding: 10px;
      border-radius: 8px;
      color: #0b1320;
      font-weight: 700;
      cursor: pointer;
      transition: 0.2s;
    }
    button:hover { background: #4fffd4; }
    .msg {
      background: rgba(255, 90, 90, 0.1);
      border-left: 3px solid #ff5a5a;
      padding: 10px;
      border-radius: 8px;
      color: #ffbcbc;
      margin-bottom: 12px;
      text-align: center;
    }
  </style>
</head>
<body>
  <div class="login-box">
    <h2>Panel MomVision</h2>
    <?php if ($mensaje): ?><div class="msg"><?= htmlspecialchars($mensaje) ?></div><?php endif; ?>
    <form method="post">
      <label>Usuario</label>
      <input type="text" name="usuario" placeholder="admin" required />

      <label>Contraseña</label>
      <input type="password" name="password" placeholder="********" required />

      <button type="submit">Entrar</button>
    </form>
  </div>
</body>
</html>
