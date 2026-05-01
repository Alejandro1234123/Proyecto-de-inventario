<?php
session_start();
require 'db.php';

$error_login      = '';
$error_registro   = '';
$success_registro = '';
$tab_activo       = 'login';

// ── LOGIN ──────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'login') {
    $tab_activo     = 'login';
    $nombre_usuario = isset($_POST['nombre_usuario']) ? trim($_POST['nombre_usuario']) : '';
    $contrasena     = isset($_POST['contrasena'])     ? $_POST['contrasena']           : '';

    if ($nombre_usuario === '' || $contrasena === '') {
        $error_login = 'Por favor completa todos los campos.';
    } else {
        $stmt = $pdo->prepare("SELECT id_usuario, nombre_completo, `contraseña` AS contrasena_hash, nivel_acceso FROM usuarios WHERE nombre_usuario = ? AND activo = 1");
        $stmt->execute([$nombre_usuario]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario && password_verify($contrasena, $usuario['contrasena_hash'])) {
            $_SESSION['id_usuario']      = $usuario['id_usuario'];
            $_SESSION['nombre_completo'] = $usuario['nombre_completo'];
            $_SESSION['nivel_acceso']    = $usuario['nivel_acceso'];
            header('Location: index.php');
            exit();
        } else {
            $error_login = 'Usuario o contraseña incorrectos.';
        }
    }
}

// ── REGISTRO ───────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'registro') {
    $tab_activo      = 'registro';
    $nombre_completo = isset($_POST['nombre_completo'])  ? trim($_POST['nombre_completo'])  : '';
    $nombre_usuario  = isset($_POST['nuevo_usuario'])    ? trim($_POST['nuevo_usuario'])    : '';
    $contrasena      = isset($_POST['nueva_contrasena']) ? $_POST['nueva_contrasena']       : '';
    $confirmar       = isset($_POST['confirmar'])        ? $_POST['confirmar']              : '';
    $nivel           = isset($_POST['nivel_acceso'])     ? $_POST['nivel_acceso']           : 'usuario';
    $codigo_ingresado= isset($_POST['codigo_admin'])     ? trim($_POST['codigo_admin'])     : '';

    $hay_error = false;

    if ($nombre_completo === '' || $nombre_usuario === '' || $contrasena === '' || $confirmar === '') {
        $error_registro = 'Por favor completa todos los campos.';
        $hay_error = true;
    } elseif (strlen($contrasena) < 6) {
        $error_registro = 'La contraseña debe tener al menos 6 caracteres.';
        $hay_error = true;
    } elseif ($contrasena !== $confirmar) {
        $error_registro = 'Las contraseñas no coinciden.';
        $hay_error = true;
    } elseif ($nivel === 'admin') {
        $stmtCfg = $pdo->prepare("SELECT valor FROM configuracion WHERE clave = 'codigo_admin'");
        $stmtCfg->execute();
        $codigo_correcto = $stmtCfg->fetchColumn();
        if ($codigo_ingresado !== $codigo_correcto) {
            $error_registro = 'El código de administrador es incorrecto.';
            $hay_error = true;
        }
    }

    if (!$hay_error) {
        $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE nombre_usuario = ?");
        $stmtCheck->execute([$nombre_usuario]);
        if ($stmtCheck->fetchColumn() > 0) {
            $error_registro = 'Ese nombre de usuario ya está en uso. Elige otro.';
        } else {
            $hash = password_hash($contrasena, PASSWORD_DEFAULT);
            $stmtIns = $pdo->prepare("INSERT INTO usuarios (nombre_usuario, `contraseña`, nombre_completo, nivel_acceso) VALUES (?, ?, ?, ?)");
            $stmtIns->execute([$nombre_usuario, $hash, $nombre_completo, $nivel]);
            $success_registro = '¡Cuenta creada con éxito! Ya puedes iniciar sesión.';
            $tab_activo = 'login';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventario — Acceso al Sistema</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --cyan:    #00bcd4;
            --cyan-d:  #0097a7;
            --cyan-l:  #e0f7fa;
            --bg:      #f4f7f6;
            --surface: #ffffff;
            --border:  #e2e8f0;
            --text:    #1a202c;
            --muted:   #718096;
            --red:     #e53935;
            --green:   #43a047;
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
            background-color: var(--bg);
            font-family: 'DM Sans', sans-serif;
            color: var(--text); overflow-x: hidden;
        }

        /* Fondo con patrón suave */
        .bg-grid {
            position: fixed; inset: 0;
            background-image:
                linear-gradient(rgba(0,188,212,0.06) 1px, transparent 1px),
                linear-gradient(90deg, rgba(0,188,212,0.06) 1px, transparent 1px);
            background-size: 48px 48px; z-index: 0;
        }
        .bg-glow {
            position: fixed; width: 700px; height: 700px; border-radius: 50%;
            background: radial-gradient(circle, rgba(0,188,212,0.10) 0%, transparent 65%);
            top: -200px; left: -200px; z-index: 0;
            animation: drift 9s ease-in-out infinite alternate;
        }
        .bg-glow-2 {
            position: fixed; width: 500px; height: 500px; border-radius: 50%;
            background: radial-gradient(circle, rgba(0,151,167,0.08) 0%, transparent 65%);
            bottom: -150px; right: -150px; z-index: 0;
            animation: drift 11s ease-in-out infinite alternate-reverse;
        }
        @keyframes drift {
            from { transform: translate(0,0); } to { transform: translate(40px,30px); }
        }

        .wrap {
            position: relative; z-index: 1;
            width: 100%; max-width: 440px; padding: 16px;
            animation: fadeUp 0.6s cubic-bezier(.22,1,.36,1) both;
        }
        @keyframes fadeUp {
            from { opacity:0; transform:translateY(24px); } to { opacity:1; transform:translateY(0); }
        }

        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 20px; padding: 36px 40px;
            box-shadow: 0 8px 40px rgba(0,188,212,0.10), 0 2px 12px rgba(0,0,0,0.06);
        }

        /* Franja cyan superior */
        .card-top-bar {
            height: 5px;
            background: linear-gradient(90deg, var(--cyan), var(--cyan-d));
            border-radius: 20px 20px 0 0;
            margin: -36px -40px 28px -40px;
        }

        .login-icon {
            width: 52px; height: 52px;
            background: linear-gradient(135deg, var(--cyan), var(--cyan-d));
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 24px; margin-bottom: 16px;
            box-shadow: 0 6px 20px rgba(0,188,212,0.30);
        }
        .login-title { font-family:'Playfair Display',serif; font-size:1.55rem; font-weight:700; color:var(--text); margin-bottom:4px; }
        .login-sub   { font-size:0.76rem; color:var(--muted); letter-spacing:0.05em; margin-bottom:26px; }

        /* Tabs */
        .tabs {
            display: flex; gap: 4px;
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: 12px; padding: 4px; margin-bottom: 26px;
        }
        .tab-btn {
            flex: 1; padding: 9px; background: none; border: none;
            border-radius: 9px; color: var(--muted);
            font-family: 'DM Sans', sans-serif; font-size: 0.88rem; font-weight: 500;
            cursor: pointer; transition: all 0.2s;
            display: flex; align-items: center; justify-content: center; gap: 6px;
        }
        .tab-btn.active { background: var(--cyan); color: white; box-shadow: 0 2px 10px rgba(0,188,212,0.35); }
        .tab-btn:not(.active):hover { color: var(--cyan); }

        .tab-panel { display: none; }
        .tab-panel.active { display: block; }

        /* Inputs */
        .form-group { margin-bottom: 16px; }
        .form-group label {
            display: block; font-size: 0.74rem; font-weight: 600;
            color: var(--muted); margin-bottom: 7px;
            letter-spacing: 0.06em; text-transform: uppercase;
        }
        .input-wrap { position: relative; }
        .input-wrap .ico {
            position: absolute; left: 13px; top: 50%;
            transform: translateY(-50%);
            color: #b0bec5; font-size: 0.95rem;
            pointer-events: none; transition: color 0.2s;
        }
        .input-wrap input {
            width: 100%;
            background: var(--bg);
            border: 1.5px solid var(--border); border-radius: 10px;
            color: var(--text); font-family: 'DM Sans', sans-serif;
            font-size: 0.92rem; padding: 11px 13px 11px 38px;
            outline: none; transition: border-color 0.2s, background 0.2s, box-shadow 0.2s;
        }
        .input-wrap input:focus {
            border-color: var(--cyan); background: var(--cyan-l);
            box-shadow: 0 0 0 3px rgba(0,188,212,0.12);
        }
        .input-wrap:focus-within .ico { color: var(--cyan); }
        .toggle-pw {
            position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
            background: none; border: none; color: #b0bec5;
            cursor: pointer; font-size: 0.95rem; padding: 0; transition: color 0.2s;
        }
        .toggle-pw:hover { color: var(--cyan); }

        /* Nivel */
        .nivel-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-bottom: 16px; }
        .nivel-opt input[type="radio"] { display: none; }
        .nivel-opt label {
            display: flex; flex-direction: column; align-items: center; gap: 5px;
            padding: 12px 8px; border: 1.5px solid var(--border); border-radius: 12px;
            cursor: pointer; transition: all 0.2s;
            background: var(--bg);
            text-align: center; font-size: 0.82rem; color: var(--muted);
        }
        .nivel-opt label i { font-size: 1.4rem; }
        .nivel-opt input[value="usuario"]:checked + label { border-color:var(--green); background:#f1f8f1; color:var(--green); }
        .nivel-opt input[value="admin"]:checked   + label { border-color:var(--red);   background:#fff5f5; color:var(--red); }
        .nivel-opt label:hover { border-color: var(--cyan); }

        /* Código admin */
        .codigo-box {
            background: #fff5f5;
            border: 1.5px solid rgba(229,57,53,0.25);
            border-radius: 12px; padding: 14px; margin-bottom: 16px; display: none;
        }
        .codigo-box.visible { display: block; }
        .codigo-label { font-size:0.73rem; font-weight:600; color:var(--red); letter-spacing:0.05em; text-transform:uppercase; margin-bottom:7px; display:block; }
        .codigo-box input {
            width: 100%; background: white;
            border: 1.5px solid rgba(229,57,53,0.3); border-radius: 8px;
            color: var(--text); font-family:'DM Sans',sans-serif;
            font-size: 0.92rem; padding: 10px 13px; outline: none;
            transition: border-color 0.2s, box-shadow 0.2s; letter-spacing: 0.1em;
        }
        .codigo-box input:focus { border-color:var(--red); box-shadow:0 0 0 3px rgba(229,57,53,0.10); }
        .codigo-hint { font-size:0.72rem; color:rgba(229,57,53,0.65); margin-top:6px; }

        /* Alerts */
        .alert { border-radius:10px; padding:11px 14px; margin-bottom:18px; font-size:0.84rem; display:flex; align-items:center; gap:8px; }
        .alert-error   { background:#ffebee; border:1px solid rgba(229,57,53,0.3); color:var(--red); animation:shake 0.4s; }
        .alert-success { background:#e8f5e9; border:1px solid rgba(67,160,71,0.3);  color:var(--green); }
        @keyframes shake {
            10%,90%{transform:translateX(-2px)} 20%,80%{transform:translateX(4px)}
            30%,70%{transform:translateX(-4px)} 40%,60%{transform:translateX(4px)} 50%{transform:translateX(-2px)}
        }

        .btn-submit {
            width:100%; padding:12px; border:none; border-radius:10px;
            background:linear-gradient(135deg,var(--cyan),var(--cyan-d));
            color:white; font-family:'DM Sans',sans-serif;
            font-size:0.93rem; font-weight:600; cursor:pointer;
            display:flex; align-items:center; justify-content:center; gap:8px;
            box-shadow:0 4px 16px rgba(0,188,212,0.30);
            transition:transform 0.15s, box-shadow 0.15s; margin-top:4px;
        }
        .btn-submit:hover { transform:translateY(-1px); box-shadow:0 6px 22px rgba(0,188,212,0.40); }
        .btn-submit:active { transform:translateY(0); }

        .footer { text-align:center; font-size:0.73rem; color:var(--muted); margin-top:20px; }
    </style>
</head>
<body>
<div class="bg-grid"></div>
<div class="bg-glow"></div>
<div class="bg-glow-2"></div>

<div class="wrap">
<div class="card">
    <div class="card-top-bar"></div>

    <div class="login-icon">📦</div>
    <div class="login-title">Bienvenido</div>
    <div class="login-sub">CONCEJO MUNICIPAL LIBERTADOR · SISTEMA DE INVENTARIO</div>

    <div class="tabs">
        <button type="button" class="tab-btn <?= $tab_activo === 'login'    ? 'active' : '' ?>" onclick="switchTab('login')">
            <i class="bi bi-box-arrow-in-right"></i> Iniciar Sesión
        </button>
        <button type="button" class="tab-btn <?= $tab_activo === 'registro' ? 'active' : '' ?>" onclick="switchTab('registro')">
            <i class="bi bi-person-plus"></i> Registrarse
        </button>
    </div>

    <!-- LOGIN -->
    <div class="tab-panel <?= $tab_activo === 'login' ? 'active' : '' ?>" id="panel-login">

        <?php if ($success_registro): ?>
        <div class="alert alert-success">
            <i class="bi bi-check-circle-fill"></i> <?= htmlspecialchars($success_registro) ?>
        </div>
        <?php endif; ?>

        <?php if ($error_login): ?>
        <div class="alert alert-error">
            <i class="bi bi-exclamation-triangle-fill"></i> <?= htmlspecialchars($error_login) ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <input type="hidden" name="accion" value="login">

            <div class="form-group">
                <label>Usuario</label>
                <div class="input-wrap">
                    <i class="bi bi-person ico"></i>
                    <input type="text" name="nombre_usuario" placeholder="nombre de usuario"
                           value="<?= (isset($_POST['accion']) && $_POST['accion']==='login' && isset($_POST['nombre_usuario'])) ? htmlspecialchars($_POST['nombre_usuario']) : '' ?>"
                           required autofocus>
                </div>
            </div>

            <div class="form-group">
                <label>Contraseña</label>
                <div class="input-wrap">
                    <i class="bi bi-lock ico"></i>
                    <input type="password" name="contrasena" id="pw1" placeholder="••••••••" required>
                    <button type="button" class="toggle-pw" onclick="togglePw('pw1','eye1')">
                        <i class="bi bi-eye" id="eye1"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn-submit">
                <i class="bi bi-box-arrow-in-right"></i> Ingresar al sistema
            </button>
        </form>
    </div>

    <!-- REGISTRO -->
    <div class="tab-panel <?= $tab_activo === 'registro' ? 'active' : '' ?>" id="panel-registro">

        <?php if ($error_registro): ?>
        <div class="alert alert-error">
            <i class="bi bi-exclamation-triangle-fill"></i> <?= htmlspecialchars($error_registro) ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <input type="hidden" name="accion" value="registro">

            <div class="form-group">
                <label>Nombre Completo</label>
                <div class="input-wrap">
                    <i class="bi bi-person-vcard ico"></i>
                    <input type="text" name="nombre_completo" placeholder="Ej: Juan Pérez"
                           value="<?= isset($_POST['nombre_completo']) ? htmlspecialchars($_POST['nombre_completo']) : '' ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label>Nombre de Usuario</label>
                <div class="input-wrap">
                    <i class="bi bi-at ico"></i>
                    <input type="text" name="nuevo_usuario" placeholder="Ej: jperez" autocomplete="off"
                           value="<?= isset($_POST['nuevo_usuario']) ? htmlspecialchars($_POST['nuevo_usuario']) : '' ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label>Contraseña</label>
                <div class="input-wrap">
                    <i class="bi bi-lock ico"></i>
                    <input type="password" name="nueva_contrasena" id="pw2" placeholder="Mínimo 6 caracteres" required>
                    <button type="button" class="toggle-pw" onclick="togglePw('pw2','eye2')">
                        <i class="bi bi-eye" id="eye2"></i>
                    </button>
                </div>
            </div>

            <div class="form-group">
                <label>Confirmar Contraseña</label>
                <div class="input-wrap">
                    <i class="bi bi-lock-fill ico"></i>
                    <input type="password" name="confirmar" id="pw3" placeholder="Repite la contraseña" required>
                    <button type="button" class="toggle-pw" onclick="togglePw('pw3','eye3')">
                        <i class="bi bi-eye" id="eye3"></i>
                    </button>
                </div>
            </div>

            <label style="font-size:0.75rem;font-weight:600;color:var(--muted);letter-spacing:0.06em;text-transform:uppercase;display:block;margin-bottom:8px;">
                Tipo de Cuenta
            </label>
            <div class="nivel-grid">
                <div class="nivel-opt">
                    <input type="radio" name="nivel_acceso" id="rol_u" value="usuario" checked onchange="toggleCodigo()">
                    <label for="rol_u"><i class="bi bi-person-fill"></i> Usuario Normal</label>
                </div>
                <div class="nivel-opt">
                    <input type="radio" name="nivel_acceso" id="rol_a" value="admin" onchange="toggleCodigo()">
                    <label for="rol_a"><i class="bi bi-shield-fill"></i> Administrador</label>
                </div>
            </div>

            <div class="codigo-box" id="codigoBox">
                <span class="codigo-label"><i class="bi bi-key-fill"></i> Código de Administrador</span>
                <input type="text" name="codigo_admin" id="codigoInput"
                       placeholder="Ingresa el código secreto" autocomplete="off">
                <div class="codigo-hint">⚠ Este código es proporcionado por el administrador del sistema.</div>
            </div>

            <button type="submit" class="btn-submit">
                <i class="bi bi-person-check-fill"></i> Crear Cuenta
            </button>
        </form>
    </div>

</div>
<div class="footer">Sistema de Inventario v1.0 · <?= date('Y') ?></div>
</div>

<script>
function switchTab(tab) {
    document.getElementById('panel-login').className    = 'tab-panel' + (tab === 'login'    ? ' active' : '');
    document.getElementById('panel-registro').className = 'tab-panel' + (tab === 'registro' ? ' active' : '');
    var btns = document.querySelectorAll('.tab-btn');
    btns[0].className = 'tab-btn' + (tab === 'login'    ? ' active' : '');
    btns[1].className = 'tab-btn' + (tab === 'registro' ? ' active' : '');
}

function togglePw(inputId, iconId) {
    var input = document.getElementById(inputId);
    var icon  = document.getElementById(iconId);
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'bi bi-eye';
    }
}

function toggleCodigo() {
    var adminRol = document.getElementById('rol_a');
    var box      = document.getElementById('codigoBox');
    var input    = document.getElementById('codigoInput');
    if (adminRol.checked) {
        box.className  = 'codigo-box visible';
        input.required = true;
    } else {
        box.className  = 'codigo-box';
        input.required = false;
        input.value    = '';
    }
}
</script>
</body>
</html>
