<?php
//addPrePfactura.php
session_start();
include "../funtions.php";

header('Content-Type: application/json; charset=utf-8');

// CONEXION A DB - RESPETADA IGUAL QUE TU ORIGINAL
$db_main = DBIZZY;
$mysqli = connect_mysqli();
$mysqliOtro = connect_mysqli_db($db_main);

if (!$mysqli || !$mysqliOtro) {
    echo json_encode(array(
        0 => "Error",
        1 => "No se pudo conectar con la base de datos",
        2 => "error",
        3 => "btn-danger",
        4 => "",
        5 => ""
    ));
    exit;
}

$mysqli->set_charset("utf8mb4");
$mysqliOtro->set_charset("utf8mb4");

function responder_json($datos)
{
    echo json_encode($datos);
    exit;
}

function limpiar_decimal($valor)
{
    $valor = trim((string)$valor);
    $valor = str_replace(",", ".", $valor);

    if ($valor === "" || !is_numeric($valor)) {
        return 0;
    }

    return (float)$valor;
}

function limpiar_entero($valor)
{
    if ($valor === "" || $valor === null) {
        return 0;
    }

    return (int)$valor;
}

try {
    $pacientes_id = isset($_POST['pacientes_id']) ? limpiar_entero($_POST['pacientes_id']) : 0;
    $fecha = isset($_POST['fecha']) && trim($_POST['fecha']) != "" ? trim($_POST['fecha']) : date("Y-m-d");
    $colaborador_id = isset($_POST['colaborador_id']) ? limpiar_entero($_POST['colaborador_id']) : 0;
    $servicio_id = isset($_POST['servicio_id']) ? limpiar_entero($_POST['servicio_id']) : 0;
    $notes = isset($_POST['notes']) ? cleanStringStrtolower($_POST['notes']) : "";
    $notes = mb_substr($notes, 0, 250, "UTF-8");

    $usuario = isset($_SESSION['colaborador_id']) ? limpiar_entero($_SESSION['colaborador_id']) : 0;
    $fecha_registro = date("Y-m-d H:i:s");
    $activo = 1;
    $estado = 1;
    $numero = 0;
    $empresa_id = isset($_SESSION['empresa_id']) ? limpiar_entero($_SESSION['empresa_id']) : 0;

    // CONSULTAR DATOS DE LA SECUENCIA DE FACTURACION - USA $mysqliOtro COMO TU ORIGINAL
    $secuencia_facturacion_id = "";

    $query_secuencia = "SELECT 
                            secuencia_facturacion_id, 
                            prefijo, 
                            siguiente AS numero, 
                            rango_final, 
                            fecha_limite, 
                            incremento, 
                            relleno
                        FROM secuencia_facturacion
                        WHERE activo = ? AND empresa_id = ?
                        LIMIT 1";

    $stmt = $mysqliOtro->prepare($query_secuencia);

    if (!$stmt) {
        throw new Exception("No se pudo preparar la consulta de secuencia: " . $mysqliOtro->error);
    }

    $stmt->bind_param("ii", $activo, $empresa_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $prefijo = "";
        $numero_secuencia = 0;
        $rango_final = "";
        $fecha_limite = "";
        $incremento = "";
        $relleno = "";

        $stmt->bind_result(
            $secuencia_facturacion_id,
            $prefijo,
            $numero_secuencia,
            $rango_final,
            $fecha_limite,
            $incremento,
            $relleno
        );

        $stmt->fetch();
        $secuencia_facturacion_id = limpiar_entero($secuencia_facturacion_id);
    }

    $stmt->close();

    $cierre = 2;
    $importe = 0;
    $tipo_factura = 1; // CONTADO
    $proforma_id = 0;

    // OBTENEMOS Y VALIDAMOS EL DETALLE DE LA FACTURA
    $detalle_factura = array();

    if (
        isset($_POST['productName']) &&
        isset($_POST['productoID']) &&
        isset($_POST['quantity']) &&
        isset($_POST['price']) &&
        isset($_POST['discount']) &&
        isset($_POST['total']) &&
        is_array($_POST['productName']) &&
        is_array($_POST['productoID']) &&
        is_array($_POST['quantity']) &&
        is_array($_POST['price']) &&
        is_array($_POST['discount']) &&
        is_array($_POST['total'])
    ) {
        $tamano_tabla = count($_POST['productName']);

        for ($i = 0; $i < $tamano_tabla; $i++) {
            $productoID = isset($_POST['productoID'][$i]) ? limpiar_entero($_POST['productoID'][$i]) : 0;
            $productName = isset($_POST['productName'][$i]) ? trim((string)$_POST['productName'][$i]) : "";
            $quantity = isset($_POST['quantity'][$i]) ? limpiar_entero($_POST['quantity'][$i]) : 0;
            $price = isset($_POST['price'][$i]) ? limpiar_decimal($_POST['price'][$i]) : 0;
            $discount_raw = isset($_POST['discount'][$i]) ? trim((string)$_POST['discount'][$i]) : "";
            $discount = limpiar_decimal($discount_raw);
            $total = isset($_POST['total'][$i]) ? limpiar_decimal($_POST['total'][$i]) : 0;

            // Respeta tu lógica original: solo procesa filas completas
            if (
                $productoID != 0 &&
                $productName != "" &&
                $quantity != 0 &&
                $price != 0 &&
                $discount_raw !== "" &&
                $total != 0
            ) {
                $detalle_factura[] = array(
                    "productoID" => $productoID,
                    "productName" => $productName,
                    "quantity" => $quantity,
                    "price" => $price,
                    "discount" => $discount,
                    "total" => $total
                );
            }
        }
    }

    if ($pacientes_id != 0 && $colaborador_id != 0 && $servicio_id != 0) {
        if (count($detalle_factura) > 0) {

            // INSERTAMOS LOS DATOS EN LA ENTIDAD FACTURA
            $facturas_id = correlativo("facturas_id", "facturas");

            $insert = "INSERT INTO facturas
                       (
                            facturas_id,
                            secuencia_facturacion_id,
                            number,
                            tipo_factura,
                            pacientes_id,
                            colaborador_id,
                            servicio_id,
                            importe,
                            notas,
                            fecha,
                            estado,
                            cierre,
                            usuario,
                            empresa_id,
                            fecha_registro,
                            proforma_id
                       )
                       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $mysqli->prepare($insert);

            if (!$stmt) {
                throw new Exception("No se pudo preparar el encabezado de la factura: " . $mysqli->error);
            }

            $stmt->bind_param(
                "iiiiiiidssiiiisi",
                $facturas_id,
                $secuencia_facturacion_id,
                $numero,
                $tipo_factura,
                $pacientes_id,
                $colaborador_id,
                $servicio_id,
                $importe,
                $notes,
                $fecha,
                $estado,
                $cierre,
                $usuario,
                $empresa_id,
                $fecha_registro,
                $proforma_id
            );

            $query = $stmt->execute();

            if (!$query) {
                throw new Exception("No se pudo guardar el encabezado de la factura: " . $stmt->error);
            }

            $stmt->close();

            // ALMACENAMOS EL DETALLE DE LA FACTURA
            $total_valor = 0;
            $descuentos = 0;
            $isv_neto = 0;
            $total_despues_isv = 0;

            // OBTENER EL ISV UNA SOLA VEZ - RESPETA TU TABLA isv.nombre
            $porcentajeISV = 0;

            $query_isv = "SELECT nombre FROM isv LIMIT 1";
            $stmt = $mysqli->prepare($query_isv);

            if (!$stmt) {
                throw new Exception("No se pudo preparar la consulta del ISV: " . $mysqli->error);
            }

            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $stmt->bind_result($porcentajeISV);
                $stmt->fetch();
            }

            $stmt->close();

            $porcentajeISV = limpiar_decimal($porcentajeISV);

            foreach ($detalle_factura as $item) {
                $facturas_detalle_id = correlativo("facturas_detalle_id", "facturas_detalle");

                $productoID = $item['productoID'];
                $productName = $item['productName'];
                $quantity = $item['quantity'];
                $price = $item['price'];
                $discount = $item['discount'];
                $total = $item['total'];
                $isv_valor = 0;

                // CONSULTAMOS EL ISV ACTIVO EN EL PRODUCTO
                $aplica_isv = 0;

                $query_isv_activo = "SELECT isv
                                     FROM productos
                                     WHERE productos_id = ?
                                     LIMIT 1";

                $stmt = $mysqli->prepare($query_isv_activo);

                if (!$stmt) {
                    throw new Exception("No se pudo preparar la consulta del ISV del producto: " . $mysqli->error);
                }

                $stmt->bind_param("i", $productoID);
                $stmt->execute();
                $stmt->store_result();

                if ($stmt->num_rows > 0) {
                    $stmt->bind_result($aplica_isv);
                    $stmt->fetch();
                }

                $stmt->close();

                if ($aplica_isv == 1) {
                    $porcentaje_isv = ($porcentajeISV / 100);
                    $isv_valor = $price * $quantity * $porcentaje_isv;
                }

                $insert_detalle = "INSERT INTO facturas_detalle
                                   (
                                        facturas_detalle_id,
                                        facturas_id,
                                        productos_id,
                                        cantidad,
                                        precio,
                                        isv_valor,
                                        descuento
                                   )
                                   VALUES (?, ?, ?, ?, ?, ?, ?)";

                $stmt = $mysqli->prepare($insert_detalle);

                if (!$stmt) {
                    throw new Exception("No se pudo preparar el detalle de la factura: " . $mysqli->error);
                }

                $stmt->bind_param(
                    "iiiiddd",
                    $facturas_detalle_id,
                    $facturas_id,
                    $productoID,
                    $quantity,
                    $price,
                    $isv_valor,
                    $discount
                );

                if (!$stmt->execute()) {
                    throw new Exception("No se pudo guardar el detalle de la factura: " . $stmt->error);
                }

                $stmt->close();

                $total_valor += ($price * $quantity);
                $descuentos += $discount;
                $isv_neto += $isv_valor;
            }

            $total_despues_isv = ($total_valor + $isv_neto) - $descuentos;

            // ACTUALIZAMOS EL IMPORTE DE LA FACTURA
            $update = "UPDATE facturas
                       SET importe = ?
                       WHERE facturas_id = ?";

            $stmt = $mysqli->prepare($update);

            if (!$stmt) {
                throw new Exception("No se pudo preparar la actualización del importe: " . $mysqli->error);
            }

            $stmt->bind_param(
                "di",
                $total_despues_isv,
                $facturas_id
            );

            if (!$stmt->execute()) {
                throw new Exception("No se pudo actualizar el importe de la factura: " . $stmt->error);
            }

            $stmt->close();

            $datos = array(
                0 => "Almacenado",
                1 => "Registro Almacenado Correctamente",
                2 => "success",
                3 => "btn-primary",
                4 => "formulario_facturacion",
                5 => "Registro",
                6 => "FacturaAtenciones",
                7 => ""
            );

            responder_json($datos);

        } else {
            $datos = array(
                0 => "Error",
                1 => "No se puedo almacenar este registro, los datos son incorrectos por favor corregir, verifique si hay registros en blanco antes de enviar los datos de la factura, se le recuerda que el detalle de la factura no puede quedar vacío",
                2 => "error",
                3 => "btn-danger",
                4 => "",
                5 => ""
            );

            responder_json($datos);
        }
    } else {
        $datos = array(
            0 => "Error",
            1 => "Lo sentimos, el Paciente, Profesional o Servicio no pueden quedar en blanco, por favor corregir",
            2 => "error",
            3 => "btn-danger",
            4 => "",
            5 => ""
        );

        responder_json($datos);
    }

} catch (Exception $e) {
    $datos = array(
        0 => "Error",
        1 => "No se pudo almacenar este registro, " . $e->getMessage(),
        2 => "error",
        3 => "btn-danger",
        4 => "",
        5 => ""
    );

    responder_json($datos);
}