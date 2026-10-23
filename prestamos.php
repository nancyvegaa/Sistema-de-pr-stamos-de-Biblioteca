<?php include 'includes/header.php';
$mensaje = ''; $error = '';

if (isset($_GET['devolver'])) {
    $idPrestamo = (int)$_GET['devolver'];
    try {
        $conexion->beginTransaction();
        $stmt = $conexion->prepare("SELECT IdLibro FROM Prestamos WHERE IdPrestamo=? AND Estado='Activo'");
        $stmt->execute([$idPrestamo]);
        $idLibro = $stmt->fetchColumn();
        if (!$idLibro) { throw new Exception('El préstamo no existe o ya fue devuelto.'); }
        $conexion->prepare("UPDATE Prestamos SET Estado='Devuelto', FechaDevolucion=SYSDATETIME() WHERE IdPrestamo=?")->execute([$idPrestamo]);
        $conexion->prepare("UPDATE Libros SET Disponibles=Disponibles+1 WHERE IdLibro=?")->execute([$idLibro]);
        $conexion->commit();
        $mensaje = 'Devolución registrada correctamente.';
    } catch (Exception $e) {
        if ($conexion->inTransaction()) { $conexion->rollBack(); }
        $error = $e->getMessage();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idLibro = (int)$_POST['id_libro'];
    $idLector = (int)$_POST['id_lector'];
    $fechaLimite = $_POST['fecha_limite'];
    $observaciones = limpiar($_POST['observaciones'] ?? '');
    try {
        $conexion->beginTransaction();
        $stmt = $conexion->prepare("SELECT Disponibles FROM Libros WITH (UPDLOCK) WHERE IdLibro=? AND Estado='Activo'");
        $stmt->execute([$idLibro]);
        $disponibles = (int)$stmt->fetchColumn();
        if ($disponibles <= 0) { throw new Exception('No hay ejemplares disponibles de este libro.'); }
        $stmt = $conexion->prepare("INSERT INTO Prestamos (IdLibro, IdLector, IdUsuario, FechaLimite, Observaciones) VALUES (?,?,?,?,?)");
        $stmt->execute([$idLibro, $idLector, $_SESSION['usuario'], $fechaLimite, $observaciones]);
        $conexion->prepare("UPDATE Libros SET Disponibles=Disponibles-1 WHERE IdLibro=?")->execute([$idLibro]);
        $conexion->commit();
        $mensaje = 'Préstamo registrado correctamente.';
    } catch (Exception $e) {
        if ($conexion->inTransaction()) { $conexion->rollBack(); }
        $error = $e->getMessage();
    }
}

$libros = $conexion->query("SELECT IdLibro, Titulo, Disponibles FROM Libros WHERE Estado='Activo' ORDER BY Titulo")->fetchAll();
$lectores = $conexion->query("SELECT IdLector, NumCuenta, NombreCompleto FROM Lectores WHERE Activo=1 ORDER BY NombreCompleto")->fetchAll();
$prestamos = $conexion->query("SELECT * FROM vw_PrestamosDetalle ORDER BY FechaPrestamo DESC")->fetchAll();
?>
<div class="tarjeta">
    <h1>Préstamos y devoluciones</h1>
    <?php if ($mensaje): ?><div class="alerta exito"><?php echo $mensaje; ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alerta error"><?php echo $error; ?></div><?php endif; ?>
    <form method="POST">
        <div class="form-grid">
            <div><label>Libro</label><select name="id_libro" required><?php foreach($libros as $l): ?><option value="<?php echo $l['IdLibro']; ?>"><?php echo $l['Titulo'] . ' - Disponibles: ' . $l['Disponibles']; ?></option><?php endforeach; ?></select></div>
            <div><label>Lector</label><select name="id_lector" required><?php foreach($lectores as $lec): ?><option value="<?php echo $lec['IdLector']; ?>"><?php echo $lec['NumCuenta'] . ' - ' . $lec['NombreCompleto']; ?></option><?php endforeach; ?></select></div>
            <div><label>Fecha límite</label><input type="date" name="fecha_limite" required value="<?php echo date('Y-m-d', strtotime('+7 days')); ?>"></div>
            <div><label>Observaciones</label><input name="observaciones" placeholder="Opcional"></div>
        </div><br>
        <button type="submit">Registrar préstamo</button>
    </form>
</div>
<div class="tarjeta">
    <h2>Historial de préstamos</h2>
    <table>
        <tr><th>Libro</th><th>Lector</th><th>Préstamo</th><th>Límite</th><th>Estado</th><th>Acciones</th></tr>
        <?php foreach($prestamos as $p): ?>
        <tr>
            <td><?php echo $p['Titulo']; ?></td><td><?php echo $p['Lector']; ?></td><td><?php echo date('Y-m-d', strtotime($p['FechaPrestamo'])); ?></td><td><?php echo $p['FechaLimite']; ?></td><td><?php echo $p['Estado']; ?></td>
            <td><?php if ($p['Estado'] === 'Activo'): ?><a class="btn btn-ok" onclick="return confirm('¿Registrar devolución?')" href="?devolver=<?php echo $p['IdPrestamo']; ?>">Devolver</a><?php else: ?>Devuelto<?php endif; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
<?php include 'includes/footer.php'; ?>
