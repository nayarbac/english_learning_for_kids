<?php
session_start();

// Verifica si el usuario está logeado
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
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

// Obtiene los avatares comprados por el usuario
$id_usuario = $_SESSION['id_usuario'];
$sql_comprados = "SELECT av.id_avatar, av.nombre, av.imagen_url 
                  FROM avatares av 
                  JOIN avatares_comprados ac ON av.id_avatar = ac.id_avatar 
                  WHERE ac.id_usuario = ?";
$stmt_comprados = $conn->prepare($sql_comprados);
$stmt_comprados->bind_param("i", $id_usuario);
$stmt_comprados->execute();
$result_comprados = $stmt_comprados->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Avatares</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background: linear-gradient(135deg, #ff9a9e, #fad0c4);
            min-height: 100vh;
        }
        .avatar-card {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .avatar-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
        }
        .avatar-card img {
            border-radius: 15px 15px 0 0;
        }
        .avatar-card h5 {
            font-weight: bold;
            color: #333;
        }
        .btn-regresar {
            position: absolute;
            top: 20px;
            left: 20px;
            z-index: 10;
        }
    </style>
</head>
<body>
    <!-- Botón Regresar -->
    <a href="javascript:history.back()" class="btn btn-primary btn-regresar">Regresar</a>

    <div class="container mt-5">
        <h2 class="text-center text-white mb-4">Tus Avatares Comprados</h2>

      
        <div class="row">
            <?php if ($result_comprados->num_rows > 0): ?>
                <?php while ($avatar = $result_comprados->fetch_assoc()): ?>
                    <div class="col-md-3 mb-4">
                        <div class="avatar-card text-center">
                            <img src="<?php echo htmlspecialchars($avatar['imagen_url']); ?>" alt="Avatar" class="img-fluid">
                            <div class="p-3">
                                <h5><?php echo htmlspecialchars($avatar['nombre']); ?></h5>
                                <!-- Formulario para cambiar el avatar -->
                                <form class="form-cambiar-avatar" action="cambiar_avatar.php" method="POST">
                                    <input type="hidden" name="id_avatar" value="<?php echo $avatar['id_avatar']; ?>">
                                    <button type="submit" class="btn btn-warning btn-sm mt-2">Usar este Avatar</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="text-center text-white">No has comprado ningún avatar aún.</p>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Manejar el envío de todos los formularios con la clase 'form-cambiar-avatar'
            $('.form-cambiar-avatar').submit(function(e) {
                e.preventDefault(); // Prevenir el comportamiento de envío normal del formulario

                var formData = $(this).serialize(); // Serializa los datos del formulario

                $.ajax({
                    type: 'POST',
                    url: $(this).attr('action'),
                    data: formData,
                    dataType: 'json', // Espera una respuesta en formato JSON
                    success: function(response) {
                        if (response.status === 'success') {
                            // Mostrar SweetAlert si el cambio fue exitoso
                            Swal.fire({
                                icon: 'success',
                                title: '¡Avatar cambiado!',
                                text: 'Tu avatar ha sido actualizado con éxito.',
                                confirmButtonText: 'Aceptar'
                            }).then(function() {
                                location.reload(); // Recargar la página para reflejar el cambio
                            });
                        } else {
                            // Mostrar un mensaje de error
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message,
                                confirmButtonText: 'Aceptar'
                            });
                        }
                    },
                    error: function() {
                        // Mostrar mensaje de error si la solicitud AJAX falla
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Hubo un problema al cambiar el avatar. Intenta de nuevo.',
                            confirmButtonText: 'Aceptar'
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>

<?php
$conn->close();
?>
