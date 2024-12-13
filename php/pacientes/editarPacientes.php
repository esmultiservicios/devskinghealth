<?php
session_start();
include '../funtions.php';

// CONEXION A DB
$mysqli = connect_mysqli();

$pacientes_id = $_POST['pacientes_id'];
$usuario = $_SESSION['colaborador_id'];
$estado = 1;  // 1. Activo 2. Inactivo
$fecha_registro = date('Y-m-d H:i:s');

$nombre = $_POST['name'];
$apellido = $_POST['lastname'];
$sexo = $_POST['sexo'];
$telefono1 = $_POST['telefono1'];
$telefono2 = $_POST['telefono2'];
$fecha_nacimiento = $_POST['fecha_nac'];

$departamento_id = isset($_POST['departamento_id']) && $_POST['departamento_id'] !== '' ? $_POST['departamento_id'] : 0;
$municipio_id = isset($_POST['municipio_id']) && $_POST['municipio_id'] !== '' ? $_POST['municipio_id'] : 0;
$pais_id = isset($_POST['pais_id']) && $_POST['pais_id'] !== '' ? $_POST['pais_id'] : 0;

$responsable = $_POST['responsable'];

$responsable_id = isset($_POST['responsable_id']) && $_POST['responsable_id'] !== '' ? $_POST['responsable_id'] : 0;
$referido_id = isset($_POST['referido_id']) && $_POST['referido_id'] !== '' ? $_POST['referido_id'] : 0;

$correo = strtolower(cleanString($_POST['correo']));
$localidad = cleanStringStrtolower($_POST['direccion']);

$update = "UPDATE pacientes 
	SET 
		nombre = '$nombre', 
		apellido = '$apellido', 
		genero = '$sexo', 
		telefono1 = '$telefono1',
		telefono2 = '$telefono2',\t\t
		email = '$correo', 
		fecha_nacimiento = '$fecha_nacimiento',
		pais_id = '$pais_id',\t\t
		departamento_id = '$departamento_id',
		municipio_id = '$municipio_id',
		responsable = '$responsable',
		responsable_id = '$responsable_id',\t\t
		localidad = '$localidad',
		referido_id = '$referido_id'
	WHERE pacientes_id = '$pacientes_id'";
$query = $mysqli->query($update);

if ($query) {
	$datos = array(
		0 => 'Editado',
		1 => 'Registro Editado Correctamente',
		2 => 'success',
		3 => 'btn-primary',
		4 => '',
		5 => 'Editar',
		6 => 'formPacientes',
		7 => 'modal_pacientes',
	);
} else {
	$datos = array(
		0 => 'Error',
		1 => 'No se puedo almacenar este registro, los datos son incorrectos por favor corregir',
		2 => 'error',
		3 => 'btn-danger',
		4 => '',
		5 => '',
	);
}

echo json_encode($datos);
