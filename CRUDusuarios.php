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

// Llamar a la función contar_tipos_cuenta para obtener los conteos
$sql_gratuita = "SELECT contar_tipos_cuenta('gratuita') AS total_gratuita";
$result_gratuita = $conn->query($sql_gratuita);
$total_gratuita = 0;
if ($result_gratuita && $row = $result_gratuita->fetch_assoc()) {
    $total_gratuita = $row['total_gratuita'];
}

$sql_pago = "SELECT contar_tipos_cuenta('pago') AS total_pago";
$result_pago = $conn->query($sql_pago);
$total_pago = 0;
if ($result_pago && $row = $result_pago->fetch_assoc()) {
    $total_pago = $row['total_pago'];
}

// Verificar si se ha solicitado eliminar un usuario
if (isset($_GET['delete'])) {
    $id_usuario = $_GET['delete'];

    // Primero, eliminar los registros relacionados en la tabla 'pagos'
    $stmt = $conn->prepare("DELETE FROM pagos WHERE id_usuario = ?");
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $stmt->close();

    // Ahora eliminar el usuario de la tabla 'usuarios'
    $stmt = $conn->prepare("DELETE FROM usuarios WHERE id_usuario = ?");
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $stmt->close();

    // Mostrar alerta de éxito con SweetAlert
    echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Usuario Eliminado',
                text: 'El usuario ha sido eliminado correctamente.',
                confirmButtonText: 'OK'
            }).then(function() {
                window.location = 'CRUDusuarios.php';
            });
          </script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $id_usuario = $_POST['id_usuario'];
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $contraseña = $_POST['contraseña'];
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $tipo_cuenta = $_POST['tipo_cuenta'];
    $rol = $_POST['rol'];
    $puntos = $_POST['puntos'];
    $id_avatar = $_POST['id_avatar'];

    $stmt = $conn->prepare("UPDATE usuarios SET nombre = ?, correo = ?, contraseña = ?, fecha_nacimiento = ?, tipo_cuenta = ?, rol = ?, puntos = ?, id_avatar = ? WHERE id_usuario = ?");
    $stmt->bind_param("ssssssiii", $nombre, $correo, $contraseña, $fecha_nacimiento, $tipo_cuenta, $rol, $puntos, $id_avatar, $id_usuario);
    $stmt->execute();
    $stmt->close();

    // Redirigir a la página después de la actualización
    header("Location: CRUDusuarios.php");
    exit();
}


// Obtener todos los usuarios
$sql = "SELECT * FROM usuarios";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Usuarios</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        /* Aquí va tu diseño */
        body {
            background: linear-gradient(to right, #00b09b, #96c93d); /* Degradado de azul a verde */
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
            transition: background-color 0.3s, transform 0.3s;
            border-radius: 5px;
        }
        .btn:hover {
            transform: scale(1.05);
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }
        .btn-danger:hover {
            background-color: #c82333;
            border-color: #bd2130;
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
        .form-control, select {
            margin: 5px 0;
        }
        .table tbody tr:hover {
            background-color: #f8f9fa;
        }
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #f1f1f1;
        }
        .table-striped tbody tr:nth-of-type(even) {
            background-color: #ffffff;
        }
        .table-striped tbody tr:hover {
            background-color: #d6e9f1;
        }
        .card-body {
            background-color: #ffffff;
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
    <div class="container mt-2">
    <!-- Botón de regresar -->
    <button onclick="window.location.href='admin.html'"  class="btn regresar-btn">
        <i class="fas fa-arrow-left"></i> Regresar
    </button>

    <!-- Botón adicional -->
    <a href="filtro.php" class="btn regresar-btn">
        <i class="fas fa-home"></i> Reporte 
    </a>
</div>

    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <h3>Lista de Usuarios</h3>
                <p>Usuarios Premium: <?php echo $total_pago; ?> | Usuarios Gratuitos: <?php echo $total_gratuita; ?></p> <!-- Mostrar el conteo -->
            </div>
            <div class="card-body">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Correo</th>
                            <th>Contraseña</th>
                            <th>Fecha Nacimiento</th>
                            <th>Tipo de Cuenta</th>
                            <th>Rol</th>
                            <th>Puntos</th>
                            <th>Avatar</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <form method="POST" action="CRUDusuarios.php">
                                        <td><?php echo $row['id_usuario']; ?></td>
                                        <td><input type="text" name="nombre" value="<?php echo $row['nombre']; ?>" class="form-control"></td>
                                        <td><input type="text" name="correo" value="<?php echo $row['correo']; ?>" class="form-control"></td>
                                        <td><input type="password" name="contraseña" value="<?php echo $row['contraseña']; ?>" class="form-control"></td>
                                        <td><input type="date" name="fecha_nacimiento" value="<?php echo $row['fecha_nacimiento']; ?>" class="form-control"></td>
                                        <td>
                                            <select name="tipo_cuenta" class="form-control">
                                                <option value="gratuita" <?php if($row['tipo_cuenta'] == 'gratuita') echo 'selected'; ?>>Gratuita</option>
                                                <option value="pago" <?php if($row['tipo_cuenta'] == 'pago') echo 'selected'; ?>>Pago</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select name="rol" class="form-control">
                                                <option value="usuario" <?php if($row['rol'] == 'usuario') echo 'selected'; ?>>Usuario</option>
                                                <option value="admin" <?php if($row['rol'] == 'admin') echo 'selected'; ?>>Admin</option>
                                            </select>
                                        </td>
                                        <td><input type="text" name="puntos" value="<?php echo $row['puntos']; ?>" class="form-control"></td>
                                        <td><input type="text" name="id_avatar" value="<?php echo $row['id_avatar']; ?>" class="form-control"></td>
                                        <td>
                                            <input type="hidden" name="id_usuario" value="<?php echo $row['id_usuario']; ?>">
                                            <button type="submit" name="update" class="btn btn-primary">Actualizar</button>
                                            <a href="?delete=<?php echo $row['id_usuario']; ?>" class="btn btn-danger" onclick="return confirm('¿Estás seguro de eliminar este usuario?');">Eliminar</a>
                                        </td>
                                    </form>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="10">No hay usuarios registrados.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>
