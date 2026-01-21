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
            margin-bottom: 100px; /* Espacio al fondo */
        }
           /* Estilos para el botón de regresar */
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
        .container {
            text-align: center;
            max-width: 900px;
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
        .d-flex {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
        }
        .character-container img {
            width: 100%;
            height: auto;
            max-width: 300px;
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
     <!-- Contenido Principal -->
    <div class="container mt-2">
      <button onclick="history.back()" class="btn regresar-btn">
        <i class="fas fa-arrow-left"></i> Regresar
      </button>
    </div>


    <div class="container">
        <div class="card">
            <h4 style="color: #ff704d; font-weight: bold;">Bienvenido a Learningles</h4>
            <p>Selecciona una categoría para comenzar a jugar.</p>
        </div>

        <div class="d-flex">
            <!-- Botones de categorías -->
            <div>
                <h2>¡Selecciona una Categoría!</h2>
                <?php
                // Código PHP para cargar categorías
                $conn = new mysqli('localhost', 'root', 'Informatica100*', 'ingles');
                if ($conn->connect_error) {
                    die("Conexión fallida: " . $conn->connect_error);
                }
                $sql = "SELECT id_categoria, nombre_categoria FROM categorias_imagenes"; // Eliminado LIMIT 5
                $resultado = $conn->query($sql);
                if ($resultado->num_rows > 0) {
                    while ($row = $resultado->fetch_assoc()) {
                        echo '<a href="jugar.php?id_categoria=' . $row["id_categoria"] . '" class="btn btn-primary">' . $row["nombre_categoria"] . '</a><br>';
                    }
                } else {
                    echo "<p>No hay categorías disponibles.</p>";
                }
                $conn->close();
                ?>
            </div>
            <!-- Imagen del personaje -->
            <div class="character-container">
                <img src="imagenes/pocoyin.png" alt="Personaje Animado">
            </div>
        </div>
    </div>
</body>
</html>
