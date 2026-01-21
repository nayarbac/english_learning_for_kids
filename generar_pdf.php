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
    <title>Reporte de Pagos</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7fc;
            margin: 0;
            padding: 0;
            color: #333;
        }

        header {
            background-color: #4CAF50;
            color: white;
            padding: 20px;
            text-align: center;
        }

        h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
        }

        .container {
            max-width: 900px;
            margin: 40px auto;
            padding: 20px;
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        .pago-card {
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
            padding: 20px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .pago-card h3 {
            font-size: 18px;
            margin: 0;
        }

        .pago-card p {
            font-size: 16px;
            color: #555;
            margin: 5px 0;
        }

        .pago-card .precio {
            font-size: 18px;
            font-weight: bold;
            color: #4CAF50;
        }

        .pago-card .estado {
            padding: 5px 10px;
            border-radius: 5px;
            font-weight: bold;
            background-color: #f1f1f1;
        }

        .estado-activo {
            color: #4CAF50;
            background-color: #d4f1d4;
        }

        .estado-pendiente {
            color: #ff9800;
            background-color: #fff3e0;
        }

        .estado-fallido {
            color: #f44336;
            background-color: #ffebee;
        }

        .btn {
            display: inline-block;
            background-color: #4CAF50;
            color: white;
            padding: 12px 20px;
            text-align: center;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s ease, transform 0.2s ease;
            margin-top: 20px;
        }

        .btn:hover {
            background-color: #45a049;
            transform: translateY(-2px);
        }

        .btn:active {
            transform: translateY(0);
        }

        .no-pagos {
            text-align: center;
            font-size: 18px;
            color: #ff3333;
            margin-top: 30px;
        }

        .footer {
            text-align: center;
            margin-top: 40px;
            color: #aaa;
            font-size: 14px;
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

<header>
    <h1>Reporte de Pagos</h1>
</header>

 <!-- Contenido Principal -->
    <div class="container mt-2">
      <button onclick="history.back()" class="btn regresar-btn">
        <i class="fas fa-arrow-left"></i> Regresar
      </button>
    </div>

<div class="container">
    <?php if (!empty($pagos)): ?>
        <?php foreach ($pagos as $pago): ?>
            <div class="pago-card">
                <div>
                    <h3>Pago ID: <?php echo $pago['id_pago']; ?></h3>
                    <p><strong>Fecha:</strong> <?php echo $pago['fecha_pago']; ?></p>
                    <p><strong>Método de Pago:</strong> <?php echo $pago['metodo_pago']; ?></p>
                    <p><strong>Membresía:</strong> <?php echo $pago['membresia']; ?></p>
                </div>
                <div style="text-align: right;">
                    <p class="precio"><?php echo "$" . number_format($pago['precio'], 2); ?></p>
                    <div class="estado <?php echo strtolower($pago['estado']); ?>">
                        <?php echo $pago['estado']; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        <a href="#" class="btn" onclick="window.print()">Imprimir / Guardar como PDF</a>
    <?php else: ?>
        <p class="no-pagos">No se encontraron pagos para este usuario.</p>
    <?php endif; ?>
    <a href="ver_pago.php" class="btn">Volver</a>
</div>

<div class="footer">
    <p>© 2024 Plataforma de Aprendizaje</p>
</div>

</body>
</html>
