<?php
session_start();   
include "../funtions.php";

// CONEXIÓN A DB
$mysqli = connect_mysqli();

$numeroAnterior = 0;
$numeroMaximo = 0;
$contador = 0;
$fecha_limite = null; // Inicializar la variable para fecha_limite
$empresa_id = $_SESSION['empresa_id'];

// CONSULTAMOS EL NÚMERO ANTERIOR
$queryNumero = "SELECT siguiente AS 'numero'
    FROM secuencia_facturacion
    WHERE activo = 1 AND empresa_id = '$empresa_id'
    ORDER BY siguiente DESC LIMIT 1";
$resultNumero = $mysqli->query($queryNumero) or die($mysqli->error);

if($resultNumero->num_rows > 0){
    $consultaNumero = $resultNumero->fetch_assoc();
    $numeroAnterior = $consultaNumero['numero'] ?? 0; // Usar el valor por defecto 0 si está vacío
}

// CONSULTAMOS EL NÚMERO MÁXIMO PERMITIDO
$queryNumeroMaximo = "SELECT rango_final AS 'numero'
    FROM secuencia_facturacion
    WHERE activo = 1 AND empresa_id = '$empresa_id'";
$resultNumeroMaximo = $mysqli->query($queryNumeroMaximo) or die($mysqli->error);

if($resultNumeroMaximo->num_rows > 0){
    $consultaNumeroMaximo = $resultNumeroMaximo->fetch_assoc();
    $numeroMaximo = $consultaNumeroMaximo['numero'] ?? 0; // Usar el valor por defecto 0 si está vacío
}

$facturasPendientes = $numeroMaximo - $numeroAnterior;

// OBTENEMOS LA FECHA LÍMITE DE FACTURACIÓN
$querFechaLimite = "SELECT DATEDIFF(fecha_limite, NOW()) AS 'dias_transcurridos', fecha_limite AS 'fecha_limite'
    FROM secuencia_facturacion
    WHERE activo = 1 AND empresa_id = '$empresa_id'";
$resultNFechaLimite = $mysqli->query($querFechaLimite) or die($mysqli->error);

if($resultNFechaLimite->num_rows > 0){
    $consultaFechaLimite = $resultNFechaLimite->fetch_assoc();
    $contador = $consultaFechaLimite['dias_transcurridos'] ?? 0; // Usar el valor por defecto 0 si está vacío
    $fecha_limite = $consultaFechaLimite['fecha_limite'] ?? null; // Si no hay fecha_limite, mantén null
}

// Preparamos los datos a enviar
$datos = array(
    0 => $facturasPendientes,
    1 => $contador,    
    2 => $fecha_limite,        
);

echo json_encode($datos);

$mysqli->close(); // CERRAR CONEXIÓN