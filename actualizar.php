<?php
session_start();

// Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

// Conexión a la base de datos
$host = 'localhost';
$usuario_db = 'root';
$contrasena_db = 'Informatica100*';
$nombre_db = 'ingles';

$conn = new mysqli($host, $usuario_db, $contrasena_db, $nombre_db);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

=
$mensaje = "";

=
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_usuario = $_SESSION['id_usuario'];
    $metodo_pago = $_POST['metodo_pago'];
    $membresias = (int)$_POST['membresias'];

    // Validar número de tarjeta: solo como campo de entrada (sin almacenar)
    $numero_tarjeta = $_POST['numero_tarjeta'];

    // Validar que el número de tarjeta tenga al menos 13 dígitos
    if (strlen($numero_tarjeta) < 13) {
        $mensaje = "El número de tarjeta debe tener al menos 13 dígitos.";
    } else {
        $precio_por_membresia = 400;
        $monto_total = $membresias * $precio_por_membresia;

       
        $conn->begin_transaction();

        try {
            
            $sql_update = "UPDATE usuarios SET tipo_cuenta = 'pago' WHERE id_usuario = ?";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param("i", $id_usuario);
            $stmt_update->execute();

           
            $sql_insert = "INSERT INTO pagos (id_usuario, fecha_pago, metodo_pago, estado, precio, membresia) 
                           VALUES (?, NOW(), ?, 'completado', ?, ?)";
            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->bind_param("isii", $id_usuario, $metodo_pago, $monto_total, $membresias);
            $stmt_insert->execute();

            // Confirmar transacción
            $conn->commit();
            $mensaje = "¡Tu cuenta ha sido actualizada a Premium y el pago se registró correctamente!";
        } catch (Exception $e) {
            
            $conn->rollback();
            $mensaje = "Error al procesar la actualización: " . $e->getMessage();
        }
    }
}


if (isset($_GET['descargar_pdf'])) {
    
    if (!isset($_SESSION['id_usuario'])) {
        exit("No autorizado");
    }

    $id_usuario = $_SESSION['id_usuario'];
    
    // Obtener los detalles del usuario
    $sql = "SELECT nombre, correo, tipo_cuenta FROM usuarios WHERE id_usuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $result = $stmt->get_result();
    $usuario = $result->fetch_assoc();

    if (!$usuario) {
        exit("Usuario no encontrado");
    }

  
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="ficha_pago.pdf"');

  
    ob_start();
    ?>
    <html>
    <head>
        <style>
            body {
                font-family: Arial, sans-serif;
                margin: 0;
                padding: 0;
            }
            .contenido {
                width: 100%;
                padding: 20px;
                box-sizing: border-box;
                text-align: center;
            }
            .titulo {
                font-size: 20px;
                font-weight: bold;
                margin-bottom: 20px;
            }
            .detalle {
                margin: 10px 0;
                font-size: 16px;
            }
            .firma {
                margin-top: 30px;
                font-size: 14px;
                font-style: italic;
            }
        </style>
    </head>
    <body>
        <div class="contenido">
            <div class="titulo">Ficha de Pago - Membresía Premium</div>
            <div class="detalle">Nombre del Usuario: <?php echo htmlspecialchars($usuario['nombre']); ?></div>
            <div class="detalle">Email: <?php echo htmlspecialchars($usuario['correo']); ?></div>
            <div class="detalle">Tipo de Cuenta: <?php echo htmlspecialchars($usuario['tipo_cuenta']); ?></div>
            <div class="detalle">Fecha: <?php echo date("d/m/Y"); ?></div>
            <div class="firma">Firma: ________________________</div>
        </div>
    </body>
    </html>
    <?php
    // Capturar el contenido HTML generado en el buffer
    $html = ob_get_contents();
    ob_end_clean();

    // Mostrar el contenido HTML (el cual se descargará como un PDF)
    echo $html;
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar a Premium</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }
        .container {
            margin-top: 50px;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .form-control {
            border-radius: 5px;
            height: 40px;
        }
        .btn {
            border-radius: 5px;
            padding: 10px 20px;
        }
        .alert {
            border-radius: 5px;
        }
             .regresar-btn {
        position: absolute;
        top: 80px;
        left: 20px;
        font-size: 1.2rem;
        padding: 10px 15px;
        border-radius: 10px;
        color: #fff;
        background-color: #ff6f61;
        font-weight: bold;
        border: none;
        cursor: pointer;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
      }

      /* Efecto hover */
      .regresar-btn:hover {
        transform: scale(1.1);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
      }
    </style>
</head>
<body>
         <!-- Contenido Principal -->
    <div class="container mt-2">
      <button onclick="history.back()" class="btn regresar-btn">
        <i class="fas fa-arrow-left"></i> Regresar
      </button>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header text-white bg-primary">
                Actualizar a Cuenta Premium
            </div>
            <div class="card-body">
              <?php if (!empty($mensaje)): ?>
    <div class="alert alert-info">
        <?php echo $mensaje; ?>
        <?php if (strpos($mensaje, 'Premium') !== false): ?>
            <button onclick="location.href='login.php'" class="btn btn-primary btn-sm ml-3">Recargar Página</button>
        <?php endif; ?>
    </div>
<?php endif; ?>

                <p>Con la cuenta Premium, obtendrás acceso a contenido exclusivo (Una vez obtenido el premium es necesario reiniciar por primera vez)</p>
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="metodo_pago">Método de Pago</label>
                        <select class="form-control" id="metodo_pago" name="metodo_pago" required>
                            <option value="tarjeta">Tarjeta de Crédito</option>
                            <option value="paypal">PayPal</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="numero_tarjeta">Número de Tarjeta</label>
                        <input type="text" class="form-control" id="numero_tarjeta" name="numero_tarjeta" minlength="13" maxlength="16" pattern="\d*" required>
                        <small class="form-text text-muted">Ingrese al menos 13 dígitos.</small>
                    </div>
                    <div class="form-group">
                        <label for="membresias">Número de Membresías (1 = 1 año)</label>
                        <input type="number" class="form-control" id="membresias" name="membresias" min="1" value="1" required>
                    </div>
                    <button type="submit" class="btn btn-success">Actualizar a Premium</button>
<a href="javascript:history.back()" class="btn btn-secondary">Volver</a>
<a href="mispagos.php" class="btn btn-info">Ver Pagos</a>


                </form>
                <?php if (!empty($mensaje) && isset($_SESSION['id_usuario'])): ?>
                    <form method="GET" action="">
                       
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
