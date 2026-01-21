<?php
session_start();
header('Content-Type: application/json');

// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "Informatica100*";
$dbname = "ingles";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Error de conexión: ' . $conn->connect_error]);
    exit;
}

// Verificar que el usuario está logueado
if (isset($_SESSION['id_usuario'])) {
    $id_usuario = $_SESSION['id_usuario'];
    $puntos_a_agregar = 200;

    // Actualizar los puntos del usuario en la base de datos
    $sql = "UPDATE usuarios SET puntos = puntos + ? WHERE id_usuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $puntos_a_agregar, $id_usuario);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al actualizar puntos.']);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Usuario no autenticado.']);
}

$conn->close();
?>
