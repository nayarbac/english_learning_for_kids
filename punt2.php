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

// Obtener filtro de categoría
$filtro_categoria = isset($_GET['filtro_categoria']) ? $_GET['filtro_categoria'] : '';

// Consulta para filtrar puntuaciones por categoría
$consulta = "SELECT * FROM puntuaciones_imagenes";
if ($filtro_categoria) {
    $consulta .= " WHERE id_categoria = " . intval($filtro_categoria);
}

$resultado = $conn->query($consulta);

// Comprobamos si la consulta tiene resultados
if ($resultado->num_rows > 0) {
    $puntuaciones_imagenes = $resultado->fetch_all(MYSQLI_ASSOC);
} else {
    $puntuaciones_imagenes = []; // Si no hay resultados, inicializa el array de puntuaciones como vacío
}

// Consulta para obtener las categorías disponibles
$consulta_categorias = "SELECT * FROM categorias_imagenes";
$resultado_categorias = $conn->query($consulta_categorias);
$categorias = $resultado_categorias->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Puntuaciones por Categoría</title>
    <style>
        body {
            font-family: Arial, sans-serif;
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
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #4CAF50;
            color: white;
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
        select {
            padding: 10px;
            font-size: 16px;
            margin-right: 10px;
        }
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
    <h1>Reporte de Puntuaciones - Filtro por Categoría</h1>
</header>
<div class="container mt-2">
        <button onclick="window.location.href='admin.html'"  class="btn regresar-btn">
        <i class="fas fa-arrow-left"></i> Regresar
      </button>
<div class="container">
    <!-- Formulario de filtro -->
    <form method="GET" action="">
        <label for="filtro_categoria">Selecciona una categoría: </label>
        <select name="filtro_categoria" id="filtro_categoria">
            <option value="">Seleccionar categoría</option>
            <?php foreach ($categorias as $categoria): ?>
                <option value="<?php echo $categoria['id_categoria']; ?>" <?php echo ($filtro_categoria == $categoria['id_categoria']) ? 'selected' : ''; ?>>
                    <?php echo $categoria['nombre_categoria']; ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn">Aplicar Filtro</button>
    </form>

    <!-- Tabla de resultados -->
    <?php if (!empty($puntuaciones_imagenes)): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>ID Usuario</th>
                    <th>ID Categoría</th>
                    <th>Puntuación</th>
                    <th>Errores</th>
                    <th>Tiempo</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($puntuaciones_imagenes as $puntuacion): ?>
                    <tr>
                        <td><?php echo $puntuacion['id']; ?></td>
                        <td><?php echo $puntuacion['id_usuario']; ?></td>
                        <td><?php echo $puntuacion['id_categoria']; ?></td>
                        <td><?php echo $puntuacion['puntuacion']; ?></td>
                        <td><?php echo $puntuacion['errores']; ?></td>
                        <td><?php echo $puntuacion['tiempo']; ?></td>
                        <td><?php echo $puntuacion['fecha']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <a href="javascript:void(0);" class="btn" onclick="generarPDF()">Descargar como PDF</a>
    <?php else: ?>
        <p>No se encontraron puntuaciones para este filtro.</p>
    <?php endif; ?>
</div>

<script>
function generarPDF() {
    var html = `
        <html>
        <head>
            <title>Reporte de Puntuaciones por Categoría</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; color: #333; }
                h1 { text-align: center; color: #4CAF50; margin-bottom: 20px; }
                table { width: 100%; border-collapse: collapse; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: center; }
                th { background-color: #4CAF50; color: white; }
                footer { margin-top: 20px; text-align: center; font-size: 14px; color: #666; }
            </style>
        </head>
        <body>
        <h1>Reporte de Puntuaciones por Categoría</h1>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>ID Usuario</th>
                    <th>ID Categoría</th>
                    <th>Puntuación</th>
                    <th>Errores</th>
                    <th>Tiempo</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>`;
    <?php foreach ($puntuaciones_imagenes as $puntuacion): ?>
        html += `
                <tr>
                    <td><?php echo $puntuacion['id']; ?></td>
                    <td><?php echo $puntuacion['id_usuario']; ?></td>
                    <td><?php echo $puntuacion['id_categoria']; ?></td>
                    <td><?php echo $puntuacion['puntuacion']; ?></td>
                    <td><?php echo $puntuacion['errores']; ?></td>
                    <td><?php echo $puntuacion['tiempo']; ?></td>
                    <td><?php echo $puntuacion['fecha']; ?></td>
                </tr>`;
    <?php endforeach; ?>
    html += `
            </tbody>
        </table>
        <footer>Generado el <?php echo date('d-m-Y H:i'); ?>.</footer>
        </body>
        </html>`;

    var pdfWindow = window.open("", "_blank");
    pdfWindow.document.write(html);
    pdfWindow.document.close();
    pdfWindow.print();
}
</script>

</body>
</html>
