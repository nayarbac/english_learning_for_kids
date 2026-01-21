<?php
session_start();

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

// Consulta para obtener las puntuaciones del usuario
$sql = "SELECT puntuacion, fecha, errores, tiempo, id_categoria FROM puntuaciones_imagenes WHERE id_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();

// Verifica si se encontraron puntuaciones
if ($result->num_rows > 0) {
    $puntuaciones = [];
    while ($row = $result->fetch_assoc()) {
        $puntuaciones[] = $row;
    }
} else {
    $puntuaciones = null;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Puntuaciones</title>
    <link href="https://fonts.googleapis.com/css2?family=Comic+Neue:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: 'Comic Neue', sans-serif;
            background: linear-gradient(45deg, #ff9a9e, #fad0c4);
            color: #444;
        }

        .container {
            margin-top: 30px;
        }

        h2 {
            color: #ff6347;
            text-align: center;
            font-size: 2.5rem;
            text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.2);
        }

        .table {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-top: 30px;
        }

        th {
            background-color: #ffcccb;
            color: #444;
            font-size: 1.2rem;
            text-align: center;
        }

        td {
            text-align: center;
            font-size: 1.1rem;
            padding: 15px;
        }

        tr:hover {
            background-color: #ffe4e1;
            transition: background-color 0.3s ease;
        }

        .no-puntuaciones {
            text-align: center;
            font-size: 1.5rem;
            color: #fff;
            background-color: #ff6b6b;
            padding: 10px 20px;
            border-radius: 10px;
            display: inline-block;
        }

        .icono {
            color: #ffcc00;
            font-size: 2rem;
            margin-right: 10px;
        }

        .boton {
            margin-top: 20px;
            text-align: center;
        }

        .btn-regreso {
            background-color: #ffcccb;
            border: none;
            padding: 10px 20px;
            font-size: 1.2rem;
            border-radius: 5px;
            color: #444;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
            transition: transform 0.2s;
        }

        .btn-regreso:hover {
            transform: scale(1.1);
            background-color: #ff6b6b;
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2><i class="fas fa-star icono"></i> Mis Puntuaciones</h2>

        <?php if ($puntuaciones): ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Categoría</th>
                        <th>Puntuación</th>
                        <th>Errores</th>
                        <th>Tiempo</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($puntuaciones as $puntuacion): ?>
                        <tr>
                            <td><?= htmlspecialchars($puntuacion['id_categoria']) ?></td>
                            <td><?= htmlspecialchars($puntuacion['puntuacion']) ?> <i class="fas fa-trophy" style="color: gold;"></i></td>
                            <td><?= htmlspecialchars($puntuacion['errores']) ?> <i class="fas fa-times-circle" style="color: red;"></i></td>
                            <td><?= htmlspecialchars($puntuacion['tiempo']) ?> segundos</td>
                            <td><?= htmlspecialchars($puntuacion['fecha']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="text-center">
                <p class="no-puntuaciones">¡Aún no tienes puntuaciones registradas! <i class="fas fa-frown"></i></p>
            </div>
        <?php endif; ?>

        <div class="boton">
            <button class="btn-regreso" onclick="window.history.back()">Regresar</button>

                <i class="fas fa-arrow-left"></i> Regresar al inicio
            </button>
        </div>
    </div>
</body>
</html>
