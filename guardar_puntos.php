<?php
session_start();

// Verifica que el usuario esté autenticado
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'message' => 'Usuario no autenticado']);
    exit();
}

// Recibe los puntos enviados por la solicitud
$data = json_decode(file_get_contents('php://input'), true);
$puntosGanados = isset($data['puntos']) ? intval($data['puntos']) : 0;
$usuario_id = $_SESSION['usuario_id'];

// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "Informatica100*";
$dbname = "ingles";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Llama al procedimiento almacenado para sumar puntos
$sql = "CALL sumarPuntosUsuario(?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $usuario_id, $puntosGanados);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Puntos guardados correctamente']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al guardar los puntos: ' . $stmt->error]);
}

// Cierra la conexión
$stmt->close();
$conn->close();
?>