<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli(); 

$pacientes_id = $_POST['pacientes_id'];
$agenda_id = $_POST['agenda_id'];

//CONSULTAR LOS DATOS DEL PACIENTE
$sql = "SELECT p.identidad AS 'identidad', p.fecha_nacimiento 'fecha_nacimiento', CONCAT(p.nombre, ' ', p.apellido) AS 'paciente', p.localidad AS 'localidad', p.religion_id AS 'religion', p.profesion_id AS 'profesion', CAST(a.fecha_cita AS DATE) AS 'fecha', a.servicio_id AS 'servicio_id', p.estado_civil AS 'estado_civil'
   FROM agenda AS a
   INNER JOIN pacientes AS p
   ON a.pacientes_id = p.pacientes_id
   WHERE a.agenda_id = '$agenda_id'";
$result = $mysqli->query($sql) or die($mysqli->error);  
     
$identidad = "";
$nombre = "";
$fecha_nacimiento = "";
$edad = "";
$profesion = "";
$religion = "";
$servicio_id = "";
$fecha_cita = "";
$palabra_anos = "";
$palabra_mes = "";
$palabra_dia = "";
$estado_civil = "";

//OBTENEMOS LOS VALORES DEL REGISTRO
if($result->num_rows>0){
	$consulta_registro = $result->fetch_assoc();
	
	$identidad = $consulta_registro['identidad'];
	$fecha_nacimiento = $consulta_registro['fecha_nacimiento'];	
	$paciente = $consulta_registro['paciente'];
	$localidad = $consulta_registro['localidad'];	
	$religion = $consulta_registro['religion'];
	$profesion = $consulta_registro['profesion'];
	$fecha_cita = $consulta_registro['fecha'];	
	$servicio_id = $consulta_registro['servicio_id'];
	$estado_civil = $consulta_registro['estado_civil'];	
	
	//CONSULTA AÑO, MES y DIA DEL PACIENTE
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
}

//OBTENER HISTORIA CLINICA
$query_historia = "SELECT pacientes_id, antecedentes, historia_clinica, examen_fisico, diagnostico, num_hijos
	FROM atenciones_medicas
	WHERE pacientes_id = '$pacientes_id'
	ORDER BY atencion_id DESC limit 1";
$result_historia = $mysqli->query($query_historia) or die($mysqli->error);
	
$antecedentes = "";
$historia_clinica = "";
$examen_fisico = "";
$diagnostico = "";
$num_hijos = 0;

if($result_historia->num_rows>0){
	$consulta_historia = $result_historia->fetch_assoc();
	
	$antecedentes = $consulta_historia['antecedentes'];
	$historia_clinica = $consulta_historia['historia_clinica'];
	$examen_fisico = $consulta_historia['examen_fisico'];
	$diagnostico = $consulta_historia['diagnostico'];
	$num_hijos = $consulta_historia['num_hijos'];	
}

//OBTENER SEGUIMIENTO
$query_seguimiento = "SELECT fecha, seguimiento
	FROM atenciones_medicas
	WHERE pacientes_id = '$pacientes_id'";
$result_seguimiento = $mysqli->query($query_seguimiento) or die($mysqli->error);
	
$seguimiento_consulta = "";
	
while($registro_seguimiento = $result_seguimiento->fetch_assoc()){
	$fecha = $registro_seguimiento['fecha'];
	$seguimiento = $registro_seguimiento['seguimiento'];
	
	$seguimiento_consulta.= "Fecha: ".$fecha."\n".$seguimiento."\n\n";
}	

$datos = array(
	 0 => $identidad, 
 	 1 => $paciente,	
	 2 => $anos." ".$palabra_anos.", ".$meses." ".$palabra_mes." y ".$dias." ".$palabra_dia,	
 	 3 => $localidad,	
	 4 => $religion,
	 5 => $profesion,	 
     6 => $pacientes_id,
     7 => $fecha_cita,
     8 => $fecha_nacimiento,
     9 => $antecedentes,
     10 => $historia_clinica,
     11 => $examen_fisico,	 
     12 => $diagnostico,	 	 
	 13 => $seguimiento_consulta,
	 14 => $servicio_id,	 
	 15 => $estado_civil,
	 16 => $num_hijos,
	 17 => $anos,
);	
	
echo json_encode($datos);

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN