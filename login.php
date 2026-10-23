<?php
require_once 'config/conexion.php';
iniciarSesionSiHaceFalta();

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = limpiar($_POST['correo'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $conexion->prepare("SELECT u.*, r.NombreRol FROM UsuariosSistema u INNER JOIN Roles r ON u.IdRol = r.IdRol WHERE u.Correo = ? AND u.Activo = 1");
    $stmt->execute([$correo]);
    $usuario = $stmt->fetch();

    if ($usuario && password_verify($password, $usuario['ContrasenaHash'])) {
        $_SESSION['usuario'] = $usuario['IdUsuario'];
        $_SESSION['nombre'] = $usuario['NombreCompleto'];
        $_SESSION['rol'] = $usuario['NombreRol'];
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Correo o contraseña incorrectos.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso Biblioteca FESC</title>
    <link rel="stylesheet" href="assets/css/estilos.css">
</head>
<body>
<div class="login">
    <div class="tarjeta">
        <h1>Biblioteca FESC</h1>
        <p>Inicia sesión para administrar libros, lectores y préstamos.</p>
        <?php if ($error): ?><div class="alerta error"><?php echo $error; ?></div><?php endif; ?>
        <form method="POST">
            <label>Correo</label>
            <input type="email" name="correo" required placeholder="admin@fesc.mx">
            <label>Contraseña</label>
            <input type="password" name="password" required placeholder="admin123">
            <br><br>
            <button type="submit">Entrar</button>
        </form>
        <small>Usuario inicial: admin@fesc.mx / admin123</small>
    </div>
</div>
</body>
</html>
