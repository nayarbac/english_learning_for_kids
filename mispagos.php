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

// Obtener los pagos del usuario actual
$id_usuario = $_SESSION['id_usuario'];
$sql_pagos = "SELECT * FROM pagos WHERE id_usuario = ?";
$stmt_pagos = $conn->prepare($sql_pagos);
$stmt_pagos->bind_param("i", $id_usuario);
$stmt_pagos->execute();
$result_pagos = $stmt_pagos->get_result();

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar a Premium</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }
        .container {
            margin-top: 50px;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .table {
            margin-top: 20px;
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
    <div class="container mt-5">
        <div class="card">
            <div class="card-header text-white bg-primary">
                Pagos Realizados
            </div>
            <div class="card-body">
                <p>A continuación, puedes ver todos los pagos realizados por tu cuenta:</p>
                <?php if ($result_pagos->num_rows > 0): ?>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>ID Pago</th>
                                <th>Fecha</th>
                                <th>Método</th>
                                <th>Monto</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($pago = $result_pagos->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $pago['id_pago']; ?></td>
                                    <td><?php echo $pago['fecha_pago']; ?></td>
                                    <td><?php echo $pago['metodo_pago']; ?></td>
                                    <td>$<?php echo number_format($pago['precio'], 2); ?></td>
                                    <td>
                                        <a href="detalle_pago.php?id_pago=<?php echo $pago['id_pago']; ?>" class="btn btn-primary btn-sm">Ver Detalle</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No se encontraron pagos realizados.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
