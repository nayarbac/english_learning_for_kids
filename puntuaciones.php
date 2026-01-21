<?php
// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "Informatica100*";
$dbname = "ingles";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$nivel = isset($_GET['nivel']) ? $_GET['nivel'] : 'principiante'; 

// Consulta SQL para obtener todos los niveles
$sql_niveles = "SELECT * FROM niveles";
$result_niveles = $conn->query($sql_niveles);

.errores, puntuaciones.fecha 
        FROM puntuaciones 
        JOIN usuarios ON puntuaciones.id_usuario = usuarios.id_usuario 
        JOIN niveles ON puntuaciones.id_nivel = niveles.id_nivel 
        WHERE niveles.nombre_nivel = ? 
        ORDER BY puntuaciones.puntuacion DESC, puntuaciones.fecha DESC
        LIMIT 10";

// Preparar la consulta
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $nivel);  // "s" es para string

// Ejecutar la consulta
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Puntuaciones</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&family=Baloo+2:wght@400;700&display=swap" rel="stylesheet">
    <style>
        /* Diseño general */
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #ffeb3b; /* Amarillo brillante */
            color: #333;
            text-align: center;
            margin: 0;
            padding: 20px;
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

        h1 {
            font-family: 'Baloo 2', cursive;
            font-size: 3rem;
            color: #ff4081; /* Rosa brillante */
            margin-bottom: 30px;
            text-shadow: 2px 2px 10px rgba(0, 0, 0, 0.2);
        }

        /* Formulario de selección de nivel */
        form {
            margin-bottom: 20px;
            background-color: #fff;
            padding: 10px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        label {
            font-size: 1.2rem;
            color: #333;
        }

        select, button {
            padding: 10px;
            font-size: 1rem;
            margin-top: 10px;
            background-color: #ff4081;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #e91e63;
        }

        /* Tabla de puntuaciones */
        table {
            width: 100%;
            max-width: 900px;
            margin: 0 auto;
            border-collapse: collapse;
            background-color: #ffffff;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s;
        }

        table:hover {
            transform: scale(1.05);
        }

        th, td {
            padding: 15px;
            text-align: center;
            font-size: 1.1rem;
        }

        th {
            background-color: #ff4081;
            color: white;
            font-weight: bold;
            border-radius: 10px;
        }

        td {
            background-color: #fff3e0; /* Amarillo suave */
            color: #444;
            font-weight: 600;
            border-bottom: 2px solid #ff4081;
        }

        tr:nth-child(even) td {
            background-color: #ffe0b2; /* Amarillo pastel */
        }

        tr:hover td {
            background-color: #ffeb3b; /* Resalta con amarillo al pasar el ratón */
        }

        /* Estilo para los iconos */
        th, td {
            font-size: 1.2rem;
        }

        /* Efecto de sombra al pasar el ratón sobre las celdas */
        td:hover {
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);
            cursor: pointer;
        }

        /* Responsive */
        @media (max-width: 768px) {
            h1 {
                font-size: 2.5rem;
            }
            th, td {
                font-size: 1rem;
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    
    <div class="container mt-2">
    <button onclick="history.back()" class="btn btn-outline-secondary regresar-btn">
        <i class="fas fa-arrow-left"></i> Regresar
    </button>
</div>

    <h1>🎉 Puntuaciones de este juego (top  10) 🎉</h1>

    <!-- Formulario de selección de nivel -->
    <form action="" method="GET">
        <label for="nivel">Selecciona el nivel:</label>
        <select name="nivel" id="nivel" required>
            <?php
        
            while ($row_nivel = $result_niveles->fetch_assoc()) {
              
                echo "<option value='" . htmlspecialchars($row_nivel['nombre_nivel']) . "' " . (($nivel == $row_nivel['nombre_nivel']) ? 'selected' : '') . ">" . htmlspecialchars($row_nivel['nombre_nivel']) . "</option>";
            }
            ?>
        </select>
        <button type="submit">Filtrar</button>
    </form>

    <!-- Mostrar las puntuaciones filtradas -->
    <h2>🎯 Puntuaciones de Nivel: <?php echo ucfirst($nivel); ?> 🎯</h2>
    <table>
        <thead>
            <tr>
                <th>👤 Nombre del Usuario</th>
                <th>📘 Nivel</th>
                <th>⭐ Puntuación</th>
                <th>❌ Errores</th>
                <th>📅 Fecha</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                // Mostrar los resultados
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['nombre']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['nombre_nivel']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['puntuacion']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['errores']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['fecha']) . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5'>No hay puntuaciones registradas para este nivel.</td></tr>";
            }
            $conn->close();
            ?>
        </tbody>
    </table>
</body>
</html>
