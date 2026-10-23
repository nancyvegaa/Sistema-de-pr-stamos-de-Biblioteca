<?php include 'includes/header.php';
$mensaje = '';
if (isset($_GET['eliminar'])) {
    $conexion->prepare("UPDATE Autores SET Activo=0 WHERE IdAutor=?")->execute([(int)$_GET['eliminar']]);
    $mensaje = 'Autor dado de baja.';
}
$editando = null;
if (isset($_GET['editar'])) {
    $stmt = $conexion->prepare("SELECT * FROM Autores WHERE IdAutor=?");
    $stmt->execute([(int)$_GET['editar']]);
    $editando = $stmt->fetch();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $nombre = limpiar($_POST['nombre']);
    $nacionalidad = limpiar($_POST['nacionalidad']);
    if ($id > 0) {
        $conexion->prepare("UPDATE Autores SET NombreAutor=?, Nacionalidad=? WHERE IdAutor=?")->execute([$nombre, $nacionalidad, $id]);
        $mensaje = 'Autor actualizado.';
    } else {
        $conexion->prepare("INSERT INTO Autores (NombreAutor, Nacionalidad) VALUES (?,?)")->execute([$nombre, $nacionalidad]);
        $mensaje = 'Autor agregado.';
    }
}
$autores = $conexion->query("SELECT * FROM Autores WHERE Activo=1 ORDER BY NombreAutor")->fetchAll();
?>
<div class="tarjeta">
<h1>Autores</h1>
<?php if ($mensaje): ?><div class="alerta exito"><?php echo $mensaje; ?></div><?php endif; ?>
<form method="POST"><input type="hidden" name="id" value="<?php echo $editando['IdAutor'] ?? 0; ?>"><div class="form-grid"><div><label>Nombre</label><input name="nombre" required value="<?php echo $editando['NombreAutor'] ?? ''; ?>"></div><div><label>Nacionalidad</label><input name="nacionalidad" value="<?php echo $editando['Nacionalidad'] ?? ''; ?>"></div></div><br><button>Guardar</button><?php if($editando): ?> <a class="btn btn-sec" href="autores.php">Cancelar</a><?php endif; ?></form>
</div>
<div class="tarjeta"><h2>Listado</h2><table><tr><th>Nombre</th><th>Nacionalidad</th><th>Acciones</th></tr><?php foreach($autores as $a): ?><tr><td><?php echo $a['NombreAutor']; ?></td><td><?php echo $a['Nacionalidad']; ?></td><td class="acciones"><a class="btn btn-sec" href="?editar=<?php echo $a['IdAutor']; ?>">Editar</a><a class="btn btn-danger" href="?eliminar=<?php echo $a['IdAutor']; ?>" onclick="return confirm('¿Eliminar autor?')">Eliminar</a></td></tr><?php endforeach; ?></table></div>
<?php include 'includes/footer.php'; ?>
