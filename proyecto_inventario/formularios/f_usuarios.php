<form action="../guardar.php" method="POST">
    <input type="hidden" name="tabla" value="usuarios">
    <label>Usuario:</label>
    <input type="text" name="nombre_usuario" class="form-control" required>
    <label>Contrasena:</label>
    <input type="password" name="contrasena" class="form-control" required>
    <label>Nivel:</label>
    <select name="nivel_acceso" class="form-select">
        <option value="admin">Administrador</option>
        <option value="usuario">Usuario</option>
    </select>
    <button type="submit" class="btn btn-primary mt-3">Guardar</button>
</form>