<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

$consulta = "SELECT *
	FROM vigencia_cotizacion";
$result = $mysqli->query($consulta) or die($mysqli->error);		

if($result->num_rows>0){	
	while($consulta2 = $result->fetch_assoc()){
		echo '<option value="'.$consulta2['vigencia_cotizacion_id'].'">'.$consulta2['valor'].'</option>';
	}
}else{
	echo '<option value="">Seleccione una Vigencia</option>';
}