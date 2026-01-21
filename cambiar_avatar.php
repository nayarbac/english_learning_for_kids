<?php
session_start();

// Verifica si el usuario está logeado
if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['status' => 'error', 'message' => 'No estás logueado.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Conexión a la base de datos
    $servername = "localhost";
    $username = "root";
    $password = "Informatica100*";
    $dbname = "ingles";

    $conn = new mysqli($servername, $username, $password, $dbname);

    // Verifica la conexión
    if ($conn->connect_error) {
        echo json_encode(['status' => 'error', 'message' => 'Error de conexión.']);
        exit;
    }

    $id_usuario = $_SESSION['id_usuario'];
    $id_avatar = $_POST['id_avatar'];

    // Actualiza el avatar en la base de datos
    $sql = "UPDATE usuarios SET id_avatar = ? WHERE id_usuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $id_avatar, $id_usuario);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Avatar cambiado con éxito.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Hubo un error al cambiar el avatar.']);
    }

    $stmt->close();
    $conn->close();
}
?>
