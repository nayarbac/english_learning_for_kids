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

// Consulta para obtener los libros
$sql = "SELECT id_libro, titulo, imagen FROM libros";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Libros</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background-color: #fffacd;
            font-family: 'Arial', sans-serif;
        }

        .card-acerca {
            background-color: #fff5c3;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            padding: 20px;
            margin-bottom: 30px;
        }

        .card-acerca h5 {
            font-size: 1.8rem;
            font-weight: bold;
            color: #5b8c5a;
        }

        .card-acerca p {
            font-size: 1.2rem;
            color: #4a4a4a;
        }

        .card {
            margin-bottom: 20px;
            border-radius: 20px;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .card:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
        }

        .card-img-top {
            border-radius: 20px 20px 0 0;
            max-height: 250px;
            object-fit: cover;
        }

        .card-body {
            padding: 15px;
            text-align: center;
            background-color: #ffffff;
        }

        .card-body h5 {
            font-size: 1.5rem;
            font-weight: bold;
            color: #34495e;
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

        .character-container {
            background-color: #fce38a;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 250px;
        }

        .character-image {
            width: auto;
            height: 100%;
            border-radius: 50%;
            transition: transform 0.3s ease;
        }

        .character-image:hover {
            transform: scale(1.1) rotate(10deg);
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
    <button onclick="history.back()" class="btn btn-outline-secondary regresar-btn">
        <i class="fas fa-arrow-left"></i> Regresar
    </button>
</div>

<div class="container mt-4">
    <h1 class="text-center">Libros Disponibles</h1>

    <div class="row">
        <div class="col-md-8">
            <div class="card card-acerca">
                <div class="card-body">
                    <h5 class="card-title">Acerca de esta página</h5>
                    <p class="card-text">Aquí podrás encontrar los libros más famosos en inglés, para que leas, conozcas nuevas palabras y disfrutes de tus personajes favoritos. ¡Aprender inglés nunca fue tan divertido!</p>
                </div>
            </div>
        </div>

        <div class="col-md-4 text-center">
            <div class="character-container">
                <img src="imagenes/esponjagar.png" 
                     alt="Personaje animado" 
                     class="character-image">
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<div class="col-md-3 col-sm-6 col-xs-12">';
                echo '<div class="card">';
                echo '<a href="detalle_libro.php?id=' . $row["id_libro"] . '">';
                echo '<img src="' . $row["imagen"] . '" class="card-img-top" alt="' . $row["titulo"] . '">';
                echo '<div class="card-body">';
                echo '<h5 class="card-title">' . $row["titulo"] . '</h5>';
                echo '</div>';
                echo '</a>';
                echo '</div>';
                echo '</div>';
            }
        } else {
            echo '<p>No se encontraron libros.</p>';
        }
        $conn->close();
        ?>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
