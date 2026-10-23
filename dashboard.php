<?php include 'includes/header.php';
function contar($conexion, $sql) {
    return (int)$conexion->query($sql)->fetchColumn();
}
$totalLibros = contar($conexion, "SELECT COUNT(*) FROM Libros WHERE Estado='Activo'");
$totalLectores = contar($conexion, "SELECT COUNT(*) FROM Lectores WHERE Activo=1");
$prestamosActivos = contar($conexion, "SELECT COUNT(*) FROM Prestamos WHERE Estado='Activo'");
$vencidos = contar($conexion, "SELECT COUNT(*) FROM Prestamos WHERE Estado='Activo' AND FechaLimite < CAST(SYSDATETIME() AS DATE)");
?>
<div class="tarjeta">
    <h1>Panel principal</h1>
    <p>Resumen general del sistema de biblioteca.</p>
    <div class="grid">
        <div class="kpi"><strong><?php echo $totalLibros; ?></strong>Libros activos</div>
        <div class="kpi"><strong><?php echo $totalLectores; ?></strong>Lectores registrados</div>
        <div class="kpi"><strong><?php echo $prestamosActivos; ?></strong>Préstamos activos</div>
        <div class="kpi"><strong><?php echo $vencidos; ?></strong>Préstamos vencidos</div>
    </div>
</div>
<div class="tarjeta">
    <h2>Accesos rápidos</h2>
    <a class="btn" href="libros.php">Agregar libro</a>
    <a class="btn" href="lectores.php">Registrar lector</a>
    <a class="btn" href="prestamos.php">Nuevo préstamo</a>
    <a class="btn btn-sec" href="reportes.php">Ver reportes</a>
</div>
<?php include 'includes/footer.php'; ?>
