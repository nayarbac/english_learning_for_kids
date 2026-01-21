<?php
session_start();

// Verifica si el usuario ha iniciado sesión
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

// Verifica si se pasó un ID de pago
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
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de Pago</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<style>
          .regresar-btn {
        position: absolute;
        top: 80px;
        left: 20px;
        font-size: 1.2rem;
        padding: 10px 15px;
        border-radius: 10px;
        color: #fff;
        background-color: #ff6f61;
        font-weight: bold;
        border: none;
        cursor: pointer;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
      }

      /* Efecto hover */
      .regresar-btn:hover {
        transform: scale(1.1);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
      }
      </style>
<body>
        <!-- Contenido Principal -->
    <div class="container mt-2">
      <button onclick="history.back()" class="btn regresar-btn">
        <i class="fas fa-arrow-left"></i> Regresar
      </button>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header bg-primary text-white">
                Detalle del Pago
            </div>
            <div class="card-body">
                <p><strong>ID de Pago:</strong> <?php echo $detalle_pago['id_pago']; ?></p>
                <p><strong>Fecha de Pago:</strong> <?php echo $detalle_pago['fecha_pago']; ?></p>
                <p><strong>Método de Pago:</strong> <?php echo $detalle_pago['metodo_pago']; ?></p>
                <p><strong>Estado:</strong> <?php echo $detalle_pago['estado']; ?></p>
                <p><strong>Monto Total:</strong> $<?php echo number_format($detalle_pago['precio'], 2); ?></p>
                <p><strong>Número de Membresías:</strong> <?php echo $detalle_pago['membresia']; ?></p>
                <a href="descargar.php?id_pago=<?php echo $detalle_pago['id_pago']; ?>" class="btn btn-success">Descargar comprobante</a>
            </div>
        </div>
    </div>
</body>
</html>
