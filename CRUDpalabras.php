<?php
// Conexión a la base de datos
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

// Eliminar una palabra si se solicita
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM palabras WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: CRUDpalabras.php?status=success");
    exit();
}

// Actualizar una palabra
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $id = (int) $_POST['id'];
    $palabra_espanol = $_POST['palabra_espanol'];
    $palabra_ingles = $_POST['palabra_ingles'];
    $id_nivel = (int) $_POST['id_nivel'];

    $stmt = $conn->prepare("UPDATE palabras SET palabra_espanol = ?, palabra_ingles = ?, id_nivel = ? WHERE id = ?");
    $stmt->bind_param("ssii", $palabra_espanol, $palabra_ingles, $id_nivel, $id);
    $stmt->execute();
    header("Location: CRUDpalabras.php?status=updated");
    exit();
}

// Agregar una nueva palabra
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add'])) {
    $palabra_espanol = $_POST['palabra_espanol'];
    $palabra_ingles = $_POST['palabra_ingles'];
    $id_nivel = !empty($_POST['id_nivel']) ? (int) $_POST['id_nivel'] : null;

    $stmt = $conn->prepare("INSERT INTO palabras (palabra_espanol, palabra_ingles, id_nivel) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $palabra_espanol, $palabra_ingles, $id_nivel);
    if ($stmt->execute()) {
        header("Location: CRUDpalabras.php?status=success");
    } else {
        header("Location: CRUDpalabras.php?status=error");
    }
    exit();
}

// Consultar todas las palabras
$sql = "SELECT * FROM palabras";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Palabras</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        body {
             background: linear-gradient(to right, #00b09b, #96c93d);
            font-family: 'Arial', sans-serif;
        }
        .content-wrapper {
            margin-top: 30px;
            padding: 30px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .table thead {
            background-color: #007bff;
            color: #ffffff;
        }
        .table td, .table th {
            vertical-align: middle;
        }
        .btn-primary, .btn-danger {
            padding: 10px 15px;
            border-radius: 5px;
            font-weight: bold;
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .btn-danger {
            background-color: #dc3545;
            border: none;
        }
        .btn-danger:hover {
            background-color: #c82333;
        }
        .form-control {
            border-radius: 5px;
        }
        .form-group label {
            font-weight: bold;
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
       <div class="container mt-2">
        <button onclick="window.location.href='admin.html'"  class="btn regresar-btn">
        <i class="fas fa-arrow-left"></i> Regresar
      </button>
        <!-- Botón adicional -->
    <a href="filtrar_niveles.php" class="btn regresar-btn">
        <i class="fas fa-home"></i> Reporte 
    </a>
<div class="container content-wrapper">
    <h2 class="text-center text-primary">Gestión de Palabras</h2>

    <!-- Formulario para agregar una palabra -->
    <h4 class="mt-4 text-info">Agregar Palabra</h4>
    <form method="POST" action="CRUDpalabras.php">
        <div class="form-group">
            <label for="palabra_espanol">Palabra en Español</label>
            <input type="text" class="form-control" id="palabra_espanol" name="palabra_espanol" required>
        </div>
        <div class="form-group">
            <label for="palabra_ingles">Palabra en Inglés</label>
            <input type="text" class="form-control" id="palabra_ingles" name="palabra_ingles" required>
        </div>
        <div class="form-group">
            <label for="id_nivel">Nivel (opcional)</label>
            <input type="number" class="form-control" id="id_nivel" name="id_nivel" min="0">
        </div>
        <button type="submit" name="add" class="btn btn-success">Agregar Palabra</button>
    </form>

    <!-- Tabla para listar palabras -->
    <h4 class="mt-5 text-info">Lista de Palabras</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Palabra en Español</th>
                <th>Palabra en Inglés</th>
                <th>Nivel</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <form method="POST" action="CRUDpalabras.php">
                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                        <td><input type="text" name="palabra_espanol" value="<?php echo htmlspecialchars($row['palabra_espanol']); ?>" class="form-control"></td>
                        <td><input type="text" name="palabra_ingles" value="<?php echo htmlspecialchars($row['palabra_ingles']); ?>" class="form-control"></td>
                        <td><input type="number" name="id_nivel" value="<?php echo htmlspecialchars($row['id_nivel']); ?>" class="form-control"></td>
                        <td>
                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['id']); ?>">
                            <button type="submit" name="update" class="btn btn-primary">Actualizar</button>
                            <a href="CRUDpalabras.php?delete=<?php echo $row['id']; ?>" class="btn btn-danger">Eliminar</a>
                        </td>
                    </form>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="5" class="text-center">No hay palabras registradas.</td>
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
    // Mostrar mensajes con SweetAlert2
    $(document).ready(function() {
        const urlParams = new URLSearchParams(window.location.search);
        const status = urlParams.get("status");

        if (status === "success") {
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: 'Operación realizada correctamente.',
                showConfirmButton: false,
                timer: 1500
            });
        } else if (status === "error") {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Hubo un problema con la operación.',
                showConfirmButton: true
            });
        }
    });
</script>
</body>
</html>

<?php
$conn->close();
?>
