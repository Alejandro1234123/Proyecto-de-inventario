<form action="../guardar.php" method="POST">
    <input type="hidden" name="tabla" value="departamentos">
    <label>Nombre Departamento:</label>
    <input type="text" name="nombre_departamento" class="form-control" required>
    <label>Responsable:</label>
    <input type="text" name="responsable" class="form-control">
    <button type="submit" class="btn btn-primary mt-3">Guardar</button>
</form>