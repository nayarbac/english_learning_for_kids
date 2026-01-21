<?php
session_start();

// Verifica si el usuario está logeado
if (!isset($_SESSION['id_usuario'])) {
    die("Por favor, inicia sesión para realizar compras.");
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

// Verifica que el avatar esté en la base de datos
if (!isset($_POST['id_avatar'])) {
    die("Avatar no encontrado.");
}

// Obtiene el ID del avatar y del usuario
$id_avatar = $_POST['id_avatar'];
$id_usuario = $_SESSION['id_usuario'];

// Consulta los detalles del avatar seleccionado
$sql_avatar = "SELECT * FROM avatares WHERE id_avatar = ?";
$stmt_avatar = $conn->prepare($sql_avatar);
$stmt_avatar->bind_param("i", $id_avatar);
$stmt_avatar->execute();
$result_avatar = $stmt_avatar->get_result();

if ($result_avatar->num_rows === 0) {
    die("Avatar no encontrado.");
}

$avatar = $result_avatar->fetch_assoc();
$costo_avatar = $avatar['costo_puntos'];  // Costo en puntos

// Obtiene los puntos del usuario
$sql_usuario = "SELECT puntos FROM usuarios WHERE id_usuario = ?";
$stmt_usuario = $conn->prepare($sql_usuario);
$stmt_usuario->bind_param("i", $id_usuario);
$stmt_usuario->execute();
$result_usuario = $stmt_usuario->get_result();
$usuario = $result_usuario->fetch_assoc();

if ($usuario['puntos'] < $costo_avatar) {
    die("No tienes suficientes puntos para comprar este avatar.");
}

// Resta los puntos del usuario
$nuevos_puntos = $usuario['puntos'] - $costo_avatar;
$sql_actualizar_puntos = "UPDATE usuarios SET puntos = ? WHERE id_usuario = ?";
$stmt_actualizar_puntos = $conn->prepare($sql_actualizar_puntos);
$stmt_actualizar_puntos->bind_param("ii", $nuevos_puntos, $id_usuario);
$stmt_actualizar_puntos->execute();

// Registra la compra del avatar en la tabla avatares_comprados
$sql_comprar = "INSERT INTO avatares_comprados (id_usuario, id_avatar) VALUES (?, ?)";
$stmt_comprar = $conn->prepare($sql_comprar);
$stmt_comprar->bind_param("ii", $id_usuario, $id_avatar);
$stmt_comprar->execute();

// Verifica si la compra se registró correctamente
if ($stmt_comprar->affected_rows > 0) {
    echo "¡Avatar comprado con éxito!";
} else {
    echo "Hubo un problema al registrar la compra. Intenta de nuevo.";
}

// Cierra la conexión
$stmt_avatar->close();
$stmt_usuario->close();
$stmt_actualizar_puntos->close();
$stmt_comprar->close();
$conn->close();
?>
