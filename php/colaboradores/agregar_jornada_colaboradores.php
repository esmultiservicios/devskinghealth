<?php
session_start();   
require_once '../BaseDataAccess.php';

// Crear instancia de BaseDataAccess
$db = new BaseDataAccess();

// Obtener datos del POST
$colaborador_id = $_POST['colaborador_id'];
$jornada_id = $_POST['jornada_id'];
$cantidad_nuevos = $_POST['cantidad_nuevos'];
$cantidad_subsiguientes = $_POST['cantidad_subsiguientes'];
$fecha_registro = date("Y-m-d H:i:s");
$fecha = date("Y-m-d");
$usuario = $_SESSION['colaborador_id'];

// Obtener nombre del colaborador
$query_colaborador = "
	SELECT 
		CONCAT(nombre,' ',apellido) AS colaborador 
	FROM 
		colaboradores 
	WHERE 
		colaborador_id = ?";

$colaboradorParameters = [
	$colaborador_id
];

$colaborador_data = $db->executeScalarQuery($query_colaborador, $colaboradorParameters);
$colaborador_nombre = $colaborador_data[0]['colaborador'] ?? '';

// Obtener nombre de jornada
$query_jornada = "
	SELECT 
		nombre 
	FROM 
		jornada 
	WHERE 
		jornada_id = ?";

$jornadaParameters = [
	$jornada_id
];

$jornada_data = $db->executeScalarQuery($query_jornada, $jornadaParameters);
$jornada_nombre = $jornada_data[0]['nombre'] ?? '';

// Obtener correlativo
$numero = $db->getCorrelativo('id', 'jornada_colaboradores');

// Consultar si el registro existe
$consulta = "
	SELECT 
		id 
	FROM 
		jornada_colaboradores 
	WHERE 
		j_colaborador_id = ? AND 
		colaborador_id = ?";

$consultaParameters = [
	$jornada_id, 
	$colaborador_id
];

$consulta_data = $db->executeScalarQuery($consulta, $consultaParameters);
$servicios_puestos_id = $consulta_data[0]['id'] ?? 0;

// Verificar si el colaborador tiene almacenada la jornada
$query_jornada_check = "
	SELECT 
		id 
	FROM 
		jornada_colaboradores 
	WHERE 
		j_colaborador_id = ? AND 
		colaborador_id = ?";

$jornadaCheckParameters = [
	$jornada_id, 
	$colaborador_id
];

$result_jornada = $db->executeScalarQuery($query_jornada_check, $jornadaCheckParameters);

if (empty($result_jornada)) {
    if ($jornada_id !== "") {
        if (empty($servicios_puestos_id)) {
            // Insertar nuevo registro
            $insert = "
			INSERT INTO jornada_colaboradores (
				id, 
				j_colaborador_id, 
				colaborador_id, 
				nuevos, 
				subsiguientes
			) 
			VALUES (
				?, 
				?, 
				?, 
				?, 
				?
			)";

            $insertParameters = [
				$numero, 
				$jornada_id, 
				$colaborador_id, 
				$cantidad_nuevos, 
				$cantidad_subsiguientes
			];

            $db->executeNonQuery($insert, $insertParameters);

            // Insertar en historial
			$historial_numero  = $db->getCorrelativo('historial_id', 'historial');

            $estado_historial = "Agregar";
            $observacion_historial = "Se ha agregado al colaborador $colaborador_nombre en la jornada de la $jornada_nombre, con un total de $cantidad_nuevos nuevos, y un total de $cantidad_subsiguientes subsiguientes";
            $modulo = "Servicio Puesto Colaboradores";

            $insert_historial = "
			INSERT INTO historial (
				historial_id , 
				pacientes_id, 
				expediente, 
				modulo, 
				codigo, 
				colaborador_id, 
				servicio_id, 
				fecha, 
				status, 
				observacion, 
				usuario, 
				fecha_registro
			) 
			VALUES (
				?, 
				'0', 
				'0', 
				?, 
				?, 
				'0', 
				'0', 
				?, 
				?, 
				?, 
				?, 
				?
			)";
            
			$historialParameters = [
				$historial_numero, 
				$modulo, 
				$numero, 
				$fecha, 
				$estado_historial, 
				$observacion_historial,
				$usuario, 
				$fecha_registro
			];

            $db->executeNonQuery($insert_historial, $historialParameters);

            $datos = [
                0 => "Almacenado", 
                1 => "Registro Almacenado Correctamente", 
                2 => "success",
                3 => "btn-primary",
                4 => "formulario_servicios_colaboradores",
                5 => "Registro",
                6 => "servicioColaboradores", // FUNCION DE LA TABLA QUE LLAMAREMOS PARA QUE ACTUALICE (DATATABLE BOOSTRAP)
                7 => "registrar_servicios_colaboradores", // Modals Para Cierre Automático
            ];
        } else {
            $datos = [
                0 => "Error", 
                1 => "Lo sentimos, este registro ya existe y no se puede almacenar", 
                2 => "error",
                3 => "btn-danger",
                4 => "",
                5 => "",        
            ];
        }
    } else {
        $datos = [
            0 => "Error", 
            1 => "Hay registros en blanco, por favor corrígelos", 
            2 => "error",
            3 => "btn-danger",
            4 => "",
            5 => "",        
        ];
    }
} else {
    $datos = [
        0 => "Error", 
        1 => "Esta jornada ya ha sido asignada, por favor corrígelos", 
        2 => "error",
        3 => "btn-danger",
        4 => "",
        5 => "",        
    ];
}

echo json_encode($datos);