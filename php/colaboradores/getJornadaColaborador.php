<?php
session_start();   
include "../funtions.php";

//CONEXION A DB
$mysqli = connect_mysqli();

$consulta = "SELECT jornada_id, nombre 
  FROM jornada";
$result = $mysqli->query($consulta); 
  
if($result->num_rows>0){
	while($consulta2 = $result->fetch_assoc()){
	    echo '<option value="'.$consulta2['jornada_id'].'">'.$consulta2['nombre'].'</option>';
	}
}else{
	echo '<option value="">No hay resultados que mostrar</option>';
}

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN