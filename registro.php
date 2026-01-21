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
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $contraseña = $_POST['contraseña'];
    $fecha_nacimiento = $_POST['fecha_nacimiento'];

    
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE correo = ?");
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        
        header("Location: registro.html?status=email_exists");
        exit();
    } else {
        
        $contraseña_hash = password_hash($contraseña, PASSWORD_DEFAULT);

        
        $sql = "INSERT INTO usuarios (nombre, correo, contraseña, fecha_nacimiento) 
                VALUES (?, ?, ?, ?)";
        
       
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $nombre, $correo, $contraseña_hash, $fecha_nacimiento);

       
        if ($stmt->execute() === TRUE) {
            
            header("Location: registro.html?status=success");
        } else {
           
            header("Location: registro.html?status=error");
        }
    }

    // Cierra la declaración y la conexión
    $stmt->close();
    $conn->close();
}
?>
