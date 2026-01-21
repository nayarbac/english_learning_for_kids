<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Seleccionar Categoría</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <style>
        body {
            background-image: url("imagenes/bullseye-gradient.png");
            font-family: "Comic Sans MS", Arial, sans-serif;
            margin-bottom: 100px; /* Agregar espacio al fondo para el banner */
        }
        .container {
            text-align: center;
            max-width: 600px;
            margin-top: 50px;
        }
        h2 {
            color: #ff704d;
            font-weight: bold;
            font-size: 2.5rem;
            margin-bottom: 30px;
        }
        .card {
            background-color: #fff5e1;
            border: 1px solid #f8b195;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
            margin-bottom: 30px;
        }
        .btn {
            font-size: 1.5rem;
            font-weight: bold;
            padding: 15px 30px;
            border-radius: 50px;
            margin: 10px;
            color: #fff;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
        }
        .btn-primary {
            background-color: #ff7f50;
        }
        .btn:hover {
            transform: scale(1.1);
            box-shadow: 0px 6px 12px rgba(0, 0, 0, 0.3);
        }
        .btn:active {
            transform: scale(0.95);
        }
        #mensaje {
            font-size: 1.3rem;
            color: #ff704d;
            font-weight: bold;
        }
        .regresar-btn {
            position: absolute;
            top: 80px;
            left: 20px;
            font-size: 1.2rem;
            padding: 10px 15px;
            border-radius: 10px;
            color: #fff;
            background-color: #FF6F61;
            font-weight: bold;
            border: none;
            cursor: pointer;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .regresar-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
        }
        /* Estilo del banner premium */
        .premium-banner {
            background-color: #f8b195;
            color: #ff6f61;
            padding: 20px;
            font-size: 1.2rem;
            font-weight: bold;
            text-align: center;
            margin-top: 50px; /* Agregar margen superior */
            box-shadow: 0px -4px 8px rgba(0, 0, 0, 0.2);
        }
        .premium-btn {
            background-color: #ff7f50;
            color: white;
            font-size: 1.2rem;
            padding: 10px 25px;
            border-radius: 50px;
            border: none;
            cursor: pointer;
            font-weight: bold;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .premium-btn:hover {
            transform: scale(1.1);
            box-shadow: 0px 6px 12px rgba(0, 0, 0, 0.3);
        }

        .d-flex {
    display: flex;
    justify-content: flex-start; /* Alinea los elementos hacia la izquierda */
    align-items: center;
    flex-wrap: nowrap; /* Evita que los elementos se vayan a una nueva línea */
}

.category-buttons {
    margin-right: 20px; /* Espacio entre los divs */
    width: 50%; /* Ajusta el tamaño de este div para que no ocupe todo el espacio */
}

.character-container {
    margin-left: 20px; /* Espacio a la derecha del div de categorías */
    width: 50%; /* Ajusta el tamaño de este div para que no ocupe todo el espacio */
}

.character-container img {
    width: 100%; /* Hace que la imagen ocupe todo el contenedor */
    height: auto; /* Mantiene la relación de aspecto */
}

    </style>
</head>
<body>
        <!-- Barra de Navegación -->
    <nav
      class="navbar navbar-expand-lg navbar-light"
      style="background-color: #fce38a"
    >
      <div class="container-fluid">
        <a
          class="navbar-brand"
          href="#"
          style="font-size: 1.8rem; font-weight: bold; color: #ff6f61"
        >
          Learningles 🍎
        </a>
        <button
          class="navbar-toggler"
          type="button"
          data-bs-toggle="collapse"
          data-target="#navbarSupportedContent"
          aria-controls="navbarSupportedContent"
          aria-expanded="false"
          aria-label="Toggle navigation"
        >
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <li class="nav-item">
              <a
                class="nav-link active"
                aria-current="page"
                href="tienda.php"
                style="color: #ff6f61; font-weight: bold"
                >Tienda</a
              >
            </li>
            <li class="nav-item">
              <a
                class="nav-link"
                href="perfil.php"
                style="color: #ff6f61; font-weight: bold"
                >Mi perfil</a>
          
        </div>
      </div>
    </nav>


    <div class="container mt-2">
        <button onclick="history.back()" class="btn regresar-btn">
            <i class="fas fa-arrow-left"></i> Regresar
        </button>
    </div>

    <div class="container">
        <!-- Card de información sobre la página -->
        <div class="card">
            <h4 style="color: #ff704d; font-weight: bold;">Bienvenido a Learningles</h4>
            <p>Este juego consiste en que aparecerán imágenes según la categoría que hayas hecho clic y tú tienes que poner la palabra correspondiente en inglés.</p>
        </div>

        <div class="d-flex mt-4">
            <!-- Div de botones de categorías -->
            <div class="category-buttons">
                <h2>¡Selecciona una Categoría para Jugar!</h2>
                <div class="text-center mt-4">
                    <?php
                    // Conectar a la base de datos
                    $conn = new mysqli('localhost', 'root', 'Informatica100*', 'ingles');

                    // Verificar conexión
                    if ($conn->connect_error) {
                        die("Conexión fallida: " . $conn->connect_error);
                    }

                    // Consultar las primeras 5 categorías
                    $sql = "SELECT id_categoria, nombre_categoria FROM categorias_imagenes LIMIT 5";
                    $resultado = $conn->query($sql);

                    // Imprimir botones para las primeras 5 categorías
                    if ($resultado->num_rows > 0) {
                        while($row = $resultado->fetch_assoc()) {
                            echo '<a href="jugar.php?id_categoria=' . $row["id_categoria"] . '" class="btn btn-primary btn-lg">' . $row["nombre_categoria"] . '</a><br>';
                        }
                    } else {
                        echo "No se encontraron categorías.";
                    }

                    // Cerrar la conexión
                    $conn->close();
                    ?>
                </div>
            </div>

            <!-- Div para el personaje animado -->
            <div class="character-container">
                <img src="imagenes/pocoyin.png" alt="Personaje Animado" class="img-fluid" />
            </div>
        </div>
    </div>

    <div class="premium-banner">
        <p>¡Sé Premium y disfruta de muchas más opciones y beneficios!</p>
        <button class="premium-btn" onclick="window.location.href='actualizar.php'">¡Hazte Premium!</button>

    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
