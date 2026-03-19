<?php
include "../funtions.php";

$servername = SERVER_MAIN;
$username = USER_MAIN;
$password = PASS_MAIN;
$dbname = DBIZZY;

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$sql = "SELECT productos_id, nombre FROM productos WHERE estado = 1 AND tipo_producto_id = 1";
$result = $conn->query($sql);

$productos = array();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // Eliminar espacios a la izquierda y derecha del campo 'nombre'
        $row['nombre'] = trim($row['nombre']);
        $productos[] = $row;
    }
}

$conn->close();

echo json_encode($productos);