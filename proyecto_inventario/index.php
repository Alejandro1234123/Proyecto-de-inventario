<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inventario – Figma Design</title>
    <style>
        :root { --sidebar-color: #00bcd4; }
        Body { background-color: #f4f7f6; margin: 0; }
        .sidebar {
            Width: 250px; height: 100vh;
            Background-color: var(--sidebar-color);
            Position: fixed; color: white; padding: 20px;
        }
        .nav-link { color: white; margin-bottom: 10px; border-radius: 8px; }
        .nav-link:hover { background: rgba(255,255,255,0.2); }
        .main-content { margin-left: 250px; padding: 40px; }
    </style>
</head>
<body>

    <div class="sidebar">
        <h4 class="text-center mb-4">MI SISTEMA</h4>
        <nav class="nav flex-column">
            <a class="nav-link" href="formularios/f_articulos.php"><i class="bi bi-box"></i> Registrar Articulo</a>
            <a class="nav-link" href="formularios/f_categorias.php"><i class="bi bi-tags"></i> Categorias</a>
            <a class="nav-link" href="formularios/f_historial.php"><i class="bi bi-clock"></i> Historial</a>
        </nav>
    </div>

    <div class="main-content">
        <div class="card p-4 shadow-sm border-0" style="border-radius:15px;">
            <h2>Bienvenido al Panel</h2>
            <p>Selecciona una opción a la izquierda para comenzar.</p>
        </div>
    </div>

</body>
</html>