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

// Obtener las fechas del filtro
$fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : '';
$fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : '';

// Consulta para filtrar pagos por fecha
$consulta = "SELECT * FROM pagos";
if ($fecha_inicio && $fecha_fin) {
    $consulta .= " WHERE fecha_pago BETWEEN '$fecha_inicio' AND '$fecha_fin'";
} elseif ($fecha_inicio) {
    $consulta .= " WHERE fecha_pago >= '$fecha_inicio'";
} elseif ($fecha_fin) {
    $consulta .= " WHERE fecha_pago <= '$fecha_fin'";
}

$resultado = $conn->query($consulta);

// Comprobamos si la consulta tiene resultados
if ($resultado->num_rows > 0) {
    $pagos = $resultado->fetch_all(MYSQLI_ASSOC);
} else {
    $pagos = []; // Si no hay resultados, inicializa el array de pagos como vacío
}

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
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f9f9f9;
            color: #333;
            margin: 0;
            padding: 0;
        }
        header {
            background-color: #4CAF50;
            color: white;
            padding: 20px;
            text-align: center;
        }
        h1 {
            margin: 0;
            font-size: 26px;
            border-bottom: 2px solid #4CAF50;
            padding-bottom: 10px;
        }
        .container {
            max-width: 900px;
            margin: 40px auto;
            padding: 20px;
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: center;
            font-size: 14px;
        }
        th {
            background-color: #4CAF50;
            color: white;
            text-transform: uppercase;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #eaf4ea;
        }
        td {
            color: #555;
        }
        footer {
            margin-top: 20px;
            text-align: center;
            font-size: 14px;
            color: #666;
            padding-top: 10px;
            border-top: 1px solid #ddd;
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
            margin-top: 20px;
        }
        .btn:hover {
            background-color: #45a049;
        }
        input[type="date"] {
            padding: 10px;
            font-size: 16px;
            margin-right: 10px;
        }
                       /* Estilos para el botón de regresar */
      .regresar-btn {
        position: absolute;
        top: 20px;
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
        position: relative; /* Cambia de absolute a relative */
    display: inline-block; /* Asegura que los botones se alineen uno al lado del otro */
    margin: 10px; 
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
    <h1>Reporte de Pagos - Filtro por Fecha</h1>
</header>

  <div class="container mt-2">
    <!-- Botón de regresar -->
    <button onclick="window.location.href='admin.html'" class="btn regresar-btn">
        <i class="fas fa-arrow-left"></i> Regresar
    </button>

<div class="container">
    <!-- Formulario de filtro -->
    <form method="GET" action="filtrar_pagos.php">
        <label for="fecha_inicio">Fecha de inicio: </label>
        <input type="date" name="fecha_inicio" id="fecha_inicio" value="<?php echo $fecha_inicio; ?>" />
        <label for="fecha_fin">Fecha de fin: </label>
        <input type="date" name="fecha_fin" id="fecha_fin" value="<?php echo $fecha_fin; ?>" />
        <button type="submit" class="btn">Aplicar Filtro</button>
    </form>

    <!-- Tabla de resultados -->
    <?php if (!empty($pagos)): ?>
        <table>
            <thead>
                <tr>
                    <th>ID Pago</th>
                    <th>ID Usuario</th>
                    <th>Fecha de Pago</th>
                    <th>Método de Pago</th>
                    <th>Estado</th>
                    <th>Precio</th>
                    <th>Membresía</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pagos as $pago): ?>
                    <tr>
                        <td><?php echo $pago['id_pago']; ?></td>
                        <td><?php echo $pago['id_usuario']; ?></td>
                        <td><?php echo $pago['fecha_pago']; ?></td>
                        <td><?php echo $pago['metodo_pago']; ?></td>
                        <td><?php echo $pago['estado']; ?></td>
                        <td><?php echo $pago['precio']; ?></td>
                        <td><?php echo $pago['membresia']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <a href="javascript:void(0);" class="btn" onclick="generarPDF()">Descargar como PDF</a>
    <?php else: ?>
        <p>No se encontraron pagos para este filtro.</p>
    <?php endif; ?>
</div>

<script>
function generarPDF() {
    var html = `
        <html>
        <head>
            <title>Reporte de Pagos</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    margin: 20px;
                    color: #333;
                }
                h1 {
                    text-align: center;
                    color: #4CAF50;
                    margin-bottom: 20px;
                    font-size: 24px;
                    border-bottom: 2px solid #4CAF50;
                    padding-bottom: 10px;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 20px;
                }
                th, td {
                    border: 1px solid #ddd;
                    padding: 12px;
                    text-align: center;
                }
                th {
                    background-color: #4CAF50;
                    color: white;
                }
                tr:nth-child(even) {
                    background-color: #f2f2f2;
                }
                tr:hover {
                    background-color: #eaf4ea;
                }
                footer {
                    margin-top: 20px;
                    text-align: center;
                    font-size: 14px;
                    color: #666;
                }
            </style>
        </head>
        <body>
        <h1>Reporte de Pagos</h1>
        <table>
            <thead>
                <tr>
                    <th>ID Pago</th>
                    <th>ID Usuario</th>
                    <th>Fecha de Pago</th>
                    <th>Método de Pago</th>
                    <th>Estado</th>
                    <th>Precio</th>
                    <th>Membresía</th>
                </tr>
            </thead>
            <tbody>`;

    <?php foreach ($pagos as $pago): ?>
        html += `
                <tr>
                    <td><?php echo $pago['id_pago']; ?></td>
                    <td><?php echo $pago['id_usuario']; ?></td>
                    <td><?php echo $pago['fecha_pago']; ?></td>
                    <td><?php echo $pago['metodo_pago']; ?></td>
                    <td><?php echo $pago['estado']; ?></td>
                    <td><?php echo $pago['precio']; ?></td>
                    <td><?php echo $pago['membresia']; ?></td>
                </tr>`;
    <?php endforeach; ?>

    html += `
            </tbody>
        </table>
        <footer>
            Generado  el <?php echo date('d-m-Y H:i'); ?> para el sistema Learningles.
        </footer>
        </body>
        </html>`;

    var pdfWindow = window.open('', '_blank');
    pdfWindow.document.write(html);
    pdfWindow.document.close();
    pdfWindow.print();
}
</script>


</body>
</html>
