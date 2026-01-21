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

if (isset($_GET['delete'])) {
    $id_adivinanza = $_GET['delete'];
    
    // Eliminar los intentos relacionados con la adivinanza
    $sql_delete_intentos = "DELETE FROM intentos WHERE id_adivinanza = $id_adivinanza";
    $conn->query($sql_delete_intentos);

    // Luego eliminar la adivinanza
    $sql = "DELETE FROM adivinanzas WHERE id_adivinanza = $id_adivinanza";
    $conn->query($sql);
    header("Location: CRUDadivinanza.php?status=deleted");
    exit();
}


// Actualizar adivinanza
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $id_adivinanza = $_POST['id_adivinanza'];
    $pregunta = $_POST['pregunta'];
    $respuesta_correcta = $_POST['respuesta_correcta'];
    $puntos = $_POST['puntos'];

    $sql = "UPDATE adivinanzas SET 
            pregunta = '$pregunta', 
            respuesta_correcta = '$respuesta_correcta', 
            puntos = $puntos 
            WHERE id_adivinanza = $id_adivinanza";
    $conn->query($sql);
    header("Location: CRUDadivinanza.php?status=updated");
    exit();
}

// Agregar adivinanza
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add'])) {
    $pregunta = $_POST['pregunta'];
    $respuesta_correcta = $_POST['respuesta_correcta'];
    $puntos = $_POST['puntos'];

    $sql = "INSERT INTO adivinanzas (pregunta, respuesta_correcta, puntos) 
            VALUES ('$pregunta', '$respuesta_correcta', $puntos)";
    $conn->query($sql);
    header("Location: CRUDadivinanza.php?status=added");
    exit();
}

// Obtener todas las adivinanzas
$sql = "SELECT * FROM adivinanzas";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD de Adivinanzas</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>
 <style>
    body{
        background: linear-gradient(to right, #f7971e, #ffd200);
     }
               
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
        position: relative; 
    display: inline-block; 
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
          <div class="container mt-2 ">
    <!-- Botón de regresar --> 
    <div>
        <button onclick="window.location.href='admin.html'" class="btn regresar-btn">
            <i class="fas fa-arrow-left"></i> Regresar
        </button>


 
        <a href="filtro_adivinanzas.php" class="btn regresar-btn">
            <i class="fas fa-home"></i> Reporte
        </a>
    </div>
</div>

<body>
    <div class="container mt-5">
        <h2 class="text-center">CRUD de Adivinanzas</h2>
        
        <!-- Formulario para agregar adivinanza -->
        <form method="POST" class="mb-4">
            <input type="hidden" name="add" value="1">
            <div class="form-row">
                <div class="col">
                    <input type="text" name="pregunta" placeholder="Pregunta" class="form-control" required>
                </div>
                <div class="col">
                    <input type="text" name="respuesta_correcta" placeholder="Respuesta Correcta" class="form-control" required>
                </div>
                <div class="col">
                    <input type="number" name="puntos" placeholder="Puntos" class="form-control" required>
                </div>
                <div class="col">
                    <button type="submit" class="btn btn-success">Agregar Adivinanza</button>
                </div>
            </div>
        </form>

        <!-- Tabla de adivinanzas -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Pregunta</th>
                    <th>Respuesta Correcta</th>
                    <th>Puntos</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <form method="POST">
                                <td><?php echo $row['id_adivinanza']; ?></td>
                                <td><input type="text" name="pregunta" value="<?php echo $row['pregunta']; ?>" class="form-control"></td>
                                <td><input type="text" name="respuesta_correcta" value="<?php echo $row['respuesta_correcta']; ?>" class="form-control"></td>
                                <td><input type="number" name="puntos" value="<?php echo $row['puntos']; ?>" class="form-control"></td>
                                <td>
                                    <input type="hidden" name="id_adivinanza" value="<?php echo $row['id_adivinanza']; ?>">
                                    <button type="submit" name="update" class="btn btn-primary">Actualizar</button>
                                    <a href="CRUDadivinanza.php?delete=<?php echo $row['id_adivinanza']; ?>" class="btn btn-danger">Eliminar</a>
                                </td>
                            </form>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">No hay adivinanzas registradas.</td>
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
        $(document).ready(function() {
            const urlParams = new URLSearchParams(window.location.search);
            const status = urlParams.get("status");

            if (status === "added") {
                Swal.fire({
                    icon: 'success',
                    title: '¡Agregado!',
                    text: 'La adivinanza ha sido agregada correctamente.',
                    showConfirmButton: false,
                    timer: 1500
                });
            } else if (status === "updated") {
                Swal.fire({
                    icon: 'success',
                    title: '¡Actualizado!',
                    text: 'La adivinanza ha sido actualizada correctamente.',
                    showConfirmButton: false,
                    timer: 1500
                });
            } else if (status === "deleted") {
                Swal.fire({
                    icon: 'success',
                    title: '¡Eliminado!',
                    text: 'La adivinanza ha sido eliminada correctamente.',
                    showConfirmButton: false,
                    timer: 1500
                });
            }
        });
    </script>
</body>
</html>

<?php
// Cerrar conexión
$conn->close();
?>
