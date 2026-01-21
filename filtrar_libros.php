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

// Consulta para filtrar libros por categoría
$consulta = "SELECT * FROM libros";
if ($filtro_categoria) {
    $consulta .= " WHERE id_categoria = " . intval($filtro_categoria);
}

$resultado = $conn->query($consulta);

// Comprobamos si la consulta tiene resultados
if ($resultado->num_rows > 0) {
    $libros = $resultado->fetch_all(MYSQLI_ASSOC);
} else {
    $libros = []; // Si no hay resultados, inicializa el array de libros como vacío
}

// Consulta para obtener las categorías disponibles
$consulta_categorias = "SELECT * FROM categorias_libros";
$resultado_categorias = $conn->query($consulta_categorias);
$categorias = $resultado_categorias->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Libros</title>
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
        h1 {
            margin: 0;
            font-size: 24px;
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
    </style>
</head>
<body>

<header>
    <h1>Reporte de Libros - Filtro por Categoría</h1>
</header>

<div class="container">
    <!-- Formulario de filtro -->
    <form method="GET" action="filtrar_libros.php">
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
    <?php if (!empty($libros)): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Título</th>
                    <th>Autor</th>
                    <th>Año</th>
                    <th>Editorial</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($libros as $libro): ?>
                    <tr>
                        <td><?php echo isset($libro['id_libro']) ? $libro['id_libro'] : 'N/A'; ?></td>
                        <td><?php echo isset($libro['titulo']) ? $libro['titulo'] : 'N/A'; ?></td>
                        <td><?php echo isset($libro['autor']) ? $libro['autor'] : 'N/A'; ?></td>
                        <td><?php echo isset($libro['anio']) ? $libro['anio'] : 'N/A'; ?></td>
                        <td><?php echo isset($libro['editorial']) ? $libro['editorial'] : 'N/A'; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <a href="javascript:void(0);" class="btn" onclick="generarPDF()">Descargar como PDF</a>
    <?php else: ?>
        <p>No se encontraron libros para este filtro.</p>
    <?php endif; ?>
</div>

<script>
function generarPDF() {
    var html = `
        <html>
        <head>
            <title>Reporte de Libros</title>
            <style>
                body {
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                    margin: 20px;
                    background-color: #f9f9f9;
                    color: #333;
                }
                h1 {
                    text-align: center;
                    color: #4CAF50;
                    margin-bottom: 20px;
                    font-size: 26px;
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
            </style>
        </head>
        <body>
        <h1>Reporte de Libros</h1>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Título</th>
                    <th>Autor</th>
                    <th>Año</th>
                    <th>Editorial</th>
                </tr>
            </thead>
            <tbody>`;

    <?php foreach ($libros as $libro): ?>
        html += `
                <tr>
                    <td><?php echo $libro['id_libro']; ?></td>
                    <td><?php echo $libro['titulo']; ?></td>
                    <td><?php echo $libro['autor']; ?></td>
                    <td><?php echo $libro['anio']; ?></td>
                    <td><?php echo $libro['editorial']; ?></td>
                </tr>`;
    <?php endforeach; ?>

    html += `
            </tbody>
        </table>
        <footer>
            Generado  el <?php echo date('d-m-Y H:i'); ?> para Learningles.
        </footer>
        </body>
        </html>`;

    // Abrir en una nueva ventana para imprimir como PDF
    var pdfWindow = window.open("", "_blank");
    pdfWindow.document.write(html);
    pdfWindow.document.close();
    pdfWindow.print();
}

</script>


</body>
</html>
