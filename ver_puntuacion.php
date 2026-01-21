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

// Obtener las categorías disponibles
$sql_categorias = "SELECT id_categoria, nombre_categoria FROM categorias_imagenes";
$result_categorias = $conn->query($sql_categorias);

// Si se selecciona una categoría, obtener los resultados filtrados
$category_filter = isset($_GET['categoria']) ? $_GET['categoria'] : '';

// Crear la consulta de puntuaciones con límite
$sql = "SELECT usuarios.nombre AS nombre_usuario, categorias_imagenes.nombre_categoria, puntuaciones_imagenes.puntuacion, puntuaciones_imagenes.errores, puntuaciones_imagenes.fecha 
        FROM puntuaciones_imagenes
        JOIN usuarios ON puntuaciones_imagenes.id_usuario = usuarios.id_usuario 
        JOIN categorias_imagenes ON puntuaciones_imagenes.id_categoria = categorias_imagenes.id_categoria";

// Aplicar el filtro de categoría si se selecciona una
if ($category_filter) {
    $sql .= " WHERE puntuaciones_imagenes.id_categoria = $category_filter";
}

// Ordenar por puntuación descendente y fecha, y limitar a 10 resultados
$sql .= " ORDER BY puntuaciones_imagenes.puntuacion DESC, puntuaciones_imagenes.fecha DESC LIMIT 10";

$result = $conn->query($sql);
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

        /* Formulario de selección de categoría */
        .categoria-select {
            margin-bottom: 20px;
        }

        select {
            font-size: 1.1rem;
            padding: 10px;
            background-color: #ff4081;
            color: white;
            border: none;
            border-radius: 5px;
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
     <button onclick="window.location.href='perfil_gratis.php'" class="btn regresar-btn">
        <i class="fas fa-arrow-left"></i> Regresar
    </button>
</div>
    <h1>🎉 Puntuaciones de este juego (top 10 mejores) 🎉</h1>

    <!-- Formulario de selección de categoría -->
    <form action="" method="GET" class="categoria-select">
        <label for="categoria">Selecciona una categoría: </label>
        <select name="categoria" id="categoria" onchange="this.form.submit()">
            <option value="">Todas</option>
            <?php
            if ($result_categorias->num_rows > 0) {
                while ($categoria = $result_categorias->fetch_assoc()) {
                    $selected = ($categoria['id_categoria'] == $category_filter) ? 'selected' : '';
                    echo "<option value='{$categoria['id_categoria']}' $selected>{$categoria['nombre_categoria']}</option>";
                }
            }
            ?>
        </select>
    </form>

    <!-- Tabla de puntuaciones -->
    <table>
        <thead>
            <tr>
                <th>👤 Nombre del Usuario</th>
                <th>📸 Categoría</th>
                <th>⭐ Puntuación</th>
                <th>❌ Errores</th>
                <th>📅 Fecha</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                // Mostrar los resultados
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['nombre_usuario']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['nombre_categoria']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['puntuacion']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['errores']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['fecha']) . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5'>No hay puntuaciones registradas.</td></tr>";
            }
            $conn->close();
            ?>
        </tbody>
    </table>
</body>
</html>
