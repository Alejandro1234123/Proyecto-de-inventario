<?php include '../db.php'; ?> 
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../bootstrap.css">
    <link rel="stylesheet" href="..">
</head>
<form action="../guardar.php" method="POST">
    <input type="hidden" name="tabla" value="subcategorias">
    
    <label>Categoria Padre:</label>
    <select name="id_categoria" class="form-select" required>
        <option value="">Seleccione una categoria...</option>
        <?php
        $res = $pdo->query("SELECT id_categoria, nombre_categoria FROM categorias");
        
        while ($c = $res->fetch(PDO::FETCH_ASSOC)) {
            echo "<option value='{$c['id_categoria']}'>{$c['nombre_categoria']}</option>";
        }
        ?>
    </select>

    <label class="mt-2">Nombre Subcategoria:</label>
    <input type="text" name="nombre_subcategoria" class="form-control" required>
    
    <button type="submit" class="btn btn-primary mt-3">Guardar Subcategoria</button>
</form>