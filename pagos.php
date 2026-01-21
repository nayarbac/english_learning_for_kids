<?php
// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "Informatica100*";
$dbname = "ingles";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Obtener todos los registros de la vista "vista_pagos"
$sql = "SELECT * FROM vista_pagos";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Pagos</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <style>
        body {
            background: linear-gradient(to right, #00b09b, #96c93d);
        }
        .button-container {
            position: absolute;
            top: 20px;
            left: 20px;
            display: flex;
            gap: 10px;
        }
        .regresar-btn, .inicio-btn {
            font-size: 1.2rem;
            padding: 10px 15px;
            border-radius: 10px;
            color: #fff;
            font-weight: bold;
            border: none;
            cursor: pointer;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .regresar-btn {
            background-color: #ff6f61;
        }
        .regresar-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
        }
        .inicio-btn {
            background-color: #007bff;
        }
        .inicio-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
        }
        .container {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
        }
        h2 {
            color: #2c3e50;
            font-weight: 700;
        }
        table {
            margin-top: 20px;
        }
        th, td {
            text-align: center;
        }
    </style>

    <div class="button-container">
        <button onclick="window.location.href='admin.html'" class="btn regresar-btn">
            <i class="fas fa-arrow-left"></i> Regresar
        </button>
        <button onclick="window.location.href='filtrar_pagos.php'" class="btn inicio-btn">
            <i class="fas fa-home"></i> Reportes
        </button>
    </div>

    <div class="container mt-4">
        <h2 class="text-center">Lista de Pagos</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID Pago</th>
                    <th>Nombre de Usuario</th>
                    <th>Fecha de Pago</th>
                    <th>Método de Pago</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id_pago']; ?></td>
                            <td><?php echo $row['nombre']; ?></td>
                            <td><?php echo $row['fecha_pago']; ?></td>
                            <td><?php echo $row['metodo_pago']; ?></td>
                            <td><?php echo $row['estado']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">No hay pagos registrados.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
// Cerrar conexión
$conn->close();
?>
