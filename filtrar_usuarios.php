<?php
$host = 'localhost';
$usuario_db = 'root';
$contrasena_db = 'Informatica100*';
$nombre_db = 'ingles';

// Conexión a la base de datos
$conn = new mysqli($host, $usuario_db, $contrasena_db, $nombre_db);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener los parámetros de los filtros
$tipo_cuenta = $_GET['tipo_cuenta'] ?? 'todos'; // "todos" como predeterminado
$comparador = $_GET['comparador'] ?? 'mayor';   // Comparador: "mayor" o "menor"
$puntos = intval($_GET['puntos'] ?? 0);

// Construir la consulta dinámica
$operador = ($comparador === 'mayor') ? '>=' : '<='; // Elegir el operador según el comparador
$sql = "SELECT id_usuario, nombre, correo, tipo_cuenta, puntos FROM usuarios WHERE puntos $operador ?";
$params = [$puntos]; // Parámetros para la consulta

if ($tipo_cuenta !== 'todos') {
    $sql .= " AND tipo_cuenta = ?";
    $params[] = $tipo_cuenta;
}

// Preparar y ejecutar la consulta
$stmt = $conn->prepare($sql);
if ($tipo_cuenta === 'todos') {
    $stmt->bind_param("i", $params[0]); // Solo filtrar por puntos
} else {
    $stmt->bind_param("is", $params[0], $params[1]); // Filtrar por puntos y tipo de cuenta
}

$stmt->execute();
$result = $stmt->get_result();

// Generar la tabla HTML
if ($result->num_rows > 0) {
    echo "<table>";
    echo "<tr><th>ID</th><th>Nombre</th><th>Correo</th><th>Tipo de Cuenta</th><th>Puntos</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['id_usuario']}</td>";
        echo "<td>{$row['nombre']}</td>";
        echo "<td>{$row['correo']}</td>";
        echo "<td>{$row['tipo_cuenta']}</td>";
        echo "<td>{$row['puntos']}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No se encontraron usuarios que coincidan con los filtros.</p>";
}

$conn->close();
?>
