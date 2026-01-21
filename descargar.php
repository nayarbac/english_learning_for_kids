<?php
session_start();


if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

// Conexión a la base de datos
$host = 'localhost';
$usuario_db = 'root';
$contrasena_db = 'Informatica100*';
$nombre_db = 'ingles';

$conn = new mysqli($host, $usuario_db, $contrasena_db, $nombre_db);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}


if (!isset($_GET['id_pago'])) {
    die("ID de pago no proporcionado.");
}

$id_usuario = $_SESSION['id_usuario'];
$id_pago = (int)$_GET['id_pago'];

// Obtén los detalles del pago
$sql_detalle_pago = "SELECT * FROM pagos WHERE id_usuario = ? AND id_pago = ?";
$stmt_detalle = $conn->prepare($sql_detalle_pago);
$stmt_detalle->bind_param("ii", $id_usuario, $id_pago);
$stmt_detalle->execute();
$detalle_pago = $stmt_detalle->get_result()->fetch_assoc();

if (!$detalle_pago) {
    die("Pago no encontrado o no autorizado.");
}

$conn->close();

// Establece los encabezados para la descarga del archivo HTML
header("Content-Type: text/html");
header("Content-Disposition: attachment; filename=detalle_pago_$id_pago.html");

// Genera el contenido del archivo HTML
echo "
<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Detalle del Pago</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .header { background-color: #007BFF; color: white; padding: 10px; text-align: center; }
        .content { margin: 20px; font-size: 14px; }
        .footer { margin-top: 20px; font-size: 12px; text-align: center; color: gray; }
    </style>
</head>
<body>
    <div class='header'>
        <h1>Detalle del Pago</h1>
    </div>
    <div class='content'>
        <p><strong>ID de Pago:</strong> {$detalle_pago['id_pago']}</p>
        <p><strong>Fecha de Pago:</strong> {$detalle_pago['fecha_pago']}</p>
        <p><strong>Método de Pago:</strong> {$detalle_pago['metodo_pago']}</p>
        <p><strong>Estado:</strong> {$detalle_pago['estado']}</p>
        <p><strong>Monto Total:</strong> $" . number_format($detalle_pago['precio'], 2) . "</p>
        <p><strong>Número de Membresías:</strong> {$detalle_pago['membresia']}</p>
    </div>
    <div class='footer'>
        <p>Este documento fue generado automáticamente.</p>
    </div>
</body>
</html>
";
?>
