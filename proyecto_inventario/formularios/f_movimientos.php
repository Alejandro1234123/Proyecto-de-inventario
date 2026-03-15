<?php include '../db.php'; 

$stmtArt = $pdo->query("SELECT id_articulo, serial FROM articulos");
$articulos = $stmtArt->fetchAll(PDO::FETCH_ASSOC);

$stmtDep = $pdo->query("SELECT id_departamento, nombre_departamento FROM departamentos");
$departamentos = $stmtDep->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Movimientos</title>
</head>
<body class="bg-light p-5">
    <div class="container" style="max-width: 600px;">
        <form action="../guardar.php" method="POST" class="card shadow p-4">
            <h3 class="text-center mb-4">Transferencia de Articulo</h3>
            
            <input type="hidden" name="tabla" value="movimientos">

            <div class="mb-3">
                <label class="form-label fw-bold">Articulo (Serial):</label>
                <select name="id_articulo" class="form-select" required>
                    <option value="">-- Seleccione el articulo --</option>
                    <?php foreach ($articulos as $art): ?>
                        <option value="<?= $art['id_articulo'] ?>"><?= $art['serial'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Departamento Destino:</label>
                <select name="id_departamento_destino" class="form-select" required>
                    <option value="">-- Seleccione destino --</option>
                    <?php foreach ($departamentos as $dep): ?>
                        <option value="<?= $dep['id_departamento'] ?>"><?= $dep['nombre_departamento'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Motivo del Movimiento:</label>
                <textarea name="motivo" class="form-control" rows="3" placeholder="Ej: Reasignacion por mantenimiento..." required></textarea>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-success">Registrar Movimiento</button>
                <a href="../index.php" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</body>
</html>