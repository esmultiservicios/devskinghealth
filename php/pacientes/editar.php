<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

$pacientes_id = $_POST['pacientes_id'];
$estado = 1; //1. Activo 2. Inactivo
$fecha_registro = date("Y-m-d H:i:s");
$usuario = $_SESSION['colaborador_id'];	

$consulta_expediente = "SELECT pacientes_id,  nombre, apellido, identidad, telefono1, telefono2, fecha_nacimiento, fecha, email, genero, localidad,
(CASE WHEN estado = '1' THEN 'Activo' ELSE 'Inactivo' END) AS 'estado',
(CASE WHEN expediente = '0' THEN 'TEMP' ELSE expediente END) AS 'expediente', departamento_id, municipio_id, pais_id, responsable, responsable_id, referido_id
	FROM pacientes
	WHERE pacientes_id = '$pacientes_id'";
$result = $mysqli->query($consulta_expediente);   

$expediente = "";
$nombre = "";
$apellido = "";
$sexo = "";
$telefono1 = "";
$telefono2 = "";
$fecha_nacimiento = "";
$correo = "";
$fecha = "";
$localidad = "";
$departamento_id = "";
$municipio_id = "";
$pais_id = "";
$responsable = "";
$responsable_id = "";
$referido_id = "";
$identidad = "";
	
if($result->num_rows>0){
	$consulta_expediente1 = $result->fetch_assoc();
	$expediente = $consulta_expediente1['expediente'];
	$nombre = $consulta_expediente1['nombre'];
	$apellido = $consulta_expediente1['apellido'];
	$sexo = $consulta_expediente1['genero'];
	$telefono1 = $consulta_expediente1['telefono1'];
	$telefono2 = $consulta_expediente1['telefono2'];
	$fecha_nacimiento = $consulta_expediente1['fecha_nacimiento'];
	$correo = $consulta_expediente1['email'];
	$fecha = $consulta_expediente1['fecha'];
	$localidad = $consulta_expediente1['localidad'];
	$departamento_id = $consulta_expediente1['departamento_id'];
	$municipio_id = $consulta_expediente1['municipio_id'];
	$pais_id = $consulta_expediente1['pais_id'];	
	$responsable = $consulta_expediente1['responsable'];	
	$responsable_id = $consulta_expediente1['responsable_id'];	
	$referido_id = $consulta_expediente1['referido_id'];
	$identidad = $consulta_expediente1['identidad'];	
}

//OBTENER LA EDAD DEL USUARIO 
/*********************************************************************************/
$valores_array = getEdad($fecha_nacimiento);
$anos = $valores_array['anos'];
$meses = $valores_array['meses'];	  
$dias = $valores_array['dias'];	
/*********************************************************************************/
if ($anos>1 ){
   $palabra_anos = "Años";
}else{
  $palabra_anos = "Año";
}

if ($meses>1 ){
   $palabra_mes = "Meses";
}else{
  $palabra_mes = "Mes";
}

if($dias>1){
	$palabra_dia = "Días";
}else{
	$palabra_dia = "Día";
}

$datos = array(
	0 => $nombre, 
	1 => $apellido,	
	2 => $telefono1,
	3 => $telefono2,
	4 => $sexo,
	5 => $correo,
	6 => $anos." ".$palabra_anos.", ".$meses." ".$palabra_mes." y ".$dias." ".$palabra_dia,					
	7 => $expediente,
	8 => $localidad,
	9 => $fecha_nacimiento,	
	10 => $departamento_id,	
	11 => $municipio_id,		
	12 => $pais_id,	
	13 => $responsable,	
	14 => $responsable_id,		
	15 => $referido_id,	
	16 => $identidad,		
);
echo json_encode($datos);
?>