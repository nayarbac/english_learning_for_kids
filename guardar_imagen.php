<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre_imagen = $_POST['nombre_imagen'];
    $id_categoria = $_POST['id_categoria'];
    
    // Manejo de la imagen
    $target_dir = "uploads/"; // Carpeta donde se guardarán las imágenes
    $target_file = $target_dir . basename($_FILES["imagen"]["name"]);
    
    // Verificar si la carpeta existe, si no, crearla
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    // Mover el archivo subido a la carpeta especificada
    if (move_uploaded_file($_FILES["imagen"]["tmp_name"], $target_file)) {
        // Conectar a la base de datos
        try {
            $pdo = new PDO('mysql:host=localhost;dbname=ingles', 'root', 'Informatica100*');
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Preparar y ejecutar la inserción en la base de datos
            $stmt = $pdo->prepare("INSERT INTO imagenes (url_imagen, nombre_imagen, id_categoria) VALUES (?, ?, ?)");
            $stmt->execute([$target_file, $nombre_imagen, $id_categoria]);

            // Redirigir a la página del formulario con estado de éxito
            header("Location: agregar_imagen.php?status=success");
        } catch (PDOException $e) {
            // Redirigir a la página del formulario con estado de error
            header("Location: agregar_imagen.php?status=error");
        }
    } else {
        // Redirigir a la página del formulario con estado de error
        header("Location: agregar_imagen.php?status=error");
    }
}
?>
