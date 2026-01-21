<?php
// Configuración de la conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "Informatica100*";
$dbname = "ingles";

// Crear la conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener los datos JSON enviados
$data = json_decode(file_get_contents("php://input"));

// Comprobar que los datos estén presentes
if (isset($data->id_usuario) && isset($data->id_nivel) && isset($data->puntuacion)) {
    $id_usuario = $data->id_usuario;
    $id_nivel = $data->id_nivel;
    $puntuacion = $data->puntuacion;
    $errores = isset($data->errores) ? $data->errores : 0; // Usar valor por defecto de 0 si no se pasa

    // Insertar la puntuación en la base de datos
    $sql = "INSERT INTO puntuaciones (id_usuario, id_nivel, puntuacion, errores) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiii", $id_usuario, $id_nivel, $puntuacion, $errores);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
}

$conn->close();
?>
