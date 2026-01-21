<?php
// Conectar a la base de datos
$servername = "localhost";
$username = "root";
$password = "Informatica100*";
$dbname = "ingles";  // Cambia esto con el nombre de tu base de datos

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ejecutar la consulta para obtener el nombre del usuario y el total gastado en avatares
$sql = "SELECT u.nombre, totalGastadoPorUsuario(u.id_usuario) AS total_gastado
        FROM usuarios u";

$result = $conn->query($sql);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Total Gastado por Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-5">

    <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-md">
        <h1 class="text-3xl font-semibold text-center mb-5">Total Gastado por Usuario en Avatares</h1>
        
        <?php if ($result->num_rows > 0): ?>
            <table class="min-w-full table-auto">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="px-4 py-2 text-left">Nombre</th>
                        <th class="px-4 py-2 text-left">Total Gastado (en puntos)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr class="border-b">
                            <td class="px-4 py-2"><?= htmlspecialchars($row['nombre']) ?></td>
                            <td class="px-4 py-2"><?= number_format($row['total_gastado'], 2) ?> puntos</td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-center text-gray-600">No hay datos disponibles.</p>
        <?php endif; ?>
    </div>

</body>
</html>

<?php
// Cerrar la conexión a la base de datos
$conn->close();
?>
