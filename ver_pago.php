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

$id_usuario = $_SESSION['id_usuario'];

// Consulta para obtener los datos de pago del usuario actual
$sql = "SELECT id_pago, fecha_pago, metodo_pago, precio, membresia, estado FROM pagos WHERE id_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();

// Verificar si hay datos de pago
$pagos = $result->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver PDF de Pago</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .container {
            margin-top: 50px;
        }
        .table {
            border: 1px solid #dee2e6;
            border-radius: 5px;
        }
                      /* Estilos para el botón de regresar */
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
</head>
<body>
        <!-- Contenido Principal -->
    <div class="container mt-2">
      <button onclick="history.back()" class="btn regresar-btn">
        <i class="fas fa-arrow-left"></i> Regresar
      </button>
    </div>
    <div class="container">
        <h1 class="text-center mb-4">Registro de Pagos</h1>
        <?php if (!empty($pagos)): ?>
            <table class="table table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>ID Pago</th>
                        <th>Fecha</th>
                        <th>Método</th>
                        <th>Precio Total</th>
                        <th>Membresías</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pagos as $pago): ?>
                        <tr>
                            <td><?php echo $pago['id_pago']; ?></td>
                            <td><?php echo $pago['fecha_pago']; ?></td>
                            <td><?php echo $pago['metodo_pago']; ?></td>
                            <td><?php echo "$" . $pago['precio']; ?></td>
                            <td><?php echo $pago['membresia']; ?></td>
                            <td><?php echo $pago['estado']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <a href="generar_pdf.php" class="btn btn-success mt-3">Descargar PDF</a>
        <?php else: ?>
            <div class="alert alert-warning">No se encontraron pagos para este usuario.</div>
        <?php endif; ?>
        <a href="actualizar.php" class="btn btn-secondary mt-3">Volver</a>
    </div>
</body>
</html>

