<?php
session_start();

// Incluir la clase BaseDataAccess
require_once '../BaseDataAccess.php';

// Crear una instancia de BaseDataAccess
$db = new BaseDataAccess();

// Verificar si 'servicios' está definido y no está vacío
if (isset($_POST['servicios']) && !empty(trim($_POST['servicios']))) {
    $servicioNombre = trim($_POST['servicios']);
    
    // Preparar la consulta para verificar si el servicio ya existe
    $query = "SELECT servicio_id FROM servicios WHERE nombre = ?";
    $parameters = [
        'nombre' => $servicioNombre
    ];

    $resultado = $db->executeScalarQuery($query, $parameters);

    if (empty($resultado)) {
        // El servicio no existe, por lo tanto, lo registramos
        $insertQuery = "
            INSERT INTO servicios (
                nombre
            ) VALUES (
                ?
            )
        ";

        $insertParameters = [
            'nombre' => $servicioNombre
        ];

        $insertResult = $db->executeNonQuery($insertQuery, $insertParameters);

        if ($insertResult) {
			$datos = [
				0 => "Éxito",  
				1 => "El servicio ha sido registrado exitosamente",
				2 => "success",
				3 => "btn-success",
				4 => "formulario_servicios",
				5 => "Registro",
				6 => "Servicios", //FUNCION DE LA TABLA QUE LLAMAREMOS PARA QUE ACTUALICE (DATATABLE BOOSTRAP)
				7 => "registrar_servicios", //Modals Para Cierre Automatico
			];			
        } else {
            $datos = [
                0 => "Error", 
                1 => "Hubo un problema al intentar registrar el servicio", 
                2 => "error",
                3 => "btn-danger",
                4 => "",
                5 => "",
			];			
            ;
        }
    } else {
        // El servicio ya existe
        $datos = [
            0 => "Error", 
            1 => "Lo sentimos, este registro ya existe y no se puede almacenar", 
            2 => "error",
            3 => "btn-danger",
            4 => "",
            5 => "",
			];			
        ;
    }
} else {
    // El valor 'servicios' no está definido o está vacío
    $datos = [
        0 => "Error", 
        1 => "El nombre del servicio no puede estar vacío", 
        2 => "error",
        3 => "btn-danger",
        4 => "",
        5 => "",
	];
}

// Imprimir el resultado como JSON o manejarlo según sea necesario
echo json_encode($datos);