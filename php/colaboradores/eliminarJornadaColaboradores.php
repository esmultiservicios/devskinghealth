<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli(); 

$id = $_POST['id'];
$fecha_registro = date("Y-m-d H:i:s");
$fecha = date("Y-m-d");
$usuario = $_SESSION['colaborador_id'];

//CONSULTAR LA ENTIDAD jornada_colaboradores
$query_servicio_puestos = "SELECT j_colaborador_id, colaborador_id FROM jornada_colaboradores WHERE id = '$id'";

$result = $mysqli->query($query_servicio_puestos);
if ($result) {
    $cosulta_servicio_puestos = $result->fetch_assoc();
    if ($cosulta_servicio_puestos) {
        $j_colaborador_id = $cosulta_servicio_puestos['j_colaborador_id'];
        $colaborador_id = $cosulta_servicio_puestos['colaborador_id'];     
    } else {
        die("No se encontró el registro en jornada_colaboradores.");
    }
} else {
    die("Error en la consulta: {$mysqli->error}");
}

//OBTENER NOMBRE DE COLABORADOR
$query_colaborador = "SELECT CONCAT(nombre,' ',apellido) AS 'colaborador' FROM colaboradores WHERE colaborador_id = '$colaborador_id'";
$result = $mysqli->query($query_colaborador);
if ($result) {
    $cosulta_colaborador = $result->fetch_assoc();
    if ($cosulta_colaborador) {
        $colaborador_nombre = $cosulta_colaborador['colaborador'];  
    } else {
        die("No se encontró el colaborador.");
    }
} else {
    die("Error en la consulta: {$mysqli->error}");
}

//OBTENER NOMBRE DE JORNADA
$query_jornada = "SELECT nombre FROM jornada WHERE jornada_id = '$j_colaborador_id'";
$result = $mysqli->query($query_jornada);
if ($result) {
    $consulta_jornada = $result->fetch_assoc();
    if ($consulta_jornada) {
        $jornada_nombre = $consulta_jornada['nombre'];  
    } else {
        die("No se encontró la jornada.");
    }
} else {
    die("Error en la consulta: {$mysqli->error}");
}

//ELIMINAMOS EL REGISTRO
$delete = "DELETE FROM jornada_colaboradores WHERE id = '$id'"; 
if ($mysqli->query($delete)) {
    // INGRESAR REGISTROS EN LA ENTIDAD HISTORIAL
    $historial_numero = historial();
    $estado_historial = "Eliminar";
    $observacion_historial = "Se ha eliminado la jornada $jornada_nombre la cual había sido asignada al colaborador $colaborador_nombre";
    $modulo = "Servicio Puesto Colaboradores";
    $insert = "INSERT INTO historial 
        VALUES('$historial_numero','0','0','$modulo','$id','0','0','$fecha','$estado_historial','$observacion_historial','$usuario','$fecha_registro')";  
    if ($mysqli->query($insert)) {
        echo 1; // Registro eliminado y registrado en historial correctamente
    } else {
        die("Error al insertar en historial: {$mysqli->error}");
    }
} else {
    echo 2; // Error al eliminar el registro
}

$mysqli->close(); // CERRAR CONEXIÓN