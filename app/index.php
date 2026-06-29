<?php
$host = getenv('DB_HOST') ?: 'db';
$port = getenv('DB_PORT') ?: '3306';
$db   = getenv('DB_NAME') ?: 'parque_vehicular';
$user = getenv('DB_USER') ?: 'parque_user';
$pass = getenv('DB_PASSWORD') ?: 'parque_pass';

$dsn = "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    $stmt = $pdo->query('SELECT mensaje, creado_en FROM ejemplo ORDER BY id DESC LIMIT 1');
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $estado = 'Conectado';
    $detalle = $row ? $row['mensaje'] : 'Sin datos de prueba';
} catch (PDOException $e) {
    $estado = 'Error de conexión';
    $detalle = $e->getMessage();
}

$ipServidor = $_SERVER['SERVER_ADDR'] ?? gethostbyname(gethostname());
$ipCliente  = $_SERVER['REMOTE_ADDR'] ?? 'desconocida';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Parque Vehicular</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: system-ui, sans-serif;
            max-width: 720px;
            margin: 2rem auto;
            padding: 0 1rem;
            background: #f4f6f8;
            color: #1a1a1a;
        }
        .card {
            background: #fff;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,.08);
            margin-bottom: 1rem;
        }
        h1 { margin-top: 0; font-size: 1.5rem; }
        .ok { color: #0a7; font-weight: 600; }
        .error { color: #c00; font-weight: 600; }
        code { background: #eee; padding: 2px 6px; border-radius: 4px; }
        ul { line-height: 1.8; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Sistema Parque Vehicular — Docker</h1>
        <p>Estado MySQL: <span class="<?= $estado === 'Conectado' ? 'ok' : 'error' ?>"><?= htmlspecialchars($estado) ?></span></p>
        <p><?= htmlspecialchars($detalle) ?></p>
    </div>
    <div class="card">
        <h2>Acceso por IP</h2>
        <p>Coloca aquí tu aplicación PHP en la carpeta <code>app/</code>.</p>
        <ul>
            <li>Aplicación: <code>http://TU_IP:8080</code></li>
            <li>phpMyAdmin: <code>http://TU_IP:8081</code></li>
            <li>MySQL (desde red local): <code>TU_IP:3306</code></li>
        </ul>
        <p>IP del cliente actual: <code><?= htmlspecialchars($ipCliente) ?></code></p>
    </div>
</body>
</html>
