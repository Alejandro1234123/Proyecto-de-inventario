<?php
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tabla = $_POST['tabla'];

    try {
        switch ($tabla) {
            case 'categorias':
                $sql = "INSERT INTO categorias (nombre_categoria, descripcion) VALUES (?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$_POST['nombre_categoria'], $_POST['descripcion']]);
                break;

            case 'departamentos':
                $sql = "INSERT INTO departamentos (nombre_departamento, responsable) VALUES (?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$_POST['nombre_departamento'], $_POST['responsable']]);
                break;

            case 'subcategorias':
                $sql = "INSERT INTO subcategorias (id_categoria, nombre_subcategoria) VALUES (?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$_POST['id_categoria'], $_POST['nombre_subcategoria']]);
                break;

            case 'usuarios':
                $pass = password_hash($_POST['contrasena'], PASSWORD_DEFAULT);
               
                $sql = "INSERT INTO usuarios (nombre_usuario, contraseña, nivel_acceso) VALUES (?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$_POST['nombre_usuario'], $pass, $_POST['nivel_acceso']]);
                break;

           case 'articulos':
                $sql = "INSERT INTO articulos (id_subcategoria, serial, estado, id_usuario_registro) VALUES (?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    $_POST['id_subcategoria'], 
                    $_POST['serial'], 
                    $_POST['estado'],
                    1 
                ]);
                break;

            case 'movimientos':
                $sql = "INSERT INTO movimientos (id_articulo, motivo) VALUES (?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$_POST['id_articulo'], $_POST['motivo']]);
                break;

            case 'historial': 
                $sql = "INSERT INTO historial_estados (id_articulo, estado_nuevo) VALUES (?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$_POST['id_articulo'], $_POST['estado_nuevo']]);
                break;
        }

        header("Location: formularios/f_" . $tabla . ".php?status=success");
        exit();

    } catch (PDOException $e) {
        echo "Error al guardar los datos: " . $e->getMessage();
    }
}
?>