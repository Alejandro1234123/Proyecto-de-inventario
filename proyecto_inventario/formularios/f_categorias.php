<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../bootstrap.css">
    <link rel="stylesheet" href="..">
</head>
<form action="../guardar.php" method="POST">
    <input type="hidden" name="tabla" value="categorias">
    <label>Nombre Categoria:</label>
    <input type="text" name="nombre_categoria" class="form-control" required>
    <label>Descripcion:</label>
    <textarea name="descripcion" class="form-control"></textarea>
    <button type="submit" class="btn btn-primary mt-3">Guardar</button>
</form>