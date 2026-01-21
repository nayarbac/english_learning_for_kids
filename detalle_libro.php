<?php
// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "Informatica100*";
$dbname = "ingles";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Obtener el ID del libro desde la URL
$id_libro = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Obtener la información del libro
$sql = "SELECT * FROM libros WHERE id_libro = $id_libro";
$result = $conn->query($sql);
$libro = $result->fetch_assoc();

if (!$libro) {
    die("Libro no encontrado");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles del Libro</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        /* Estilos generales */
        body {
            background-image: url("imagenes/confetti-doodles.png");
    background-size: cover; /* Fondo azul claro */
            font-family: 'Comic Sans MS', cursive, sans-serif; /* Fuente amigable para niños */
            color: #333; /* Color del texto */
        }

        /* Estilo para el tooltip que muestra la traducción */
        .tooltip-translation {
            position: relative;
            display: inline-block;
            cursor: pointer;
            border-bottom: 1px dotted #ff6347; /* Subrayado de palabras en un color vibrante */
        }

        .tooltip-translation .tooltip-text {
            visibility: hidden;
            width: auto;
            max-width: 200px;
            background-color: #ffeb3b; /* Fondo amarillo */
            color: #000;
            text-align: center;
            border-radius: 5px;
            padding: 5px;
            position: absolute;
            z-index: 1;
            bottom: 100%; /* Mostrar encima de la palabra */
            left: 50%;
            transform: translateX(-50%);
            opacity: 0;
            transition: opacity 0.3s;
        }

        .tooltip-translation:hover .tooltip-text {
            visibility: visible;
            opacity: 1;
        }

        /* Estilo para las tarjetas */
        .card {
            border: 2px solid #ff6347; /* Borde de color vibrante */
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            margin-bottom: 20px; /* Espaciado entre tarjetas */
        }

        .card-img-top {
            height: 200px; /* Altura fija para la imagen */
            object-fit: cover; /* Mantener proporciones de la imagen */
            border-bottom: 2px solid #ff6347; /* Borde inferior en la imagen */
        }

        .card-body {
            padding: 15px;
            background-color: #fff; /* Fondo blanco para las tarjetas */
        }

        .card-title {
            font-size: 1.5rem;
            font-weight: bold;
            color: #ff6347; /* Color vibrante para el título */
        }

        .card-text {
            font-size: 1rem;
            color: #555;
        }

        /* Estilo para la tarjeta de descripción */
        .card-descripcion {
            border: 2px solid #007bff; /* Borde azul */
            border-radius: 10px;
            background-color: #e3f2fd; /* Fondo azul claro */
            padding: 10px;
        }

        /* Botón de lectura */
        #playPauseBtn {
            background-color: #ff6347; /* Color rojo vibrante para el botón */
            color: white; /* Texto blanco */
            padding: 12px 24px;
            border: none;
            border-radius: 5px;
            font-size: 1.2rem;
            cursor: pointer;
            transition: background-color 0.3s; /* Transición suave */
            margin-top: 20px; /* Espacio arriba del botón */
        }

        #playPauseBtn:hover {
            background-color: #c62828; /* Color rojo más oscuro al pasar el ratón */
        }
        /* Estilos para el botón de regresar */
.regresar-btn {
    position: absolute;
    top: 80px; /* Ajusta si el botón no queda bien debajo de la navbar */
    left: 20px;
    font-size: 1.2rem;
    padding: 10px 15px;
    border-radius: 10px;
    color: #fff;
    background-color: #FF6F61; /* Naranja brillante */
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

    <div class="container mt-5">
        <div class="row">
            <div class="col-md-4"> <!-- Tamaño de columna más pequeño -->
                <div class="card">
                    <img src="<?php echo $libro['imagen']; ?>" class="card-img-top" alt="<?php echo $libro['titulo']; ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $libro['titulo']; ?></h5>
                        <p class="card-text"><strong>Autor:</strong> <?php echo $libro['autor']; ?></p>
                        <p class="card-text"><strong>Año:</strong> <?php echo $libro['anio']; ?></p>
                        <p class="card-text"><strong>Editorial:</strong> <?php echo $libro['editorial']; ?></p>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card card-descripcion">
                    <div class="card-body">
                        <h5 class="card-title">Descripción del Libro</h5>
                        <p class="card-text" id="descripcion"><?php echo $libro['texto_libro']; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                
            </div>
        </div>
    </div>

<script>
    // Diccionario de palabras en inglés con sus traducciones en español
    const diccionario = {
        "once": "una vez",
        "upon": "sobre",
        "time": "tiempo",
        "lived": "vivía",
                  "once": "una vez",
    "upon": "sobre",
    "time": "tiempo",
    "lived": "vivía",
    "wood": "madera",
    "carver": "carpintero",
    "named": "llamado",
    "he": "él",
    "made": "hizo",
    "puppets": "marionetas, títeres",
    "from": "de",
    "pieces": "pedazos",
    "called": "llamó",
    "favorite": "favorito",
    "father": "padre",
    "boy": "niño",
    "wished": "deseó",
    "night": "noche",
    "blue": "azul",
    "fairy": "hada",
    "spell": "hechizo",
    "next": "siguiente",
    "day": "día",
    "happy": "feliz",
    "walk": "caminar",
    "sit": "sentarse",
    "talk": "hablar",
    "real": "real",
    "school": "escuela",
    "sold": "vendió",
    "coat": "abrigo",
    "book": "libro",
    "goodbye": "adiós",
    "stopped": "se detuvo",
    "watch": "mirar",
    "show": "espectáculo",
    "evil": "malvado",
    "master": "maestro",
    "locked": "encerrado",
    "inside": "dentro",
    "free": "libre",
    "met": "conoció",
    "fox": "zorro",
    "wonderful": "maravilloso",
    "place": "lugar",
    "island": "isla",
    "lied": "mintió",
    "nose": "nariz",
    "grew": "creció",
    "very": "muy",
    "long": "largo",
    "bird": "pájaro",
    "peck": "picar",
    "chick": "pollito",
    "wishing": "deseando",
    "dining": "comedor",
    "table": "mesa",
    "never": "nunca",
    "help": "ayudar",
    "kind": "amable",
    "words": "palabras",
    "great": "genial",
    "let": "dejar",
    "come": "venir",
    "rest": "descansar",
    "let's": "vamos a",
    "swing": "columpio",
    "be": "ser",
    "big": "grande",
    "adventure": "aventura",
    "home": "hogar",
    "light": "luz",
    "cloud": "nube",
    "life": "vida",
    "see": "ver",
    "smile": "sonrisa",
    "world": "mundo",
    "bright": "brillante",
    
    // Nuevas palabras basadas en el segundo texto
    "world": "mundo",
    "animals": "animales",
    "arrogant": "arrogante",
    "hare": "liebre",
    "fastest": "más rápido",
    "laughing": "riendo",
    "slow": "lento",
    "turtle": "tortuga",
    "run": "correr",
    "much": "mucho",
    "tired": "cansado",
    "strange": "extraño",
    "bet": "apuesta",
    "sure": "seguro",
    "win": "ganar",
    "race": "carrera",
    "stone": "piedra",
    "amused": "divertido",
    "animals": "animales",
    "applauses": "aplausos",
    "road": "camino",
    "finishing": "meta",
    "line": "línea",
    "ready": "listo",
    "started": "empezó",
    "relying": "confiando",
    "speed": "velocidad",
    "lazing": "holgazanear",
    "time": "tiempo",
    "enough": "suficiente",
    "creature": "criatura",
    "ahead": "adelante",
    "stopped": "detuvo",
    "sat": "se sentó",
    "rest": "descansar",
    "passed": "pasó",
    "made": "hizo",
    "mock": "burlarse",
    "advantage": "ventaja",
    "quick": "rápido",
    "walk": "caminar",
    "several": "varias",
    "kept": "mantuvo",
    "way": "camino",
    "arrived": "llegó",
    "woke": "despertó",
    "might": "podía",
    "lesson": "lección",
    "never": "nunca",
    "mock": "burlarse",
    "others": "otros"
        // Agrega más palabras del diccionario aquí
    };

    // Procesar el texto del libro
    const descripcionElemento = document.getElementById('descripcion');
    const textoOriginal = descripcionElemento.textContent;

    // Dividir el texto en palabras y procesar cada una
    const palabras = textoOriginal.split(/\b/); // Divide en palabras, manteniendo espacios y puntuación

    const textoProcesado = palabras.map(palabra => {
        const palabraLimpia = palabra.toLowerCase().replace(/[.,!?]/g, ''); // Limpia puntuación y minúsculas
        if (diccionario[palabraLimpia]) {
            // Si está en el diccionario, envuelve la palabra con un tooltip
            return `
                <span class="tooltip-translation">
                    ${palabra}
                    <span class="tooltip-text">${diccionario[palabraLimpia]}</span>
                </span>
            `;
        }
        return palabra; // Si no está en el diccionario, deja la palabra como está
    }).join('');

    // Reemplaza el contenido del texto con el texto procesado
    descripcionElemento.innerHTML = textoProcesado;
</script>


</body>
</html>

<?php
$conn->close(); // Cerrar conexión
?>