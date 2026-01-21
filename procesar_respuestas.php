<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
    $respuestas = $_POST['respuestas']; // Recibimos las respuestas del formulario

    // Obtener las respuestas correctas de la base de datos
    $sql = "SELECT id_imagen, nombre_en_ingles FROM imagenes";
    $result = $conn->query($sql);

    // Guardamos las respuestas correctas
    $respuestasCorrectas = [];
    while ($row = $result->fetch_assoc()) {
        $respuestasCorrectas[$row['id_imagen']] = strtolower(trim($row['nombre_en_ingles'])); // Aseguramos que la comparación sea insensible a mayúsculas
    }

    // Comprobamos las respuestas del usuario
    foreach ($respuestas as $index => $respuestaUsuario) {
        $respuestaUsuario = strtolower(trim($respuestaUsuario)); // Aseguramos que la comparación sea insensible a mayúsculas
        if (!empty($respuestaUsuario) && isset($respuestasCorrectas[$index])) {
            if ($respuestaUsuario == $respuestasCorrectas[$index]) {
                $imagenesCompletadas++; // Respuesta correcta
            } else {
                $errores++; // Respuesta incorrecta
            }
        }
    }

    // Calculamos la puntuación
    $puntuacion = ($imagenesCompletadas / count($respuestasCorrectas)) * 100;

    // Respondemos con los resultados en formato JSON
    echo json_encode([
        'puntuacion' => round($puntuacion, 2), // Redondeamos la puntuación a 2 decimales
        'imagenesCompletadas' => $imagenesCompletadas,
        'errores' => $errores
    ]);

    // Cerramos la conexión
    $conn->close();
}
?>
