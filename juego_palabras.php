<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['id_usuario'])) {
    die("No has iniciado sesión. Por favor, inicia sesión primero.");
}

$id_usuario = $_SESSION['id_usuario']; // Obtener el ID del usuario de la sesión

// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "Informatica100*";
$dbname = "ingles";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener el nivel actual del juego
$nivel = isset($_GET['nivel']) ? intval($_GET['nivel']) : 1;

// Consultar las palabras para el nivel actual
$sql = "SELECT palabra_espanol, palabra_ingles FROM palabras WHERE id_nivel = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $nivel);
$stmt->execute();
$result = $stmt->get_result();

$palabras = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $palabras[] = $row;
    }
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Juego de Pares - Nivel <?php echo $nivel; ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* Fondo degradado */
        body {
            background: linear-gradient(to right, #f39c12, #8e44ad);
            font-family: 'Comic Sans MS', cursive, sans-serif;
            color: #fff;
        }

        /* Estilos del título */
        h1 {
            font-size: 2.5rem;
            margin-bottom: 20px;
            font-weight: bold;
            text-align: center;
        }

        /* Contenedor de la aplicación */
        .game-container {
            background-color: #fff;
            color: #333;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
            text-align: center;
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

        /* Diseño de las cartas */
        .card {
            width: 120px;
            height: 80px;
            background-color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            margin: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            font-size: 1.5rem;
            font-weight: bold;
            color: #3498db;
        }

        /* Efecto hover de las cartas */
        .card:hover {
            transform: scale(1.1);
            background-color: #ffdd59;
        }

        /* Cartas seleccionadas y encontradas */
        .selected {
            border: 3px solid #3498db;
            background-color: #f1c40f;
        }

        .matched {
            background-color: #2ecc71;
            color: #fff;
            border: none;
        }

        /* Contador de puntuación */
        #score, #errorCount {
            font-size: 1.5rem;
            font-weight: bold;
            margin: 10px 0;
        }

        /* Botón de ver puntuaciones */
        #puntuacionesBtn {
            margin-top: 20px;
            background-color: #f39c12;
            color: #fff;
            border: none;
            transition: background-color 0.3s;
        }

        #puntuacionesBtn:hover {
            background-color: #e67e22;
        }

        /* Ocultar por defecto */
        .d-none {
            display: none;
        }
    </style>
</head>
<body>
    <!-- Contenido Principal -->
    <div class="container mt-2">
        <button onclick="history.back()" class="btn regresar-btn">
            <i class="fas fa-arrow-left"></i> Regresar
        </button>
    </div>

    <div class="container mt-5">
        <div class="game-container">
            <h1>Juego de Pares - Nivel <?php echo $nivel; ?></h1>
            <div id="game" class="d-flex flex-wrap justify-content-center"></div>
            <p>Puntuación: <span id="score" class="badge badge-success">0</span></p>
            <p>Errores: <span id="errorCount" class="badge badge-danger">0</span></p>
            <button id="puntuacionesBtn" class="btn d-none">Ver Puntuaciones</button>
            <!-- Botón para guardar puntuación -->
            <button id="guardarPuntuacionBtn" class="btn btn-success mt-3">Guardar Puntuación</button>
        </div>
    </div>

    <script>
        let selectedCard = null;
        let score = 0;
        let errorCount = 0;
        let matchedPairs = 0; // Parejas encontradas
        const totalPairs = <?php echo count($palabras); ?>; // Total de parejas

        // Palabras del juego
        const palabras = <?php echo json_encode($palabras); ?>;

        // Crear cartas en el juego
        const gameContainer = document.getElementById('game');
        palabras.forEach(pair => {
            const cardEspañol = document.createElement('div');
            cardEspañol.classList.add('card', 'mx-2', 'my-2');
            cardEspañol.dataset.language = 'espanol';
            cardEspañol.innerText = pair.palabra_espanol;

            const cardIngles = document.createElement('div');
            cardIngles.classList.add('card', 'mx-2', 'my-2');
            cardIngles.dataset.language = 'ingles';
            cardIngles.innerText = pair.palabra_ingles;

            gameContainer.appendChild(cardEspañol);
            gameContainer.appendChild(cardIngles);
        });

        function checkMatch(card1, card2) {
            const espanol = card1.dataset.language === 'espanol' ? card1.innerText : card2.innerText;
            const ingles = card1.dataset.language === 'ingles' ? card1.innerText : card2.innerText;

            return palabras.some(pair => pair.palabra_espanol === espanol && pair.palabra_ingles === ingles);
        }

        document.querySelectorAll('.card').forEach(card => {
            card.addEventListener('click', () => {
                if (!selectedCard) {
                    selectedCard = card;
                    selectedCard.classList.add('selected');
                } else {
                    selectedCard.classList.remove('selected');
                    
                    if (checkMatch(selectedCard, card)) {
                        selectedCard.classList.add('matched');
                        card.classList.add('matched');
                        score++;
                        matchedPairs++; // Incrementar parejas encontradas
                        document.getElementById('score').innerText = score;

                        if (matchedPairs === totalPairs) {
                            document.getElementById('puntuacionesBtn').classList.remove('d-none'); // Mostrar botón
                        }
                    } else {
                        errorCount++;
                        document.getElementById('errorCount').innerText = errorCount;

                        Swal.fire({
                            icon: 'error',
                            title: '¡Incorrecto!',
                            text: 'No has encontrado un par.',
                        });
                    }

                    selectedCard = null;
                }
            });
        });

        document.getElementById('puntuacionesBtn').addEventListener('click', () => {
            window.location.href = 'puntuaciones.php';
        });

        // Guardar puntuación
        document.getElementById('guardarPuntuacionBtn').addEventListener('click', () => {
            const data = {
                id_usuario: <?php echo $id_usuario; ?>,
                id_nivel: <?php echo $nivel; ?>,
                puntuacion: score,
                errores: errorCount
            };

            fetch('guardar_puntuacion.php', {
                method: 'POST',
                body: JSON.stringify(data),
                headers: { 'Content-Type': 'application/json' }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Puntuación guardada',
                        text: 'Tu puntuación ha sido guardada correctamente.',
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Hubo un problema al guardar tu puntuación.',
                    });
                }
            })
            .catch(error => {
                console.error('Error al guardar puntuación:', error);
            });
        });
    </script>
</body>
</html>
