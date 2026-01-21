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

if (isset($_GET['delete'])) {
    $id_categoria = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM categorias_imagenes WHERE id_categoria = ?");
    $stmt->bind_param("i", $id_categoria);
    $stmt->execute();
    $stmt->close();
    header("Location: CRUDcategorias_imagenes.php?status=success");
    exit();
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $id_categoria = $_POST['id_categoria'];
    $nombre_categoria = $_POST['nombre_categoria'];

    $stmt = $conn->prepare("UPDATE categorias_imagenes SET nombre_categoria = ? WHERE id_categoria = ?");
    $stmt->bind_param("si", $nombre_categoria, $id_categoria);
    $stmt->execute();
    $stmt->close();
    header("Location: CRUDcategorias_imagenes.php?status=updated");
    exit();
}

// Verificar si se ha enviado el formulario para agregar una categoría
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['update'])) {
    $nombre_categoria = $_POST['nombre_categoria'];

    $stmt = $conn->prepare("INSERT INTO categorias_imagenes (nombre_categoria) VALUES (?)");
    $stmt->bind_param("s", $nombre_categoria);
    if ($stmt->execute()) {
        header("Location: CRUDcategorias_imagenes.php?status=success");
    } else {
        header("Location: CRUDcategorias_imagenes.php?status=error");
    }
    $stmt->close();
    exit();
}

// Obtener todas las categorías
$sql = "SELECT * FROM categorias_imagenes";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Categorías</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: linear-gradient(to right, #f7971e, #ffd200);
            flex-direction: column;
        }
        .content-wrapper, .container {
            background-color: rgba(255, 255, 255, 0.4);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 30px 450px rgb(0, 0, 0, 0.9);
            width: 100%;
            max-width: 400px;
        }
        .table-container {
            max-width: 800px;
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
      }

      
      .regresar-btn:hover {
        transform: scale(1.1);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
      }
    </style>
</head>
<body>
     <div class="container mt-2">
      <button onclick="window.location.href='admin.html'" class="btn regresar-btn">
        <i class="fas fa-arrow-left"></i> Regresar
      </button>
    </div>

    <!-- Formulario para agregar categorías -->
    <div class="content-wrapper">
        <h2 class="text-center">Agregar Categoría</h2>
        <form action="CRUDcategorias_imagenes.php" method="POST">
            <div class="form-group">
                <label for="nombre_categoria">Nombre de la Categoría</label>
                <input type="text" class="form-control" id="nombre_categoria" name="nombre_categoria" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Agregar Categoría</button>
        </form>
    </div>

    <!-- Tabla para listar, actualizar y eliminar categorías -->
    <div class="container mt-5 table-container">
        <h2 class="text-center">Lista de Categorías de Imágenes</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre de la Categoría</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <form method="POST" action="CRUDcategorias_imagenes.php">
                                <td><?php echo $row['id_categoria']; ?></td>
                                <td><input type="text" name="nombre_categoria" value="<?php echo $row['nombre_categoria']; ?>" class="form-control"></td>
                                <td>
                                    <input type="hidden" name="id_categoria" value="<?php echo $row['id_categoria']; ?>">
                                    <button type="submit" name="update" class="btn btn-primary">Actualizar</button>
                                    <a href="CRUDcategorias_imagenes.php?delete=<?php echo $row['id_categoria']; ?>" class="btn btn-danger">Eliminar</a>
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

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

    <script>
        // Mostrar mensajes de estado usando SweetAlert2
        $(document).ready(function () {
            const urlParams = new URLSearchParams(window.location.search);
            const status = urlParams.get("status");

            if (status === "success") {
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: 'Operación completada con éxito.',
                    showConfirmButton: false,
                    timer: 1500
                });
            } else if (status === "error") {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Hubo un problema al realizar la operación.',
                    showConfirmButton: false,
                    timer: 1500
                });
            } else if (status === "updated") {
                Swal.fire({
                    icon: 'success',
                    title: '¡Actualizado!',
                    text: 'La categoría ha sido actualizada correctamente.',
                    showConfirmButton: false,
                    timer: 1500
                });
            }
        });
    </script>
</body>
</html>

<?php
// Cerrar la conexión al final
$conn->close();
?>
