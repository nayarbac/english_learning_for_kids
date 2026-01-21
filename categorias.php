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

// Verificar si se ha solicitado eliminar una categoría
if (isset($_GET['delete'])) {
    $id_categoria = (int) $_GET['delete'];  
    if ($id_categoria > 0) {
        $stmt = $conn->prepare("DELETE FROM categorias_libros WHERE id_categoria = ?");
        $stmt->bind_param("i", $id_categoria);
        if ($stmt->execute()) {
            header("Location: categorias.php?status=deleted");
        } else {
            header("Location: categorias.php?status=error");
        }
        $stmt->close();
        exit();
    }
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $id_categoria = (int) $_POST['id_categoria'];  
    $nombre_categoria = $_POST['nombre_categoria'];

    if ($id_categoria > 0 && !empty($nombre_categoria)) {
        $stmt = $conn->prepare("UPDATE categorias_libros SET nombre_categoria = ? WHERE id_categoria = ?");
        $stmt->bind_param("si", $nombre_categoria, $id_categoria);
        if ($stmt->execute()) {
            header("Location: categorias.php?status=updated");
        } else {
            header("Location: categorias.php?status=error");
        }
        $stmt->close();
        exit();
    }
}

// Verificar si se ha enviado un nuevo registro
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    $nombre_categoria = $_POST['nombre_categoria'];

    if (!empty($nombre_categoria)) {
        $stmt = $conn->prepare("INSERT INTO categorias_libros (nombre_categoria) VALUES (?)");
        $stmt->bind_param("s", $nombre_categoria);
        if ($stmt->execute()) {
            header("Location: categorias.php?status=success");
        } else {
            header("Location: categorias.php?status=error");
        }
        $stmt->close();
        exit();
    }
}

// Obtener todas las categorías
$sql = "SELECT * FROM categorias_libros";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Categorías de Libros</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        body {
             background: linear-gradient(to right, #00b09b, #96c93d);
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 20px;
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
        .form-group label {
            font-weight: 600;
        }
        .btn {
            border-radius: 5px;
            padding: 10px 15px;
        }
        .btn-primary {
            background-color: #f39c12;
            border-color: #f39c12;
        }
        .btn-danger {
            background-color: #e74c3c;
            border-color: #e74c3c;
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
     <div class="container mt-2">
      <button onclick="history.back()" class="btn regresar-btn">
        <i class="fas fa-arrow-left"></i> Regresar
      </button>
    <div class="container">
        <h2 class="text-center">Gestión de Categorías</h2>

        <!-- Formulario de Registro -->
        <form action="categorias.php" method="POST" class="mb-4">
            <div class="form-group">
                <label for="nombre_categoria">Nombre de la Categoría</label>
                <input type="text" name="nombre_categoria" id="nombre_categoria" class="form-control" required>
            </div>
            <button type="submit" name="register" class="btn btn-primary btn-block">Registrar Categoría</button>
        </form>

        <!-- Tabla de Categorías -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre de Categoría</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <form method="POST" action="categorias.php">
                                <td><?php echo $row['id_categoria']; ?></td>
                                <td><input type="text" name="nombre_categoria" value="<?php echo $row['nombre_categoria']; ?>" class="form-control"></td>
                                <td>
                                    <input type="hidden" name="id_categoria" value="<?php echo $row['id_categoria']; ?>">
                                    <button type="submit" name="update" class="btn btn-primary">Actualizar</button>
                                    <a href="categorias.php?delete=<?php echo $row['id_categoria']; ?>" class="btn btn-danger" onclick="return confirm('¿Estás seguro de eliminar esta categoría?')">Eliminar</a>
                                </td>
                            </form>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="text-center">No hay categorías registradas.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script>
        window.onload = function () {
            const urlParams = new URLSearchParams(window.location.search);
            const status = urlParams.get("status");

            if (status === "success") {
                Swal.fire('¡Registro exitoso!', 'La categoría se registró correctamente.', 'success');
            } else if (status === "deleted") {
                Swal.fire('¡Eliminado!', 'La categoría fue eliminada correctamente.', 'success');
            } else if (status === "updated") {
                Swal.fire('¡Actualizado!', 'La categoría se actualizó correctamente.', 'success');
            } else if (status === "error") {
                Swal.fire('Error', 'Hubo un problema al procesar la solicitud.', 'error');
            }
        };
    </script>
</body>
</html>

<?php
// Cierra la conexión al final
$conn->close();
?>
