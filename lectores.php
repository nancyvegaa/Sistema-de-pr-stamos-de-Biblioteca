<?php include 'includes/header.php';
$mensaje = ''; $error = '';
if (isset($_GET['eliminar'])) {
    $stmt = $conexion->prepare("UPDATE Lectores SET Activo=0 WHERE IdLector=?");
    $stmt->execute([(int)$_GET['eliminar']]);
    $mensaje = 'Lector dado de baja correctamente.';
}
$editando = null;
if (isset($_GET['editar'])) {
    $stmt = $conexion->prepare("SELECT * FROM Lectores WHERE IdLector=?");
    $stmt->execute([(int)$_GET['editar']]);
    $editando = $stmt->fetch();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $datos = [limpiar($_POST['numcuenta']), limpiar($_POST['nombre']), limpiar($_POST['telefono']), limpiar($_POST['correo']), limpiar($_POST['carrera']), $_POST['semestre'] === '' ? null : (int)$_POST['semestre']];
    try {
        if ($id > 0) {
            $stmt = $conexion->prepare("UPDATE Lectores SET NumCuenta=?, NombreCompleto=?, Telefono=?, Correo=?, Carrera=?, Semestre=? WHERE IdLector=?");
            $stmt->execute([...$datos, $id]);
            $mensaje = 'Lector actualizado correctamente.';
        } else {
            $stmt = $conexion->prepare("INSERT INTO Lectores (NumCuenta, NombreCompleto, Telefono, Correo, Carrera, Semestre) VALUES (?,?,?,?,?,?)");
            $stmt->execute($datos);
            $mensaje = 'Lector registrado correctamente.';
        }
    } catch (PDOException $e) { $error = $e->getMessage(); }
}
$lectores = $conexion->query("SELECT * FROM Lectores WHERE Activo=1 ORDER BY NombreCompleto")->fetchAll();
?>
<div class="tarjeta">
    <h1><?php echo $editando ? 'Editar lector' : 'Registro de lectores'; ?></h1>
    <?php if ($mensaje): ?><div class="alerta exito"><?php echo $mensaje; ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alerta error"><?php echo $error; ?></div><?php endif; ?>
    <form method="POST">
        <input type="hidden" name="id" value="<?php echo $editando['IdLector'] ?? 0; ?>">
        <div class="form-grid">
            <div><label>Número de cuenta</label><input name="numcuenta" required value="<?php echo $editando['NumCuenta'] ?? ''; ?>"></div>
            <div><label>Nombre completo</label><input name="nombre" required value="<?php echo $editando['NombreCompleto'] ?? ''; ?>"></div>
            <div><label>Teléfono</label><input name="telefono" value="<?php echo $editando['Telefono'] ?? ''; ?>"></div>
            <div><label>Correo</label><input type="email" name="correo" value="<?php echo $editando['Correo'] ?? ''; ?>"></div>
            <div><label>Carrera</label><input name="carrera" value="<?php echo $editando['Carrera'] ?? ''; ?>"></div>
            <div><label>Semestre</label><input type="number" name="semestre" value="<?php echo $editando['Semestre'] ?? ''; ?>"></div>
        </div><br>
        <button type="submit">Guardar</button>
        <?php if ($editando): ?><a class="btn btn-sec" href="lectores.php">Cancelar</a><?php endif; ?>
    </form>
</div>
<div class="tarjeta">
    <h2>Lectores registrados</h2>
    <table>
        <tr><th>Núm. cuenta</th><th>Nombre</th><th>Correo</th><th>Carrera</th><th>Acciones</th></tr>
        <?php foreach($lectores as $l): ?>
        <tr>
            <td><?php echo $l['NumCuenta']; ?></td><td><?php echo $l['NombreCompleto']; ?></td><td><?php echo $l['Correo']; ?></td><td><?php echo $l['Carrera']; ?></td>
            <td class="acciones"><a class="btn btn-sec" href="?editar=<?php echo $l['IdLector']; ?>">Editar</a><a class="btn btn-danger" onclick="return confirm('¿Dar de baja este lector?')" href="?eliminar=<?php echo $l['IdLector']; ?>">Eliminar</a></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
<?php include 'includes/footer.php'; ?>
