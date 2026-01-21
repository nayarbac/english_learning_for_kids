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

// Verificar si se ha solicitado eliminar un libro
if (isset($_GET['delete'])) {
    $id_libro = $_GET['delete'];

    // Eliminar el libro de la tabla 'libros'
    $stmt = $conn->prepare("DELETE FROM libros WHERE id_libro = ?");
    $stmt->bind_param("i", $id_libro);
    $stmt->execute();
    $stmt->close();

    // Redireccionar después de eliminar
    header("Location: CRUDlibros.php");
    exit();
}

// Verificar si se ha solicitado actualizar un libro
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $id_libro = $_POST['id_libro'];
    $titulo = $_POST['titulo'];
    $autor = $_POST['autor'];
    $anio = $_POST['anio'];
    $editorial = $_POST['editorial'];
    $texto_libro = $_POST['texto_libro'];
    $imagen = $_POST['imagen'];

    $stmt = $conn->prepare("UPDATE libros SET titulo = ?, autor = ?, anio = ?, editorial = ?, texto_libro = ?, imagen = ? WHERE id_libro = ?");
    $stmt->bind_param("ssssssi", $titulo, $autor, $anio, $editorial, $texto_libro, $imagen, $id_libro);
    $stmt->execute();
    $stmt->close();

    // Redireccionar después de modificar
    header("Location: CRUDlibros.php");
    exit();
}

// Obtener todos los libros
$sql = "SELECT * FROM libros";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Libros</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background: linear-gradient(to right, #00b09b, #96c93d);
            font-family: 'Arial', sans-serif;
        }
        .container {
            max-width: 1200px;
        }
        .table th, .table td {
            text-align: center;
            vertical-align: middle;
        }
        .table {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .table thead {
            background-color: #4e73df;
            color: white;
        }
        .btn {
            border-radius: 5px;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }
        .form-control {
            border-radius: 5px;
            margin: 5px 0;
            border-color: #4e73df;
        }
        .card-header {
            background-color: #007bff;
            color: white;
            text-align: center;
            font-size: 1.5rem;
            padding: 10px;
        }
    </style>
</head>
<body>
    <div class="container mt-2">
        <button onclick="window.location.href='admin.html'" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Regresar
        </button>

        <a href="filtrar_libros.php" class="btn btn-secondary">
            <i class="fas fa-home"></i> Reporte 
        </a>
    </div>

    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <h3>Lista de Libros</h3>
            </div>
            <div class="card-body">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Título</th>
                            <th>Autor</th>
                            <th>Año</th>
                            <th>Editorial</th>
                            <th>Texto del Libro</th>
                            <th>Imagen</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <form method="POST" action="CRUDlibros.php">
                                        <td><?php echo $row['id_libro']; ?></td>
                                        <td><input type="text" name="titulo" value="<?php echo $row['titulo']; ?>" class="form-control"></td>
                                        <td><input type="text" name="autor" value="<?php echo $row['autor']; ?>" class="form-control"></td>
                                        <td><input type="text" name="anio" value="<?php echo $row['anio']; ?>" class="form-control"></td>
                                        <td><input type="text" name="editorial" value="<?php echo $row['editorial']; ?>" class="form-control"></td>
                                        <td><input type="text" name="texto_libro" value="<?php echo $row['texto_libro']; ?>" class="form-control"></td>
                                        <td><input type="text" name="imagen" value="<?php echo $row['imagen']; ?>" class="form-control"></td>
                                        <td>
                                            <input type="hidden" name="id_libro" value="<?php echo $row['id_libro']; ?>">
                                            <button type="submit" name="update" class="btn btn-primary">Modificar</button>
                                            <a href="CRUDlibros.php?delete=<?php echo $row['id_libro']; ?>" class="btn btn-danger" onclick="return confirm('¿Estás seguro de que deseas eliminar este libro?')">Eliminar</a>
                                        </td>
                                    </form>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center">No hay libros registrados.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>

<?php
$conn->close();
?>
