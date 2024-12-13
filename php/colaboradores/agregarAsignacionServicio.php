<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli(); 

$colaborador_id = $_POST['colaborador_id'];
$servicio_id = $_POST['servicio_id'];
$fecha_registro = date("Y-m-d H:i:s");
$fecha = date("Y-m-d");
$usuario = $_SESSION['colaborador_id'];
   
//CONSULTAMOS QUE SI EL COLABORADOR YA HA SIDO ASIGNADO AL SERVICIO Y QUE NO SE REPITA
$consulta = "SELECT servicios_colaboradores_id 
      FROM servicios_colaboradores 
	  WHERE colaborador_id = '$colaborador_id'";
$result = $mysqli->query($consulta);	  

if($result->num_rows==0){
	//OBTENER CORRELATIVO
	$servicios_colaboradores_id = correlativo('servicios_colaboradores_id', 'servicios_colaboradores');
	$insert = "INSERT INTO servicios_colaboradores VALUES('$servicios_colaboradores_id', '$colaborador_id', '$servicio_id', '$usuario', '$fecha_registro')";
	$query = $mysqli->query($insert);

	// Establecer el resultado basado en el éxito de la consulta
	$datos = $query ? [
		0 => "Almacenado", 
		1 => "Registro Almacenado Correctamente", 
		2 => "success",
		3 => "btn-primary",
		4 => "formulario_asignacion_servicios_colaboradores",
		5 => "Registro",
		6 => "asignarServicioColaboradores", // FUNCION DE LA TABLA QUE LLAMAREMOS PARA QUE ACTUALICE (DATATABLE BOOSTRAP)
		7 => "asignar_servicio_colaborador", // Modals Para Cierre Automático
	] : [
		0 => "Error", 
		1 => "No se pudo almacenar este registro, por favor intente más tarde", 
		2 => "error",
		3 => "btn-danger",
		4 => "",
		5 => "",
	];
}else{
	$datos = [
		0 => "Error", 
		1 => "Lo sentimos este registro ya existe no se puede almacenar", 
		2 => "error",
		3 => "btn-danger",
		4 => "",
		5 => "",		
	];	
}

echo json_encode($datos);
$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN