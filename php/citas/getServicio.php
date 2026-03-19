<?php  
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

//CONSULTA LOS DATOS DE LA ENTIDAD CORPORACION
$consulta = "SELECT s.servicio_id AS 'servicio_id', s.nombre AS 'servicio'
	FROM servicios_colaboradores AS sc
	INNER JOIN servicios AS s
	ON sc.servicio_id = s.servicio_id"; 
$result = $mysqli->query($consulta); 

if($result->num_rows>0){
	echo '<option value="">Sin seleccion</option>';
	while($consulta2 = $result->fetch_assoc()){
		echo '<option value="'.$consulta2['servicio_id'].'">'.$consulta2['servicio'].'</option>';
	}
}else{
	echo '<option value="">No hay registros</option>';
}

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN