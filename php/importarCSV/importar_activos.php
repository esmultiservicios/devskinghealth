<?php
session_start();   
include('../funtions.php');

//CONEXION A DB
$mysqli = connect_mysqli(); 
date_default_timezone_set('America/Tegucigalpa');
  
if ($_FILES['csv']['size'] > 0) {
$csv = $_FILES['csv']['tmp_name'];
$handle = fopen($csv,'r');

/*
  $data[0] corresponde al id_cuenta
  $data[1] corresponde a la cuenta
  $data[2] corresponde a la extensión
*/
$first = false;

while ($data = fgetcsv($handle, 1000, ",", ";")) {
    if (!$first) {
        // Activa la bandera en la primera iteración y continúa
        $first = true;
        continue;
    }

    // CONSULTAR EXISTENCIA DE EXPEDIENTE
    $consultar_expediente = "SELECT pacientes_id 
                             FROM pacientes 
                             WHERE expediente = '{$data[0]}'";
    $result = $mysqli->query($consultar_expediente);

    if ($result && $result->num_rows > 0) {
        // Obtener el ID del paciente
        $consultar_expediente2 = $result->fetch_assoc();
        $pacientes_id = $consultar_expediente2['pacientes_id'];

        // Actualizar el estado del paciente
        $update = "UPDATE pacientes 
                   SET status = '1' 
                   WHERE pacientes_id = '$pacientes_id'";
        $mysqli->query($update);
    } else {
        continue;
    }
}

echo 'OK';

fclose($handle);
}

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN  
?>