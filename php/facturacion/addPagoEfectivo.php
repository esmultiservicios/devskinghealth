<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$db_main = DBIZZY;
$mysqli = connect_mysqli(); 
$mysqliOtro = connect_mysqli_db($db_main); 

$facturas_id = $_POST['factura_id_efectivo'];
$fecha = date("Y-m-d");
$fecha_registro = date("Y-m-d H:i:s");
$importe = $_POST['monto_efectivo'];
$cambio = $_POST['cambio_efectivo'];
$empresa_id = $_SESSION['empresa_id'];	
$usuario = $_SESSION['colaborador_id'];			
$tipo_pago_id = 1;//EFECTIVO		
$banco_id = 0;//SIN BANCO
$tipo_pago = 1;//1. CONTADO 2. CRÉDITO
$estado = 2;//FACTURA PAGADA
$estado_pago = 1;//ACTIVO
$fecha_registro = date("Y-m-d H:i:s");
$referencia_pago1 = "";
$referencia_pago2 = "";
$referencia_pago3 = "";
$activo = 1;//SECUENCIA DE FACTURACION
$efectivo = $importe;
$tarjeta = 0;	

//CONSULTAMOS EL TIPO DE FACTURA
$query_tipo_factura = "SELECT tipo_factura
FROM facturas
WHERE facturas_id = '$facturas_id'";
$resultTipoFactura = $mysqli->query($query_tipo_factura) or die($mysqli->error);
$consulta2TipoFactura = $resultTipoFactura->fetch_assoc();

$tipo_factura = "";

if($resultTipoFactura->num_rows>0){	
	$tipo_factura = $consulta2TipoFactura['tipo_factura'];	
}

//CONSULTAR DATOS DE LA SECUENCIA DE FACTURACION
$query_secuencia = "SELECT secuencia_facturacion_id, prefijo, siguiente AS 'numero', rango_final, fecha_limite, incremento, relleno
   FROM secuencia_facturacion
   WHERE activo = '$activo' AND empresa_id = '$empresa_id'";
$result = $mysqliOtro->query($query_secuencia) or die($mysqliOtro->error);
$consulta2 = $result->fetch_assoc();

$secuencia_facturacion_id = "";
$prefijo = "";
$numero = "";
$rango_final = "";
$fecha_limite = "";
$incremento = "";
$no_factura = "";

if($result->num_rows>0){
	$secuencia_facturacion_id = $consulta2['secuencia_facturacion_id'];	
	$prefijo = $consulta2['prefijo'];
	$numero = $consulta2['numero'];
	$rango_final = $consulta2['rango_final'];
	$fecha_limite = $consulta2['fecha_limite'];	
	$incremento = $consulta2['incremento'];
	$no_factura = $consulta2['prefijo']."".str_pad($consulta2['numero'], $consulta2['relleno'], "0", STR_PAD_LEFT);		
}

//VERIFICAMOS QUE NO SE HA INGRESADO EL PAGO, SI NO SE HA REALIZADO EL INGRESO, PROCEDEMOS A ALMACENAR EL PAGO
$query_factura = "SELECT pagos_id
	FROM pagos
	WHERE facturas_id = '$facturas_id'";
$result_factura = $mysqli->query($query_factura) or die($mysqli->error);	

//SI NO SE HA INGRESADO ALMACENAOS EL PAGO
if($result_factura->num_rows==0){
	$pagos_id  = correlativo('pagos_id', 'pagos');
	$insert = "INSERT INTO pagos 
		VALUES ('$pagos_id','$facturas_id','$tipo_pago','$fecha','$importe','$efectivo','$cambio','$tarjeta','$usuario','$estado_pago','$empresa_id','$fecha_registro')";
	$query = $mysqli->query($insert);	

	if($query){
		//ACTUALIZAMOS LOS DETALLES DEL PAGO
		$pagos_detalles_id  = correlativo('pagos_detalles_id', 'pagos_detalles');
		$insert = "INSERT INTO pagos_detalles 
			VALUES ('$pagos_detalles_id','$pagos_id','$tipo_pago_id','$banco_id','$importe','$referencia_pago1','$referencia_pago2','$referencia_pago3')";
		$query = $mysqli->query($insert);
	
		if($tipo_factura == 1){
			//ACTUALIZAMOS EL ESTADO DE LA FACTURA
			$update_factura = "UPDATE facturas
				SET
					estado = '$estado',
					number = '$numero'
				WHERE facturas_id = '$facturas_id'";
			$mysqli->query($update_factura) or die($mysqli->error);	

			//CONSULTAMOS EL NUMERO QUE SIGUE DE EN LA SECUENCIA DE FACTURACION
			$numero_secuencia_facturacion = correlativodbMain("siguiente", "secuencia_facturacion", $db_main);
			
			//ACTUALIZAMOS LA SECUENCIA DE FACTURACION AL NUMERO SIGUIENTE		
			$update = "UPDATE secuencia_facturacion 
			SET 
				siguiente = '$numero_secuencia_facturacion' 
			WHERE secuencia_facturacion_id = '$secuencia_facturacion_id'";
			$mysqliOtro->query($update);
		}else{
			//ACTUALIZAMOS EL ESTADO DE LA FACTURA
			$update_factura = "UPDATE facturas
				SET
					estado = '$estado',
				WHERE facturas_id = '$facturas_id'";
			$mysqli->query($update_factura) or die($mysqli->error);				
		}
		
		$datos = array(
			0 => "Guardar", 
			1 => "Pago Realizado Correctamente", 
			2 => "info",
			3 => "btn-primary",
			4 => "formEfectivoBill",
			5 => "Registro",
			6 => "Pagos",//FUNCION DE LA TABLA QUE LLAMAREMOS PARA QUE ACTUALICE (DATATABLE BOOSTRAP)
			7 => "modal_pagos", //Modals Para Cierre Automatico
			8 => $facturas_id, //Modals Para Cierre Automatico
			9 => "Guardar",
		);		
	}else{
		$datos = array(
			0 => "Error", 
			1 => "No se puedo almacenar este registro, los datos son incorrectos por favor corregir", 
			2 => "error",
			3 => "btn-danger",
			4 => "",
			5 => "",			
		);
	}	
}else{
	$datos = array(
		0 => "Error", 
		1 => "Lo sentimos, no se puede almacenar el pago por favor valide si existe un pago para esta factura", 
		2 => "error",
		3 => "btn-danger",
		4 => "",
		5 => "",			
	);
}

echo json_encode($datos);