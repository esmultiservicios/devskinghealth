<?php
session_start();   
include "../funtions.php";

//CONEXION A DB
$mysqli = connect_mysqli();

$consulta = "SELECT * FROM responsable";
$result = $mysqli->query($consulta) or die($mysqli->error);

if($result->num_rows>0){	
	while($consulta2 = $result->fetch_assoc()){
		echo '<option value="'.$consulta2['responsable_id'].'">'.$consulta2['nombre'].'</option>';
	}
}else{
	echo '<option value="">No hay registros</option>';
}

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN