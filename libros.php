<?php include 'includes/header.php';
$mensaje = '';
$error = '';

$categorias = $conexion->query("SELECT IdCategoria, NombreCategoria FROM Categorias WHERE Activo=1 ORDER BY NombreCategoria")->fetchAll();
$editoriales = $conexion->query("SELECT IdEditorial, NombreEditorial FROM Editoriales WHERE Activo=1 ORDER BY NombreEditorial")->fetchAll();
$autores = $conexion->query("SELECT IdAutor, NombreAutor FROM Autores WHERE Activo=1 ORDER BY NombreAutor")->fetchAll();

if (isset($_GET['eliminar'])) {
    $id = (int)$_GET['eliminar'];
    try {
        $stmt = $conexion->prepare("UPDATE Libros SET Estado='Inactivo' WHERE IdLibro=?");
        $stmt->execute([$id]);
        $mensaje = 'Libro dado de baja correctamente.';
    } catch (PDOException $e) { $error = $e->getMessage(); }
}

$editando = null;
$autorLibro = null;
if (isset($_GET['editar'])) {
    $stmt = $conexion->prepare("SELECT * FROM Libros WHERE IdLibro=?");
    $stmt->execute([(int)$_GET['editar']]);
    $editando = $stmt->fetch();
    $stmt = $conexion->prepare("SELECT TOP 1 IdAutor FROM LibroAutor WHERE IdLibro=?");
    $stmt->execute([(int)$_GET['editar']]);
    $autorLibro = $stmt->fetchColumn();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $isbn = limpiar($_POST['isbn']);
    $titulo = limpiar($_POST['titulo']);
    $idCategoria = (int)$_POST['id_categoria'];
    $idEditorial = (int)$_POST['id_editorial'];
    $idAutor = (int)$_POST['id_autor'];
    $anio = $_POST['anio'] === '' ? null : (int)$_POST['anio'];
    $ubicacion = limpiar($_POST['ubicacion']);
    $total = (int)$_POST['total'];
    $disponibles = (int)$_POST['disponibles'];

    try {
        $conexion->beginTransaction();
        if ($id > 0) {
            $stmt = $conexion->prepare("UPDATE Libros SET ISBN=?, Titulo=?, IdCategoria=?, IdEditorial=?, AnioPublicacion=?, Ubicacion=?, TotalEjemplares=?, Disponibles=? WHERE IdLibro=?");
            $stmt->execute([$isbn, $titulo, $idCategoria, $idEditorial, $anio, $ubicacion, $total, $disponibles, $id]);
            $conexion->prepare("DELETE FROM LibroAutor WHERE IdLibro=?")->execute([$id]);
            $conexion->prepare("INSERT INTO LibroAutor (IdLibro, IdAutor) VALUES (?,?)")->execute([$id, $idAutor]);
            $mensaje = 'Libro actualizado correctamente.';
        } else { 
            $stmt = $conexion->prepare("INSERT INTO Libros (ISBN, Titulo, IdCategoria, IdEditorial, AnioPublicacion, Ubicacion, TotalEjemplares, Disponibles, Estado) OUTPUT INSERTED.IdLibro VALUES (?,?,?,?,?,?,?,?,'Activo')");
            $stmt->execute([$isbn, $titulo, $idCategoria, $idEditorial, $anio, $ubicacion, $total, $disponibles]);
            $nuevoId = $stmt->fetchColumn(); 
            $conexion->prepare("INSERT INTO LibroAutor (IdLibro, IdAutor) VALUES (?,?)")->execute([$nuevoId, $idAutor]); 
            $mensaje = 'Libro registrado correctamente.';
        }
        $conexion->commit();
    } catch (PDOException $e) {
        if ($conexion->inTransaction()) { $conexion->rollBack(); }
        $error = 'No se pudo guardar el libro: ' . $e->getMessage();
    }
}

$sql = "SELECT l.*, c.NombreCategoria, e.NombreEditorial,
        STUFF((SELECT ', ' + a.NombreAutor FROM LibroAutor la INNER JOIN Autores a ON la.IdAutor=a.IdAutor WHERE la.IdLibro=l.IdLibro FOR XML PATH(''), TYPE).value('.', 'NVARCHAR(MAX)'), 1, 2, '') AS Autores
        FROM Libros l
        INNER JOIN Categorias c ON l.IdCategoria=c.IdCategoria
        INNER JOIN Editoriales e ON l.IdEditorial=e.IdEditorial
        WHERE l.Estado='Activo'
        ORDER BY l.Titulo";
$libros = $conexion->query($sql)->fetchAll();
?>
<div class="tarjeta">
    <h1><?php echo $editando ? 'Editar libro' : 'Registro de libros'; ?></h1>
    <?php if ($mensaje): ?><div class="alerta exito"><?php echo $mensaje; ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alerta error"><?php echo $error; ?></div><?php endif; ?>
    <form method="POST">
        <input type="hidden" name="id" value="<?php echo $editando['IdLibro'] ?? 0; ?>">
        <div class="form-grid">
            <div><label>ISBN</label><input name="isbn" required value="<?php echo $editando['ISBN'] ?? ''; ?>"></div>
            <div><label>Título</label><input name="titulo" required value="<?php echo $editando['Titulo'] ?? ''; ?>"></div>
            <div><label>Categoría</label><select name="id_categoria" required><?php foreach($categorias as $c): ?><option value="<?php echo $c['IdCategoria']; ?>" <?php echo (($editando['IdCategoria'] ?? '') == $c['IdCategoria']) ? 'selected' : ''; ?>><?php echo $c['NombreCategoria']; ?></option><?php endforeach; ?></select></div>
            <div><label>Editorial</label><select name="id_editorial" required><?php foreach($editoriales as $e): ?><option value="<?php echo $e['IdEditorial']; ?>" <?php echo (($editando['IdEditorial'] ?? '') == $e['IdEditorial']) ? 'selected' : ''; ?>><?php echo $e['NombreEditorial']; ?></option><?php endforeach; ?></select></div>
            <div><label>Autor principal</label><select name="id_autor" required><?php foreach($autores as $a): ?><option value="<?php echo $a['IdAutor']; ?>" <?php echo ($autorLibro == $a['IdAutor']) ? 'selected' : ''; ?>><?php echo $a['NombreAutor']; ?></option><?php endforeach; ?></select></div>
            <div><label>Año</label><input type="number" name="anio" value="<?php echo $editando['AnioPublicacion'] ?? ''; ?>"></div>
            <div><label>Ubicación</label><input name="ubicacion" value="<?php echo $editando['Ubicacion'] ?? ''; ?>"></div>
            <div><label>Total</label><input type="number" min="0" name="total" required value="<?php echo $editando['TotalEjemplares'] ?? 1; ?>"></div>
            <div><label>Disponibles</label><input type="number" min="0" name="disponibles" required value="<?php echo $editando['Disponibles'] ?? 1; ?>"></div>
        </div><br>
        <button type="submit">Guardar</button>
        <?php if ($editando): ?><a class="btn btn-sec" href="libros.php">Cancelar</a><?php endif; ?>
    </form>
</div>
<div class="tarjeta">
    <h2>Libros registrados</h2>
    <table>
        <tr><th>ISBN</th><th>Título</th><th>Autor</th><th>Categoría</th><th>Disponibles</th><th>Acciones</th></tr>
        <?php foreach($libros as $l): ?>
        <tr>
            <td><?php echo $l['ISBN']; ?></td><td><?php echo $l['Titulo']; ?></td><td><?php echo $l['Autores']; ?></td><td><?php echo $l['NombreCategoria']; ?></td><td><?php echo $l['Disponibles'] . '/' . $l['TotalEjemplares']; ?></td>
            <td class="acciones"><a class="btn btn-sec" href="?editar=<?php echo $l['IdLibro']; ?>">Editar</a><a class="btn btn-danger" onclick="return confirm('¿Dar de baja este libro?')" href="?eliminar=<?php echo $l['IdLibro']; ?>">Eliminar</a></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
<?php include 'includes/footer.php'; ?>
