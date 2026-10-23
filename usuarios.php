<?php include 'includes/header.php';
if (!esAdmin()) { echo '<div class="tarjeta"><h1>Acceso restringido</h1><p>Solo el administrador puede entrar a este módulo.</p></div>'; include 'includes/footer.php'; exit; }
$mensaje = ''; $error = '';
$roles = $conexion->query("SELECT * FROM Roles ORDER BY NombreRol")->fetchAll();
if (isset($_GET['desactivar'])) {
    $id = (int)$_GET['desactivar'];
    if ($id !== (int)$_SESSION['usuario']) {
        $conexion->prepare("UPDATE UsuariosSistema SET Activo=0 WHERE IdUsuario=?")->execute([$id]);
        $mensaje = 'Usuario desactivado.';
    } else { $error = 'No puedes desactivar tu propio usuario.'; }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = limpiar($_POST['nombre']);
    $correo = limpiar($_POST['correo']);
    $password = $_POST['password'];
    $idRol = (int)$_POST['id_rol'];
    try {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conexion->prepare("INSERT INTO UsuariosSistema (NombreCompleto, Correo, ContrasenaHash, IdRol) VALUES (?,?,?,?)");
        $stmt->execute([$nombre, $correo, $hash, $idRol]);
        $mensaje = 'Usuario creado correctamente.';
    } catch (PDOException $e) { $error = $e->getMessage(); }
}
$usuarios = $conexion->query("SELECT u.IdUsuario, u.NombreCompleto, u.Correo, r.NombreRol, u.Activo FROM UsuariosSistema u INNER JOIN Roles r ON u.IdRol=r.IdRol ORDER BY u.NombreCompleto")->fetchAll();
?>
<div class="tarjeta"><h1>Usuarios del sistema</h1><?php if($mensaje): ?><div class="alerta exito"><?php echo $mensaje; ?></div><?php endif; ?><?php if($error): ?><div class="alerta error"><?php echo $error; ?></div><?php endif; ?><form method="POST"><div class="form-grid"><div><label>Nombre</label><input name="nombre" required></div><div><label>Correo</label><input type="email" name="correo" required></div><div><label>Contraseña</label><input type="password" name="password" required></div><div><label>Rol</label><select name="id_rol"><?php foreach($roles as $r): ?><option value="<?php echo $r['IdRol']; ?>"><?php echo $r['NombreRol']; ?></option><?php endforeach; ?></select></div></div><br><button>Crear usuario</button></form></div>
<div class="tarjeta"><h2>Listado</h2><table><tr><th>Nombre</th><th>Correo</th><th>Rol</th><th>Activo</th><th>Acción</th></tr><?php foreach($usuarios as $u): ?><tr><td><?php echo $u['NombreCompleto']; ?></td><td><?php echo $u['Correo']; ?></td><td><?php echo $u['NombreRol']; ?></td><td><?php echo $u['Activo'] ? 'Sí' : 'No'; ?></td><td><?php if($u['Activo']): ?><a class="btn btn-danger" href="?desactivar=<?php echo $u['IdUsuario']; ?>" onclick="return confirm('¿Desactivar usuario?')">Desactivar</a><?php endif; ?></td></tr><?php endforeach; ?></table></div>
<?php include 'includes/footer.php'; ?>
