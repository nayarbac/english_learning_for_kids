<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>adivinanz</title>
    <style>
        /* Cambia el fondo de la página */
        body {
            background-image: url('imagenes/endless-constellation.png'); 
            background-size: cover; 
            background-position: center; 
            background-attachment: fixed; 
            font-family: Arial, sans-serif;
            text-align: center;
            margin-top: 50px;
            color: white; 
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

        h1 {
            color: #333;
        }
        
        button {
            padding: 10px 20px;
            margin: 5px;
            font-size: 16px;
            cursor: pointer;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        
        button:hover {
            background-color: #45a049;
        }
        
        button:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }
        
        /* Diseño de la adivinanza */
        .adivinanza-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .adivinanza {
            background-color: rgba(255, 255, 255, 0.8); /* Fondo translúcido */
            padding: 30px;
            border: 3px solid #ffb74d;
            border-radius: 15px;
            font-family: Comic Sans MS, Arial, sans-serif;
            text-align: center;
            width: 80%;
            max-width: 500px;
            position: relative;
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
</head>
<body>
    <!-- Contenido Principal -->
    <div class="container mt-2">
      <button onclick="history.back()" class="btn regresar-btn">
        <i class="fas fa-arrow-left"></i> Regresar
      </button>
    </div>

<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "Informatica100*";
$dbname = "ingles";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

function jugarAdivinanza($respuesta_usuario) {
    global $conn;

    if (!isset($_SESSION['id_usuario'])) {
        return "<p style='color: red; font-weight: bold;'>Por favor, inicia sesión para jugar.</p>";
    }

    $id_usuario = $_SESSION['id_usuario'];
    $fecha_hoy = date('Y-m-d');

    $query_intento = "SELECT * FROM intentos WHERE id_usuario = ? AND DATE(fecha) = ?";
    $stmt = $conn->prepare($query_intento);
    if (!$stmt) {
        die("Error en la consulta de intentos: " . $conn->error);
    }
    $stmt->bind_param("is", $id_usuario, $fecha_hoy);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        return "<p style='color: blue; font-weight: bold;'>Ya has jugado hoy. ¡Vuelve mañana!</p>";
    }

    $query = "SELECT a.* 
              FROM adivinanzas a 
              LEFT JOIN intentos i ON a.id_adivinanza = i.id_adivinanza AND i.id_usuario = ?
              WHERE i.id_adivinanza IS NULL
              ORDER BY RAND() LIMIT 1";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die("Error en la consulta de adivinanzas: " . $conn->error);
    }
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $adivinanza = $result->fetch_assoc();
        $pregunta = htmlspecialchars($adivinanza['pregunta']);
        $respuesta_correcta = strtolower($adivinanza['respuesta_correcta']);
        $id_adivinanza = $adivinanza['id_adivinanza'];
        $puntos = max(0, $adivinanza['puntos']); // Evitar valores negativos

        if ($respuesta_usuario !== null) {
            $respuesta_usuario = strtolower(trim($respuesta_usuario));
            if ($respuesta_usuario === $respuesta_correcta) {
                $stmt = $conn->prepare("UPDATE usuarios SET puntos = puntos + ? WHERE id_usuario = ?");
                $stmt->bind_param("ii", $puntos, $id_usuario);
                $stmt->execute();

                $fecha = date('Y-m-d H:i:s');
                $stmt = $conn->prepare("INSERT INTO intentos (id_usuario, id_adivinanza, respuesta_usuario, fecha) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("iiss", $id_usuario, $id_adivinanza, $respuesta_usuario, $fecha);
                $stmt->execute();

                return "<p style='color: green; font-weight: bold;'>¡Respuesta correcta! ¡Has ganado $puntos puntos!</p>";
            } else {
                $fecha = date('Y-m-d H:i:s');
                $stmt = $conn->prepare("INSERT INTO intentos (id_usuario, id_adivinanza, respuesta_usuario, fecha) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("iiss", $id_usuario, $id_adivinanza, $respuesta_usuario, $fecha);
                $stmt->execute();

                return "<p style='color: red; font-weight: bold;'>Respuesta incorrecta. Intenta nuevamente mañana.</p>";
            }
        }

        // Diseño para la adivinanza
        return "
            <div style='display: flex; justify-content: center; align-items: center; height: 100vh;'>
                <div style='background-color: #fffbdb; padding: 30px; border: 3px solid #ffb74d; border-radius: 15px; font-family: Comic Sans MS, Arial, sans-serif; text-align: center; width: 80%; max-width: 500px; position: relative;'>
                    <!-- Espacio para la mascota -->
                    <div style='position: absolute; top: -60px; left: 50%; transform: translateX(-50%);'>
                        <img src='imagenes/robot.png' alt='Mascota' style='width: 100px; height: 100px; border-radius: 50%; border: 3px solid #ffb74d;'>
                    </div>

                    <h1 style='color: #ff6f61;'>¡Adivinanza del Día!</h1>
                    <p style='font-size: 20px; color: #4e342e; font-weight: bold; margin: 20px 0;'>$pregunta</p>

                    <form method='POST' style='margin-top: 20px;'>
                        <input type='text' name='respuesta_usuario' placeholder='Escribe tu respuesta aquí...' 
                               style='padding: 12px; font-size: 18px; border: 2px solid #ffa726; border-radius: 10px; width: 80%; max-width: 300px; text-align: center;'>
                        <br><br>
                        <button type='submit' 
                                style='background-color: #ff7043; color: white; padding: 12px 25px; font-size: 18px; border: none; border-radius: 10px; cursor: pointer; box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.2);'>
                            Responder
                        </button>
                    </form>
                    
                    <!-- Pie con mensaje motivador -->
                    <p style='margin-top: 30px; font-size: 16px; color: #ef6c00;'>¡Piensa bien antes de responder! 🎉</p>
                </div>
            </div>";
    } else {
        return "<p style='color: orange; font-weight: bold;'>No hay adivinanzas disponibles. ¡Vuelve mañana!</p>";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $respuesta_usuario = htmlspecialchars(trim($_POST['respuesta_usuario']));
    echo jugarAdivinanza($respuesta_usuario);
} else {
    echo jugarAdivinanza(null);
}
?>

