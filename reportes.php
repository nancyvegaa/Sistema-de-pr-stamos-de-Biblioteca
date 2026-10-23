<?php include 'includes/header.php';
$prestamosActivos = $conexion->query("SELECT * FROM vw_PrestamosDetalle WHERE Estado='Activo' ORDER BY FechaLimite")->fetchAll();
$vencidos = $conexion->query("SELECT * FROM vw_PrestamosDetalle WHERE Estado='Activo' AND FechaLimite < CAST(SYSDATETIME() AS DATE) ORDER BY FechaLimite")->fetchAll();
$librosBajos = $conexion->query("SELECT Titulo, ISBN, Disponibles, TotalEjemplares FROM Libros WHERE Estado='Activo' AND Disponibles <= 1 ORDER BY Disponibles")->fetchAll();
?>
<div class="tarjeta">
    <h1>Reportes</h1>
    <p>Consulta rápida para revisar préstamos activos, vencidos y libros con baja disponibilidad.</p>
</div>
<div class="tarjeta"><h2>Préstamos vencidos</h2><table><tr><th>Libro</th><th>Lector</th><th>Fecha límite</th><th>Días vencidos</th></tr><?php foreach($vencidos as $p): ?><tr><td><?php echo $p['Titulo']; ?></td><td><?php echo $p['Lector']; ?></td><td><?php echo $p['FechaLimite']; ?></td><td><?php echo $p['DiasVencido']; ?></td></tr><?php endforeach; ?></table></div>
<div class="tarjeta"><h2>Préstamos activos</h2><table><tr><th>Libro</th><th>Lector</th><th>Fecha límite</th><th>Registro</th></tr><?php foreach($prestamosActivos as $p): ?><tr><td><?php echo $p['Titulo']; ?></td><td><?php echo $p['Lector']; ?></td><td><?php echo $p['FechaLimite']; ?></td><td><?php echo $p['RegistradoPor']; ?></td></tr><?php endforeach; ?></table></div>
<div class="tarjeta"><h2>Libros con baja disponibilidad</h2><table><tr><th>Título</th><th>ISBN</th><th>Disponibles</th><th>Total</th></tr><?php foreach($librosBajos as $l): ?><tr><td><?php echo $l['Titulo']; ?></td><td><?php echo $l['ISBN']; ?></td><td><?php echo $l['Disponibles']; ?></td><td><?php echo $l['TotalEjemplares']; ?></td></tr><?php endforeach; ?></table></div>
<?php include 'includes/footer.php'; ?>
