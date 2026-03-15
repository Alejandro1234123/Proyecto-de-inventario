<?php include '../db.php'; ?>
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../bootstrap.css">
    <link rel="stylesheet" href="..">
</head>
<form action="../guardar.php" method="POST">
    <input type="hidden" name="tabla" value="articulos">

    <div class="row">
        <div class="col">
            <label>Serial:</label>
            <input type="text" name="serial" class="form-control" required>
        </div>
        <div class="col">
            <label>Estado:</label>
            <select name="estado" class="form-select" required>
                <option value="excelente">Excelente</option>
                <option value="bueno" selected>Bueno</option>
                <option value="regular">Regular</option>
                <option value="malo">Malo</option>
            </select>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col">
            <label>Subcategoria:</label>
            <select name="id_subcategoria" class="form-select" required>
                <option value="">Seleccione una subcategoria...</option>
                <?php
                
                $consulta = $pdo->query("SELECT id_subcategoria, nombre_subcategoria FROM subcategorias");
                while ($s = $consulta->fetch(PDO::FETCH_ASSOC)) {
                    echo '<option value="' . $s['id_subcategoria'] . '">' . $s['nombre_subcategoria'] . '</option>';
                }
                ?>
            </select>
        </div>
    </div>

    <button type="submit" class="btn btn-primary mt-4">Registrar Articulo</button>
</form>