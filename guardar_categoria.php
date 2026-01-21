<?php
// Conectar a la base de datos
try {
    $pdo = new PDO('mysql:host=localhost;dbname=ingles', 'root', 'Informatica100*');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Verificar si se ha enviado el formulario
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Obtener el nombre de la categoría del formulario
        $nombre_categoria = $_POST['nombre_categoria'];

        // Preparar la consulta SQL para insertar la nueva categoría
        $stmt = $pdo->prepare("INSERT INTO categorias_imagenes (nombre_categoria) VALUES (:nombre_categoria)");
        $stmt->execute([':nombre_categoria' => $nombre_categoria]);

        // Redirigir con un mensaje de éxito
        header("Location: agregar_categoria.php?status=success");
        exit;
    }
} catch (PDOException $e) {
    // Manejar el error
    header("Location: agregar_categoria.php?status=error");
    exit;
}
?>
