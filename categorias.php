<?php include 'includes/header.php';
$mensaje = '';
if (isset($_GET['eliminar'])) {
    $conexion->prepare("UPDATE Categorias SET Activo=0 WHERE IdCategoria=?")->execute([(int)$_GET['eliminar']]);
    $mensaje = 'Categoría dada de baja.';
}
$editando = null;
if (isset($_GET['editar'])) {
    $stmt = $conexion->prepare("SELECT * FROM Categorias WHERE IdCategoria=?");
    $stmt->execute([(int)$_GET['editar']]);
    $editando = $stmt->fetch();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $nombre = limpiar($_POST['nombre']);
    $descripcion = limpiar($_POST['descripcion']);
    if ($id > 0) {
        $conexion->prepare("UPDATE Categorias SET NombreCategoria=?, Descripcion=? WHERE IdCategoria=?")->execute([$nombre, $descripcion, $id]);
        $mensaje = 'Categoría actualizada.';
    } else {
        $conexion->prepare("INSERT INTO Categorias (NombreCategoria, Descripcion) VALUES (?,?)")->execute([$nombre, $descripcion]);
        $mensaje = 'Categoría agregada.';
    }
}
$categorias = $conexion->query("SELECT * FROM Categorias WHERE Activo=1 ORDER BY NombreCategoria")->fetchAll();
?>
<div class="tarjeta">
<h1>Categorías</h1>
<?php if ($mensaje): ?><div class="alerta exito"><?php echo $mensaje; ?></div><?php endif; ?>
<form method="POST"><input type="hidden" name="id" value="<?php echo $editando['IdCategoria'] ?? 0; ?>"><div class="form-grid"><div><label>Nombre</label><input name="nombre" required value="<?php echo $editando['NombreCategoria'] ?? ''; ?>"></div><div><label>Descripción</label><input name="descripcion" value="<?php echo $editando['Descripcion'] ?? ''; ?>"></div></div><br><button>Guardar</button><?php if($editando): ?> <a class="btn btn-sec" href="categorias.php">Cancelar</a><?php endif; ?></form>
</div>
<div class="tarjeta"><h2>Listado</h2><table><tr><th>Categoría</th><th>Descripción</th><th>Acciones</th></tr><?php foreach($categorias as $c): ?><tr><td><?php echo $c['NombreCategoria']; ?></td><td><?php echo $c['Descripcion']; ?></td><td class="acciones"><a class="btn btn-sec" href="?editar=<?php echo $c['IdCategoria']; ?>">Editar</a><a class="btn btn-danger" href="?eliminar=<?php echo $c['IdCategoria']; ?>" onclick="return confirm('¿Eliminar categoría?')">Eliminar</a></td></tr><?php endforeach; ?></table></div>
<?php include 'includes/footer.php'; ?>
