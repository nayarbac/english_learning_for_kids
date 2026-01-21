<?php
session_start(); // Inicia la sesión

// Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php"); // Redirige al login si no hay sesión activa
    exit();
}

// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "Informatica100*";
$dbname = "ingles";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica la conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Obtén el ID del usuario desde la sesión
$id_usuario = $_SESSION['id_usuario'];

// Consulta para obtener todos los datos del usuario
$sql = "SELECT * FROM usuarios WHERE id_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();

// Verifica si se encontró el usuario
if ($result->num_rows > 0) {
    $usuario = $result->fetch_assoc();

    // Obtén el id_avatar del usuario
    $id_avatar = $usuario['id_avatar'];

    // Consulta para obtener la URL de la imagen del avatar
    $sql_avatar = "SELECT imagen_url FROM avatares WHERE id_avatar = ?";
    $stmt_avatar = $conn->prepare($sql_avatar);
    $stmt_avatar->bind_param("i", $id_avatar);
    $stmt_avatar->execute();
    $result_avatar = $stmt_avatar->get_result();

    if ($result_avatar->num_rows > 0) {
        $avatar = $result_avatar->fetch_assoc();
        $avatar_image = $avatar['imagen_url'];
    } else {
        $avatar_image = "imagenes/default.png"; // Imagen por defecto si no se encuentra el avatar
    }

} else {
    echo "No se encontraron datos para este usuario.";
    exit();
}

$stmt->close();
$stmt_avatar->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Usuario</title>
    <link href="https://fonts.googleapis.com/css2?family=Comic+Neue:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            
            background-image: url("imagenes/polka-dots.png");
            font-family: 'Comic Neue', sans-serif;
            animation: backgroundAnimation 5s infinite alternate;
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

        .container {
            margin-top: 50px;
        }

        .profile-card {
            background: #ffffff;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            animation: fadeIn 1s ease-in-out;
            text-align: center;
        }

        .avatar {
            width: 160px;
            height: 160px;
            border-radius: 50%;
            object-fit: cover;
            margin: 0 auto;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease-in-out;
        }

        .avatar:hover {
            transform: scale(1.1);
        }

        .btn-primary, .btn-warning {
            font-size: 18px;
            font-weight: bold;
            padding: 12px 20px;
            margin: 10px;
            border-radius: 25px;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background-color: #ff6347;
            color: white;
        }

        .btn-primary:hover {
            background-color: #ff4500;
        }

        .btn-warning {
            background-color: #ffde00;
            color: white;
        }

        .btn-warning:hover {
            background-color: #f2c200;
        }

        .table th, .table td {
            text-align: center;
            font-size: 18px;
        }

        h2 {
            color: #ff6347;
            font-size: 2.5rem;
            text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1);
        }

        /* Animaciones */
        @keyframes fadeIn {
            0% { opacity: 0; transform: translateY(30px); }
            100% { opacity: 1; transform: translateY(0); }
        }

        @keyframes backgroundAnimation {
            0% { background-color: #f0f8ff; }
            50% { background-color: #ffecb3; }
            100% { background-color: #f0f8ff; }
        }
    </style>
</head>
<body>
      <!-- Contenido Principal -->
    <div class="container mt-2">
       <button onclick="window.location.href='gratuita.html'" class="btn regresar-btn">
        <i class="fas fa-arrow-left"></i> Regresar
      </button>
    </div>

    <div class="container">
        <div class="profile-card">
            <h2>¡Bienvenido a tu Perfil!</h2>
            <!-- Imagen del avatar -->
            <img src="<?= htmlspecialchars($avatar_image) ?>" alt="Avatar del usuario" class="avatar">
            
            <!-- Información del usuario -->
            <table class="table table-bordered mt-3">
                <tr>
                    <th>ID</th>
                    <td><?= htmlspecialchars($usuario['id_usuario']) ?></td>
                </tr>
                <tr>
                    <th>Nombre</th>
                    <td><?= htmlspecialchars($usuario['nombre']) ?></td>
                </tr>
                <tr>
                    <th>Correo</th>
                    <td><?= htmlspecialchars($usuario['correo']) ?></td>
                </tr>
                <tr>
                    <th>Tipo de Cuenta</th>
                    <td><?= htmlspecialchars($usuario['tipo_cuenta']) ?></td>
                </tr>
                <tr>
                    <th>Rol</th>
                    <td><?= htmlspecialchars($usuario['rol']) ?></td>
                </tr>
            
            </table>
            <!-- Botones -->
            <div class="text-center">
                <a href="login.php" class="btn btn-primary">Cerrar Sesión</a>
                <a href="actualizar.php" class="btn btn-warning">Obtener Premium</a>
                <a href="mis_avatares.php" class="btn btn-info">Mis avatares</a>
                <a href="puntuacionmia.php" class="btn btn-success">Mis puntuaciones en juego de adivina imagenes</a>
                <a href="puntuacionmia2.php" class="btn btn-dark">Mis puntuaciones en juego de pares</a>
            </div>
        </div>
    </div>
</body>
</html>
