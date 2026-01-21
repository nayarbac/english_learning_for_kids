<?php
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

// Verifica si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recoge los datos del formulario
    $palabra_espanol = $_POST['palabra_espanol'];
    $palabra_ingles = $_POST['palabra_ingles'];
    $id_nivel = isset($_POST['id_nivel']) ? $_POST['id_nivel'] : NULL;

    // Prepara la consulta para insertar la palabra
    $stmt = $conn->prepare("INSERT INTO palabras (palabra_espanol, palabra_ingles, id_nivel) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $palabra_espanol, $palabra_ingles, $id_nivel);

    // Ejecuta la consulta y redirige con el resultado
    if ($stmt->execute()) {
        // Redirige con éxito
        header("Location: agregar_palabra.html?status=success");
    } else {
        // Redirige con error
        header("Location: agregar_palabra.html?status=error");
    }

    // Cierra la declaración y la conexión
    $stmt->close();
    $conn->close();
}
?>
