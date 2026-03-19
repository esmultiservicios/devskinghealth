<?php 
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();
$servicio_id = $_POST['servicio'];

//CONSULTA LOS DATOS DE LA ENTIDAD CORPORACION
$consulta = "SELECT c.colaborador_id, c.nombre, c.apellido
    FROM servicios_colaboradores AS sc
    INNER JOIN colaboradores AS c ON sc.colaborador_id = c.colaborador_id
    WHERE sc.servicio_id = $servicio_id";
$result = $mysqli->query($consulta);			  

if($result->num_rows>0){
	while($consulta2 = $result->fetch_assoc()){
		$nombre_ = explode(" ", $consulta2['nombre']);
		$apellido_ = explode(" ", $consulta2['apellido']);
		$colaborador = $nombre_[0]." ".$apellido_[0];
		
		
		echo '<option value="'.$consulta2['colaborador_id'].'">'.$colaborador.'</option>';
	}
}else{
	echo '<option value="">No hay registros</option>';
}

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN