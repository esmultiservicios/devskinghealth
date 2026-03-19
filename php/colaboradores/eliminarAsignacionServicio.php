<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli(); 

$servicios_colaboradores_id = $_POST['servicios_colaboradores_id'];
$colaborador_id = $_POST['colaborador_id'];
$fecha_registro = date("Y-m-d H:i:s");
$fecha = date("Y-m-d");
$usuario = $_SESSION['colaborador_id'];

//OBTENEMOS EL NOMBRE DEL SERVICIO ASIGNADO
$query_consulta = "SELECT s.nombre AS 'servicio'
   FROM servicios_colaboradores AS sc
   INNER JOIN servicios AS s
   ON sc.servicio_id = s.servicio_id
   WHERE sc.colaborador_id = '$colaborador_id'";
$result = $mysqli->query($query_consulta);

$servicio = "";

if($result->num_rows>0){
   $consulta = $result->fetch_assoc();
   $servicio = $consulta['servicio'];
}

//OBTENEMOS EL NOMBRE DEL COLABORADOR
$query_consulta = "SELECT CONCAT(nombre,' ',apellido) AS 'colaborador', identidad
   FROM colaboradores
   WHERE colaborador_id = '$colaborador_id'";
$result = $mysqli->query($query_consulta);

$colaborador = "";
$identidad = "";

if($result->num_rows>0){
   $consulta = $result->fetch_assoc();
   $colaborador = $consulta['colaborador'];
   $identidad = $consulta['identidad'];
}

//ELIMINAMOS EL REGISTRO
$delete = "DELETE FROM servicios_colaboradores 
   WHERE servicios_colaboradores_id = '$servicios_colaboradores_id'";
$query = $mysqli->query($delete);

if($query){
   //INGRESAR REGISTROS EN LA ENTIDAD HISTORIAL
   $historial_numero = historial();
   $estado_historial = "Eliminar";
   $observacion_historial = "Se ha eliminado el colaborador: $colaborador del servicio: $servicio";
   $modulo = "Asignacion Servicios";
   $insert = "INSERT INTO historial 
       VALUES('$historial_numero','0','0','$modulo','$id','0','0','$fecha','$estado_historial','$observacion_historial','$usuario','$fecha_registro')";	
   $mysqli->query($insert);	   
   /********************************************/	
   echo 1;
}else{
	echo 2;
}

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN  
?>