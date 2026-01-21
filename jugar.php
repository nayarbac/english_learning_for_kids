<script src="https://cdn.tailwindcss.com"></script>
<?php
session_start();

// Conexión a la base de datos
$servername = "localhost";
$username = "root"; // Cambia esto según tu configuración
$password = "Informatica100*"; // Cambia esto si tienes contraseña
$dbname = "ingles"; // Cambia esto por el nombre de tu base de datos



$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$imagenesCompletadas = 0;
$errores = 0;
$id_categoria = isset($_GET['id_categoria']) ? intval($_GET['id_categoria']) : 0;

// Obtener imágenes de la categoría
$sql = "SELECT * FROM imagenes WHERE id_categoria = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_categoria);
$stmt->execute();
$result = $stmt->get_result();
$imagenes = [];
while ($row = $result->fetch_assoc()) {
    $imagenes[] = $row;
}

// Validar si hay imágenes disponibles en esta categoría
if (empty($imagenes)) {
    echo "<div class='alert alert-warning text-center'>No hay imágenes disponibles en esta categoría.</div>";
    exit;
}

// Procesar respuestas del formulario
$puntuacion = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['respuestas']) && is_array($_POST['respuestas'])) {
    $respuestas = $_POST['respuestas'];

    foreach ($imagenes as $index => $imagen) {
        if (isset($respuestas[$index]) && strtolower(trim($respuestas[$index])) === strtolower($imagen['nombre_imagen'])) {
            $imagenesCompletadas++;
        } else {
            $errores++;
        }
    }

    $puntuacion = "$imagenesCompletadas aciertos, $errores errores"; // Guardamos la puntuación en una variable

    if (isset($_SESSION['id_usuario'])) {
        $id_usuario = $_SESSION['id_usuario'];
        $tiempo = 60; // Cambia esto según el tiempo real tomado

        // Registrar la puntuación
        $sql_insert = "INSERT INTO puntuaciones_imagenes (id_usuario, id_categoria, puntuacion, errores, tiempo) VALUES (?, ?, ?, ?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("iiiii", $id_usuario, $id_categoria, $imagenesCompletadas, $errores, $tiempo);
        if ($stmt_insert->execute()) {
            // Mostrar la alerta con la puntuación en la misma página
            echo "
            <div class='flex items-center justify-center min-h-screen bg-gray-900 bg-opacity-70 fixed inset-0 z-50'>
                <div class='bg-white p-6 rounded-lg shadow-lg text-center space-y-4 max-w-md w-full'>
                    <h2 class='text-3xl font-bold text-purple-600'>¡Felicidades!</h2>
                    <p class='text-lg text-gray-700'>Has completado la partida. Tu puntuación es:</p>
                    <span class='text-4xl font-extrabold text-yellow-500'>$imagenesCompletadas</span>
                    <p class='text-gray-600'>¡Genial, sigue practicando!</p>
                    <a href='ver_puntuacion.php' 
                       class='bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded shadow-md transition-all'>
                       Ver tus puntuaciones
                    </a>
                </div>
            </div>";
        } else {
            echo "<div class='text-red-500 font-semibold'>Error al registrar la puntuación: " . $stmt_insert->error . "</div>";
        }

        exit; // Terminar el script aquí
    }
}

// Cierra la conexión
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Adivina el objeto en inglés</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #ff7eb9, #ff66b2); /* Fondo rosa degradado */
            font-family: 'Comic Sans MS', cursive, sans-serif;
            padding: 50px 0;
            text-align: center;
        }

        h1 {
            color: #fff;
            font-size: 50px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .formulario-juego {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
            gap: 20px;
            margin: 20px auto;
            max-width: 900px;
        }

        .formulario-juego div {
            background: linear-gradient(145deg, #f3f3f3, #e8e8e8);
            box-shadow: 5px 5px 15px rgba(0, 0, 0, 0.1), -5px -5px 15px rgba(255, 255, 255, 0.7);
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            width: 180px; /* Haciendo las cards más grandes */
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .formulario-juego div:hover {
            transform: scale(1.1);
            box-shadow: 10px 10px 20px rgba(0, 0, 0, 0.2), -10px -10px 20px rgba(255, 255, 255, 0.9);
        }

        .formulario-juego img {
            width: 100%;
            height: auto;
            border-radius: 8px;
            margin-bottom: 10px;
            border: 2px solid #ddd;
        }

        .formulario-juego input {
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 5px 10px;
            width: 90%;
            text-align: center;
            font-size: 14px;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .formulario-juego input:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }

        .btn-primary {
            margin: 20px auto;
            display: block;
            background: linear-gradient(145deg, #007bff, #0056b3);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 25px;
            font-size: 16px;
            text-transform: uppercase;
            letter-spacing: 1px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            transform: scale(1.1);
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
    <button onclick="history.back()" class="btn regresar-btn">
        <i class="fas fa-arrow-left"></i> Regresar
    </button>

    <h1>Adivina el objeto en inglés</h1>

  <form method="POST" action="" class="formulario-juego">
    <?php foreach ($imagenes as $index => $imagen): ?>
        <div>
            <?php if (!empty($imagen['url_imagen'])): ?>
                <img src="<?php echo htmlspecialchars($imagen['url_imagen']); ?>" alt="Imagen <?php echo $index; ?>">
            <?php else: ?>
                <p>No hay imagen disponible...</p>
            <?php endif; ?>
            <input type="text" name="respuestas[<?php echo $index; ?>]" placeholder="Escribe el nombre en inglés">
        </div>
    <?php endforeach; ?>
    <button type="submit" class="btn btn-primary">Enviar respuestas</button>

    <?php if ($puntuacion !== null): ?>
        <div class="mt-4">
            <h3 class="text-lg font-bold text-gray-700">Tu puntuación:</h3>
            <p class="text-xl text-yellow-500"><?php echo $puntuacion; ?></p>
        </div>
    <?php endif; ?>
</form>

</body>
</html>
