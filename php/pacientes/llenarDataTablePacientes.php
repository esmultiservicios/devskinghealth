<?php
session_start();
include '../funtions.php';

// CONEXION A DB
$mysqli = connect_mysqli();

$estado = $_POST['estado'];
$paciente = $_POST['paciente'];

$consulta = "SELECT pacientes_id, CONCAT(nombre,' ',apellido) AS 'paciente', identidad, telefono1, telefono2, fecha_nacimiento, expediente AS 'expediente_', localidad,
(CASE WHEN estado = '1' THEN 'Activo' ELSE 'Inactivo' END) AS 'estado',
(CASE WHEN genero = 'H' THEN 'Hombre' ELSE 'Mujer' END) AS 'genero',
(CASE WHEN expediente = '0' THEN 'TEMP' ELSE expediente END) AS 'expediente', email
FROM pacientes
WHERE estado = '$estado'
ORDER BY expediente";;

$result = $mysqli->query($consulta);

$arreglo = array();

while ($data = $result->fetch_assoc()) {
	// Añadir el registro completo al arreglo de datos
	$arreglo['data'][] = $data;
}

echo json_encode($arreglo);

$result->free();  // LIMPIAR RESULTADO
$mysqli->close();  // CERRAR CONEXIÓN
