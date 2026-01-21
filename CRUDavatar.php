<?php
// Configuración de la conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "Informatica100*";
$dbname = "ingles";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Eliminar avatar
if (isset($_GET['delete'])) {
    $id_avatar = $_GET['delete'];
    $sql = "CALL eliminar_avatar($id_avatar)";
    if ($conn->query($sql) === TRUE) {
        
        header("Location: CRUDavatar.php?status=deleted");
        exit(); // Asegura que no se ejecute código adicional después de la redirección
    } else {
        echo "Error al eliminar el avatar: " . $conn->error;
    }
}

// Actualizar avatar
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $id_avatar = $_POST['id_avatar'];
    $nombre = $_POST['nombre'];
    $costo_puntos = $_POST['costo_puntos'];
    $imagen_url = $_POST['imagen_url'];

    $sql = "CALL actualizar_avatar($id_avatar, '$nombre', $costo_puntos, '$imagen_url')";
    if ($conn->query($sql) === TRUE) {
        // Redirige para mostrar la alerta después de la actualización
        header("Location: CRUDavatar.php?status=updated");
    } else {
        echo "Error al actualizar el avatar: " . $conn->error;
    }
    exit();
}

// Agregar avatar con carga de imagen
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add'])) {
    $nombre = $_POST['nombre'];
    $costo_puntos = $_POST['costo_puntos'];
    $imagen = $_FILES['imagen'];

    $target_dir = "imagenes/";
    $target_file = $target_dir . time() . '_' . basename($imagen["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    
    $check = getimagesize($imagen["tmp_name"]);
    if ($check === false) {
        $uploadOk = 0;
    }


    if ($imagen["size"] > 500000) {
        $uploadOk = 0;
    }

    // Solo permitir ciertos formatos de archivo
    if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
        $uploadOk = 0;
    }

    if ($uploadOk === 1 && move_uploaded_file($imagen["tmp_name"], $target_file)) {
        $stmt = $conn->prepare("CALL registrar_avatar(?, ?, ?)");
        $stmt->bind_param("sis", $nombre, $costo_puntos, $target_file);
        $stmt->execute();
        $stmt->close();
        // Redirige para mostrar la alerta después de agregar
        header("Location: CRUDavatar.php?status=added");
    } else {
        echo "Error al cargar la imagen.";
    }
    exit();
}

// Obtener todos los avatares
$sql = "SELECT * FROM avatares";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD de Avatares</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>
<style>
    body{
         background: linear-gradient(to right, #f7971e, #ffd200);
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

      
      .regresar-btn:hover {
        transform: scale(1.1);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
      }
        </style>
<body>
        <div class="container mt-2">
      <button onclick="window.location.href='admin.html'" class="btn regresar-btn">
        <i class="fas fa-arrow-left"></i> Regresar
      </button>
    </div>
    <div class="container mt-5">
        <h2 class="text-center">CRUD de Avatares</h2>
        
        <!-- Formulario para agregar avatar -->
        <form method="POST" enctype="multipart/form-data" class="mb-4">
            <input type="hidden" name="add" value="1">
            <div class="form-row">
                <div class="col">
                    <input type="text" name="nombre" placeholder="Nombre del avatar" class="form-control" required>
                </div>
                <div class="col">
                    <input type="number" name="costo_puntos" placeholder="Costo en puntos" class="form-control" required>
                </div>
                <div class="col">
                    <input type="file" name="imagen" class="form-control-file" accept="image/*" required>
                </div>
                <div class="col">
                    <button type="submit" class="btn btn-success">Agregar Avatar</button>
                </div>
            </div>
        </form>

        <!-- Tabla de avatares -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Costo en puntos</th>
                    <th>URL de Imagen</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <form method="POST">
                                <td><?php echo $row['id_avatar']; ?></td>
                                <td><input type="text" name="nombre" value="<?php echo $row['nombre']; ?>" class="form-control"></td>
                                <td><input type="number" name="costo_puntos" value="<?php echo $row['costo_puntos']; ?>" class="form-control"></td>
                                <td><input type="text" name="imagen_url" value="<?php echo $row['imagen_url']; ?>" class="form-control"></td>
                                <td>
                                    <input type="hidden" name="id_avatar" value="<?php echo $row['id_avatar']; ?>">
                                    <button type="submit" name="update" class="btn btn-primary">Actualizar</button>
                                    <a href="CRUDavatar.php?delete=<?php echo $row['id_avatar']; ?>" class="btn btn-danger">Eliminar</a>
                                </td>
                            </form>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">No hay avatares registrados.</td>
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
        // Mostrar mensajes de éxito para agregar, actualizar o eliminar
        document.addEventListener("DOMContentLoaded", function() {
            const urlParams = new URLSearchParams(window.location.search);
            const status = urlParams.get("status");

            // Verifica el valor del parámetro 'status' y muestra la alerta correspondiente
            if (status === "added") {
                Swal.fire({
                    icon: 'success',
                    title: '¡Agregado!',
                    text: 'El avatar ha sido agregado correctamente.',
                    showConfirmButton: false,
                    timer: 1500
                });
            } else if (status === "updated") {
                Swal.fire({
                    icon: 'success',
                    title: '¡Actualizado!',
                    text: 'El avatar ha sido actualizado correctamente.',
                    showConfirmButton: false,
                    timer: 1500
                });
            } else if (status === "deleted") {
                Swal.fire({
                    icon: 'success',
                    title: '¡Eliminado!',
                    text: 'El avatar ha sido eliminado correctamente.',
                    showConfirmButton: false,
                    timer: 1500
                });
            }
        });
    </script>
</body>
</html>

<?php
$conn->close();
?>
