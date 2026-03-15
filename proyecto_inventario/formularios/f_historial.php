<?php include '../db.php'; 

$stmtArt = $pdo->query("SELECT id_articulo, serial FROM articulos");
$articulos = $stmtArt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Historial</title>
</head>
<body class="p-5">
    <form action="../guardar.php" method="POST">
        <input type="hidden" name="tabla" value="historial">

        <label>Seleccionar Articulo:</label>
        <select name="id_articulo" class="form-select mb-3">
            <?php foreach ($articulos as $a): ?>
                <option value="<?= $a['id_articulo'] ?>"><?= $a['serial'] ?></option>
            <?php endforeach; ?>
        </select>

        <label>Nuevo Estado:</label>
        <select name="estado_nuevo" class="form-select mb-3">
            <option value="excelente">Excelente</option>
            <option value="bueno">Bueno</option>
            <option value="regular">Regular</option>
            <option value="malo">Malo</option>
        </select>

        <button type="submit" class="btn btn-secondary">Actualizar Estado</button>
    </form>
</body>
</html>