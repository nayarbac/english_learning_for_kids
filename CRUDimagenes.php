<?php
// Configuración de la base de datos
$servername = "localhost";
$username = "root";
$password = "Informatica100*";
$dbname = "ingles";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Agregar imagen
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_image'])) {
    $nombre_imagen = $_POST['nombre_imagen'];
    $id_categoria = $_POST['id_categoria'];
    $imagen = $_FILES['imagen'];

    if ($imagen['error'] === 0) {
        $ruta_imagen = "uploads/" . basename($imagen['name']);
        if (move_uploaded_file($imagen['tmp_name'], $ruta_imagen)) {
            $stmt = $conn->prepare("INSERT INTO imagenes (url_imagen, nombre_imagen, id_categoria) VALUES (?, ?, ?)");
            $stmt->bind_param("ssi", $ruta_imagen, $nombre_imagen, $id_categoria);
            $stmt->execute();
            $stmt->close();
            header("Location: CRUDimagenes.php?status=success");
        } else {
            header("Location: CRUDimagenes.php?status=error");
        }
    }
}

// Eliminar imagen
if (isset($_GET['delete'])) {
    $id_imagen = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM imagenes WHERE id_imagen = ?");
    $stmt->bind_param("i", $id_imagen);
    $stmt->execute();
    $stmt->close();
    header("Location: CRUDimagenes.php?status=success");
    exit();
}

// Actualizar imagen
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $id_imagen = $_POST['id_imagen'];
    $url_imagen = $_POST['url_imagen'];
    $nombre_imagen = $_POST['nombre_imagen'];
    $id_categoria = $_POST['id_categoria'];

    $stmt = $conn->prepare("UPDATE imagenes SET url_imagen = ?, nombre_imagen = ?, id_categoria = ? WHERE id_imagen = ?");
    $stmt->bind_param("ssii", $url_imagen, $nombre_imagen, $id_categoria, $id_imagen);
    $stmt->execute();
    $stmt->close();
    header("Location: CRUDimagenes.php?status=updated");
    exit();
}

// Obtener datos para mostrar
$result_imagenes = $conn->query("SELECT * FROM imagenes");
$result_categorias = $conn->query("SELECT * FROM categorias_imagenes");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Imágenes</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        body {
             background: linear-gradient(to right, #f7971e, #ffd200);
        }
        .content-wrapper {
            margin: 30px auto;
            padding: 30px;
            background-color: rgba(255, 255, 255, 0.8);
            border-radius: 15px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.5);
            max-width: 900px;
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
      .ir-inicio-btn {
    background-color: #28a745; /* Verde */
    margin-top: 2px;

.ir-inicio-btn:hover {
    background-color: #218838; /* Verde más oscuro */
}
    </style>
</head>
<body>
       <div class="container mt-2 ">
    <!-- Botón de regresar --> 
    <div>
        <button onclick="window.location.href='admin.html'" class="btn regresar-btn">
            <i class="fas fa-arrow-left"></i> Regresar
        </button>


 
        <a href="filtrar_imagenes.php" class="btn regresar-btn">
            <i class="fas fa-home"></i> Reporte
        </a>
    </div>
</div>

    <div class="container content-wrapper">
        <h2 class="text-center">Gestión de Imágenes</h2>

        <!-- Formulario para agregar imágenes -->
        <form action="CRUDimagenes.php" method="POST" enctype="multipart/form-data" class="mb-4">
            <input type="hidden" name="add_image" value="1">
            <div class="form-group">
                <label for="nombre_imagen">Nombre de la Imagen</label>
                <input type="text" name="nombre_imagen" id="nombre_imagen" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="imagen">Selecciona una Imagen</label>
                <input type="file" name="imagen" id="imagen" class="form-control-file" accept="image/*" required>
            </div>
            <div class="form-group">
                <label for="id_categoria">Categoría</label>
                <select name="id_categoria" id="id_categoria" class="form-control" required>
                    <option value="">Selecciona una categoría</option>
                    <?php while ($categoria = $result_categorias->fetch_assoc()): ?>
                        <option value="<?php echo $categoria['id_categoria']; ?>"><?php echo $categoria['nombre_categoria']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-success btn-block">Agregar Imagen</button>
        </form>

        <!-- Tabla para listar y editar imágenes -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>URL</th>
                    <th>Nombre</th>
                    <th>Categoría</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result_imagenes->num_rows > 0): ?>
                    <?php while ($row = $result_imagenes->fetch_assoc()): ?>
                        <tr>
                            <form action="CRUDimagenes.php" method="POST">
                                <td><?php echo $row['id_imagen']; ?></td>
                                <td><input type="text" name="url_imagen" value="<?php echo $row['url_imagen']; ?>" class="form-control"></td>
                                <td><input type="text" name="nombre_imagen" value="<?php echo $row['nombre_imagen']; ?>" class="form-control"></td>
                                <td>
                                    <select name="id_categoria" class="form-control">
                                        <?php
                                        $result_categorias = $conn->query("SELECT * FROM categorias_imagenes");
                                        while ($categoria = $result_categorias->fetch_assoc()): ?>
                                            <option value="<?php echo $categoria['id_categoria']; ?>" <?php echo ($categoria['id_categoria'] == $row['id_categoria']) ? 'selected' : ''; ?>>
                                                <?php echo $categoria['nombre_categoria']; ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </td>
                                <td>
                                    <input type="hidden" name="id_imagen" value="<?php echo $row['id_imagen']; ?>">
                                    <button type="submit" name="update" class="btn btn-primary btn-sm">Actualizar</button>
                                    <a href="CRUDimagenes.php?delete=<?php echo $row['id_imagen']; ?>" class="btn btn-danger btn-sm">Eliminar</a>
                                </td>
                            </form>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">No hay imágenes registradas.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const urlParams = new URLSearchParams(window.location.search);
        const status = urlParams.get("status");
        if (status === "success") {
            Swal.fire("¡Éxito!", "Operación realizada correctamente.", "success");
        } else if (status === "error") {
            Swal.fire("Error", "Hubo un problema. Inténtalo de nuevo.", "error");
        }
    </script>
</body>
</html>
<?php $conn->close(); ?>
