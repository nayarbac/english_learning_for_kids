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

// Verificar si se ha solicitado eliminar un nivel
if (isset($_GET['delete'])) {
    $id_nivel = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM NIVELES WHERE id_nivel = ?");
    $stmt->bind_param("i", $id_nivel);
    $stmt->execute();
    $stmt->close();
    header("Location: CRUDniveles.php?status=success");
    exit();
}

// Verificar si se ha solicitado actualizar un nivel
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $id_nivel = $_POST['id_nivel'];
    $nombre_nivel = $_POST['nombre_nivel'];

    $stmt = $conn->prepare("UPDATE NIVELES SET nombre_nivel = ? WHERE id_nivel = ?");
    $stmt->bind_param("si", $nombre_nivel, $id_nivel);
    $stmt->execute();
    $stmt->close();
    header("Location: CRUDniveles.php?status=updated");
    exit();
}

// Verificar si se ha solicitado agregar un nuevo nivel
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add'])) {
    $nombre_nivel = $_POST['nuevo_nivel'];

    // Insertar el nuevo nivel en la base de datos
    $stmt = $conn->prepare("INSERT INTO NIVELES (nombre_nivel) VALUES (?)");
    $stmt->bind_param("s", $nombre_nivel);
    $stmt->execute();
    $stmt->close();
    header("Location: CRUDniveles.php?status=added");
    exit();
}

// Obtener todos los niveles
$sql = "SELECT * FROM NIVELES";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Niveles</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>
<body>
    <style>
        body{
         background: linear-gradient(to right, #00b09b, #96c93d);
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
         <div class="container mt-2">
      <button onclick="history.back()" class="btn regresar-btn">
        <i class="fas fa-arrow-left"></i> Regresar
      </button>
    <div class="container mt-5">
        <h2 class="text-center">Lista de Niveles</h2>

        <!-- Formulario para agregar un nuevo nivel -->
        <div class="mb-4">
            <h4>Registrar nuevo nivel</h4>
            <form method="POST" action="CRUDniveles.php">
                <div class="form-group">
                    <label for="nuevo_nivel">Nombre del Nivel</label>
                    <input type="text" class="form-control" id="nuevo_nivel" name="nuevo_nivel" required>
                </div>
                <button type="submit" name="add" class="btn btn-success">Agregar Nivel</button>
            </form>
        </div>

        <!-- Tabla de niveles -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre del Nivel</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <form method="POST" action="CRUDniveles.php">
                                <td><?php echo $row['id_nivel']; ?></td>
                                <td><input type="text" name="nombre_nivel" value="<?php echo $row['nombre_nivel']; ?>" class="form-control"></td>
                                <td>
                                    <input type="hidden" name="id_nivel" value="<?php echo $row['id_nivel']; ?>">
                                    <button type="submit" name="update" class="btn btn-primary">Actualizar</button>
                                    <a href="CRUDniveles.php?delete=<?php echo $row['id_nivel']; ?>" class="btn btn-danger">Eliminar</a>
                                </td>
                            </form>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="text-center">No hay niveles registrados.</td>
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
        // Mostrar mensaje de éxito al eliminar o actualizar
        $(document).ready(function() {
            const urlParams = new URLSearchParams(window.location.search);
            const status = urlParams.get("status");

            if (status === "success") {
                Swal.fire({
                    icon: 'success',
                    title: '¡Eliminado!',
                    text: 'El nivel ha sido eliminado correctamente.',
                    showConfirmButton: false,
                    timer: 1500
                });
            } else if (status === "updated") {
                Swal.fire({
                    icon: 'success',
                    title: '¡Actualizado!',
                    text: 'El nivel ha sido actualizado correctamente.',
                    showConfirmButton: false,
                    timer: 1500
                });
            } else if (status === "added") {
                Swal.fire({
                    icon: 'success',
                    title: '¡Agregado!',
                    text: 'El nivel ha sido agregado correctamente.',
                    showConfirmButton: false,
                    timer: 1500
                });
            }
        });
    </script>
</body>
</html>

<?php
// Cierra la conexión al final
$conn->close();
?>
