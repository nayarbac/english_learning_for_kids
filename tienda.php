<?php
session_start(); // Inicia la sesión para obtener el ID del usuario logeado

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

// Verifica que el usuario esté logeado
if (!isset($_SESSION['id_usuario'])) {
    die("Por favor, inicia sesión para ver la tienda de avatares.");
}

// Obtiene los datos del usuario logeado
$id_usuario = $_SESSION['id_usuario'];
$sql_usuario = "SELECT nombre, puntos, id_avatar FROM usuarios WHERE id_usuario = ?";
$stmt_usuario = $conn->prepare($sql_usuario);
$stmt_usuario->bind_param("i", $id_usuario);
$stmt_usuario->execute();
$result_usuario = $stmt_usuario->get_result();

// Verifica que haya un resultado de la consulta
if ($result_usuario->num_rows > 0) {
    $usuario = $result_usuario->fetch_assoc();
} else {
    die("Error: No se encontró información del usuario.");
}

// Consulta todos los avatares disponibles
$sql_avatares = "SELECT * FROM avatares";
$result_avatares = $conn->query($sql_avatares);

// Verifica que la consulta de avatares sea válida
if (!$result_avatares) {
    die("Error al obtener los avatares: " . $conn->error);
}

// Verificar si el usuario ha enviado la solicitud de compra
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_avatar = $_POST['id_avatar'];

    // Obtener el avatar seleccionado
    $sql_avatar = "SELECT * FROM avatares WHERE id_avatar = ?";
    $stmt_avatar = $conn->prepare($sql_avatar);
    $stmt_avatar->bind_param("i", $id_avatar);
    $stmt_avatar->execute();
    $result_avatar = $stmt_avatar->get_result();
    
    if ($result_avatar->num_rows > 0) {
        $avatar = $result_avatar->fetch_assoc();
        $costo_avatar = $avatar['costo_puntos'];

        // Verificar si el usuario tiene suficientes puntos
        if ($usuario['puntos'] >= $costo_avatar) {
            // Realizar la compra
            $sql_comprar = "UPDATE usuarios SET puntos = puntos - ? WHERE id_usuario = ?";
            $stmt_comprar = $conn->prepare($sql_comprar);
            $stmt_comprar->bind_param("ii", $costo_avatar, $id_usuario);
            if ($stmt_comprar->execute()) {
                echo "<script>alert('Compra realizada con éxito. ¡Disfruta tu nuevo avatar!');</script>";
            } else {
                echo "<script>alert('Error al realizar la compra. Intenta nuevamente más tarde.');</script>";
            }
        } else {
            echo "<script>alert('No tienes suficientes puntos para comprar este avatar.');</script>";
        }
    } else {
        echo "<script>alert('Avatar no encontrado.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tienda de Avatares</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
           background-image: url("imagenes/large-triangles.png");
            font-family: 'Comic Sans MS', 'Arial', sans-serif;
            color: #333;
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

        h2 {
            font-size: 2.5rem;
            color: #fff;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        .container {
            background-color: #ffffff;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            padding: 20px;
        }

        .avatar-item {
            text-align: center;
            margin-bottom: 30px;
        }

        .avatar-image {
            max-width: 120px;
            border: 4px solid #ffc107;
            border-radius: 50%;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .avatar-image:hover {
            transform: scale(1.1);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .avatar-cost {
            font-weight: bold;
            color: #28a745;
        }

        .btn-primary {
            background-color: #ff5722;
            border: none;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .btn-primary:hover {
            background-color: #e64a19;
            transform: scale(1.05);
        }

        .btn-custom {
            margin-right: 10px;
            font-weight: bold;
            background-color: #17a2b8;
            color: #fff;
            border: none;
        }

        .btn-custom:hover {
            background-color: #138496;
        }

        .text-center p {
            font-size: 1.2rem;
            color: #555;
        }
    </style>
</head>
<body>
   <!-- Barra de Navegación -->
    <nav
      class="navbar navbar-expand-lg navbar-light"
      style="background-color: #fce38a"
    >
      <div class="container-fluid">
        <a
          class="navbar-brand"
          href="#"
          style="font-size: 1.8rem; font-weight: bold; color: #ff6f61"
        >
          Learningles 🍎
        </a>
        <button
          class="navbar-toggler"
          type="button"
          data-bs-toggle="collapse"
          data-target="#navbarSupportedContent"
          aria-controls="navbarSupportedContent"
          aria-expanded="false"
          aria-label="Toggle navigation"
        >
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <li class="nav-item">
              <a
                class="nav-link active"
                aria-current="page"
                href="tienda.php"
                style="color: #ff6f61; font-weight: bold"
                >Tienda</a
              >
            </li>
            <li class="nav-item">
              <a
                class="nav-link"
                href="perfil.php"
                style="color: #ff6f61; font-weight: bold"
                >Mi perfil</a>
            </li>
        </div>
      </div>
    </nav>
    
    <!-- Contenido Principal -->
    <div class="container mt-2">
      <button onclick="history.back()" class="btn regresar-btn">
        <i class="fas fa-arrow-left"></i> Regresar
      </button>
    </div>
    <div class="container mt-5">
        <h2 class="text-center">¡Bienvenido, <?php echo htmlspecialchars($usuario['nombre']); ?>!</h2>
        <p class="text-center">Tienes <span style="color: #ff5722; font-weight: bold;"><?php echo $usuario['puntos']; ?> puntos</span> disponibles para gastar en avatares divertidos.</p>

        <!-- Botones "Mis Avatares" y "Cambiar Avatar" -->
        <div class="text-center mb-4">
            <a href="mis_avatares.php" class="btn btn-custom btn-lg">Mis Avatares</a>
        </div>

        <div class="row">
            <?php while ($avatar = $result_avatares->fetch_assoc()): ?>
                <div class="col-md-3 avatar-item">
                    <img src="<?php echo htmlspecialchars($avatar['imagen_url']); ?>" alt="Avatar" class="avatar-image">
                    <h5><?php echo htmlspecialchars($avatar['nombre']); ?></h5>
                    <p class="avatar-cost"><?php echo $avatar['costo_puntos']; ?> puntos</p>
                    <form method="post" action="">
                        <input type="hidden" name="id_avatar" value="<?php echo $avatar['id_avatar']; ?>">
                        <button type="submit" class="btn btn-primary">Comprar</button>
                    </form>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>

<?php
$conn->close(); // Cierra la conexión a la base de datos
?>
