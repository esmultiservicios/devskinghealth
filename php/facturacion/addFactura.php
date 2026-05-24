<?php
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

function respuesta_json($datos)
{
    echo json_encode($datos);
    exit;
}

function valor_decimal($valor)
{
    $valor = trim((string)$valor);
    $valor = str_replace(",", ".", $valor);

    if ($valor === "" || !is_numeric($valor)) {
        return 0;
    }

    return (float)$valor;
}

function valor_entero($valor)
{
    if ($valor === "" || $valor === null) {
        return 0;
    }

    return (int)$valor;
}

try {
    $pacientes_id = isset($_POST['pacientes_id']) ? valor_entero($_POST['pacientes_id']) : 0;
    $facturas_id = isset($_POST['facturas_id']) ? trim($_POST['facturas_id']) : "";
    $fecha = date("Y-m-d");
    $colaborador_id = isset($_POST['colaborador_id']) ? valor_entero($_POST['colaborador_id']) : 0;
    $servicio_id = isset($_POST['servicio_id']) ? valor_entero($_POST['servicio_id']) : 0;
    $notes = isset($_POST['notes']) ? cleanStringStrtolower($_POST['notes']) : "";
    $notes = mb_substr($notes, 0, 250, "UTF-8");

    $usuario = isset($_SESSION['colaborador_id']) ? valor_entero($_SESSION['colaborador_id']) : 0;
    $empresa_id = isset($_SESSION['empresa_id']) ? valor_entero($_SESSION['empresa_id']) : 0;
    $fecha_registro = date("Y-m-d H:i:s");

    $activo = 1;
    $estado = 4; // ESTADO FACTURA CREDITO
    $cierre = 2;
    $importe = 0;
    $tipo_factura = 1; // CONTADO
    $estado_factura = 1; // BORRADOR
    $numero = 0; // NUMERO DE FACTURA AUN NO GENERADO
    $proforma_id = 0;

    if ($pacientes_id <= 0 || $colaborador_id <= 0 || $servicio_id <= 0) {
        $datos = array(
            0 => "Error",
            1 => "Lo sentimos, el Paciente, Profesional o Servicio no pueden quedar en blanco, por favor corregir",
            2 => "error",
            3 => "btn-danger",
            4 => "",
            5 => ""
        );

        respuesta_json($datos);
    }

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
        throw new Exception("No se pudo preparar la consulta de secuencia de facturación: " . $mysqliOtro->error);
    }

    $stmt->bind_param("ii", $activo, $empresa_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $prefijo = "";
        $numero_secuencia = 0;
        $rango_final = 0;
        $fecha_limite = "";
        $incremento = 0;
        $relleno = 0;

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
    }

    $stmt->close();

    if ($secuencia_facturacion_id === "") {
        $datos = array(
            0 => "Error",
            1 => "No existe una secuencia de facturación activa para esta empresa",
            2 => "error",
            3 => "btn-danger",
            4 => "",
            5 => ""
        );

        respuesta_json($datos);
    }

    // VALIDAR DETALLE ANTES DE INSERTAR ENCABEZADO
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
        $total_filas = count($_POST['productName']);

        for ($i = 0; $i < $total_filas; $i++) {
            $productoID = isset($_POST['productoID'][$i]) ? valor_entero($_POST['productoID'][$i]) : 0;
            $productName = isset($_POST['productName'][$i]) ? trim((string)$_POST['productName'][$i]) : "";
            $quantity = isset($_POST['quantity'][$i]) ? valor_entero($_POST['quantity'][$i]) : 0;
            $price = isset($_POST['price'][$i]) ? valor_decimal($_POST['price'][$i]) : 0;
            $discount = isset($_POST['discount'][$i]) ? valor_decimal($_POST['discount'][$i]) : 0;
            $total = isset($_POST['total'][$i]) ? valor_decimal($_POST['total'][$i]) : 0;

            $fila_vacia = ($productoID <= 0 && $productName == "" && $quantity <= 0 && $price <= 0 && $total <= 0);

            if ($fila_vacia) {
                continue;
            }

            if ($productoID <= 0 || $productName == "" || $quantity <= 0 || $price < 0 || $total < 0) {
                $datos = array(
                    0 => "Error",
                    1 => "No se puedo almacenar este registro, hay datos incorrectos en la fila " . ($i + 1) . " del detalle de la factura",
                    2 => "error",
                    3 => "btn-danger",
                    4 => "",
                    5 => ""
                );

                respuesta_json($datos);
            }

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

    if (count($detalle_factura) <= 0) {
        $datos = array(
            0 => "Error",
            1 => "No se puedo almacenar este registro, los datos son incorrectos por favor corregir, verifique si hay registros en blanco antes de enviar los datos de la factura, se le recuerda que el detalle de la factura no puede quedar vacío",
            2 => "error",
            3 => "btn-danger",
            4 => "",
            5 => ""
        );

        respuesta_json($datos);
    }

    // INSERTAMOS LOS DATOS EN LA ENTIDAD FACTURA
    if ($facturas_id == "") {
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

        $secuencia_facturacion_id = valor_entero($secuencia_facturacion_id);

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
            $estado_factura,
            $cierre,
            $usuario,
            $empresa_id,
            $fecha_registro,
            $proforma_id
        );

        if (!$stmt->execute()) {
            throw new Exception("No se pudo guardar el encabezado de la factura: " . $stmt->error);
        }

        $stmt->close();
    } else {
        $facturas_id = valor_entero($facturas_id);
    }

    $total_valor = 0;
    $descuentos = 0;
    $isv_neto = 0;
    $total_despues_isv = 0;

    // OBTENER EL ISV - RESPETANDO TU TABLA CAMI: isv.nombre
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

    $porcentajeISV = valor_decimal($porcentajeISV);

    // ALMACENAMOS EL DETALLE DE LA FACTURA
    foreach ($detalle_factura as $item) {
        $productoID = $item["productoID"];
        $productName = $item["productName"];
        $quantity = $item["quantity"];
        $price = $item["price"];
        $discount = $item["discount"];
        $total = $item["total"];
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
        } else {
            throw new Exception("El producto " . $productName . " no existe");
        }

        $stmt->close();

        if ($aplica_isv == 1) {
            $porcentaje_isv = ($porcentajeISV / 100);
            $isv_valor = $price * $quantity * $porcentaje_isv;
        }

        // VALIDAMOS SI EL PRODUCTO EXISTE EN EL DETALLE
        $facturas_detalle_id_existente = 0;

        $query_consulta_detalle_productos = "SELECT facturas_detalle_id
            FROM facturas_detalle
            WHERE productos_id = ? AND facturas_id = ?
            LIMIT 1";

        $stmt = $mysqli->prepare($query_consulta_detalle_productos);

        if (!$stmt) {
            throw new Exception("No se pudo preparar la consulta del detalle: " . $mysqli->error);
        }

        $stmt->bind_param("ii", $productoID, $facturas_id);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($facturas_detalle_id_existente);
            $stmt->fetch();
        }

        $stmt->close();

        if ($facturas_detalle_id_existente <= 0) {
            $facturas_detalle_id = correlativo("facturas_detalle_id", "facturas_detalle");

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
        } else {
            $update_detalle = "UPDATE facturas_detalle
                SET
                    cantidad = ?,
                    precio = ?,
                    isv_valor = ?,
                    descuento = ?
                WHERE productos_id = ? AND facturas_id = ?";

            $stmt = $mysqli->prepare($update_detalle);

            if (!$stmt) {
                throw new Exception("No se pudo preparar la actualización del detalle: " . $mysqli->error);
            }

            $stmt->bind_param(
                "idddii",
                $quantity,
                $price,
                $isv_valor,
                $discount,
                $productoID,
                $facturas_id
            );

            if (!$stmt->execute()) {
                throw new Exception("No se pudo actualizar el detalle de la factura: " . $stmt->error);
            }

            $stmt->close();
        }

        // CONSULTAMOS LA CATEGORIA DEL PRODUCTO
        $categoria_producto = "";

        $query_categoria = "SELECT cp.nombre AS categoria
            FROM productos AS p
            INNER JOIN categoria_producto AS cp
                ON p.categoria_producto_id = cp.categoria_producto_id
            WHERE p.productos_id = ?
            GROUP BY p.productos_id
            LIMIT 1";

        $stmt = $mysqli->prepare($query_categoria);

        if (!$stmt) {
            throw new Exception("No se pudo preparar la consulta de categoría: " . $mysqli->error);
        }

        $stmt->bind_param("i", $productoID);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($categoria_producto);
            $stmt->fetch();
        }

        $stmt->close();

        if ($categoria_producto == "Producto") {
            // CONSULTAMOS LA CANTIDAD EN LA ENTIDAD PRODUCTOS
            $cantidad_productos = 0;

            $query_productos = "SELECT cantidad
                FROM productos
                WHERE productos_id = ?
                LIMIT 1";

            $stmt = $mysqli->prepare($query_productos);

            if (!$stmt) {
                throw new Exception("No se pudo preparar la consulta de cantidad del producto: " . $mysqli->error);
            }

            $stmt->bind_param("i", $productoID);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $stmt->bind_result($cantidad_productos);
                $stmt->fetch();
            }

            $stmt->close();

            $cantidad = $cantidad_productos - $quantity;

            // ACTUALIZAMOS LA NUEVA CANTIDAD EN LA ENTIDAD PRODUCTOS
            $update_productos = "UPDATE productos
                SET cantidad = ?
                WHERE productos_id = ?";

            $stmt = $mysqli->prepare($update_productos);

            if (!$stmt) {
                throw new Exception("No se pudo preparar la actualización del producto: " . $mysqli->error);
            }

            $stmt->bind_param("di", $cantidad, $productoID);

            if (!$stmt->execute()) {
                throw new Exception("No se pudo actualizar la cantidad del producto: " . $stmt->error);
            }

            $stmt->close();

            // CONSULTAMOS EL SALDO DEL PRODUCTO EN LA ENTIDAD MOVIMIENTOS
            $saldo_productos = 0;

            $query_movimientos = "SELECT saldo
                FROM movimientos
                WHERE productos_id = ?
                ORDER BY movimientos_id DESC
                LIMIT 1";

            $stmt = $mysqli->prepare($query_movimientos);

            if (!$stmt) {
                throw new Exception("No se pudo preparar la consulta de saldo: " . $mysqli->error);
            }

            $stmt->bind_param("i", $productoID);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $stmt->bind_result($saldo_productos);
                $stmt->fetch();
            }

            $stmt->close();

            $saldo = $saldo_productos - $quantity;

            $cantidad_entrada = 0;
            $cantidad_salida = $quantity;
            $documento = "Factura " . $pacientes_id;

            $movimientos_id = correlativo("movimientos_id", "movimientos");
            $comentario_movimientos = "Salida por Facturación";

            $insert_movimiento = "INSERT INTO movimientos
                (
                    movimientos_id,
                    productos_id,
                    documento,
                    cantidad_entrada,
                    cantidad_salida,
                    saldo,
                    fecha_registro,
                    comentario
                )
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $mysqli->prepare($insert_movimiento);

            if (!$stmt) {
                throw new Exception("No se pudo preparar el movimiento: " . $mysqli->error);
            }

            $stmt->bind_param(
                "iisiiiss",
                $movimientos_id,
                $productoID,
                $documento,
                $cantidad_entrada,
                $cantidad_salida,
                $saldo,
                $fecha_registro,
                $comentario_movimientos
            );

            if (!$stmt->execute()) {
                throw new Exception("No se pudo guardar el movimiento: " . $stmt->error);
            }

            $stmt->close();
        }

        $total_valor += ($price * $quantity);
        $descuentos += $discount;
        $isv_neto += $isv_valor;
    }

    $total_despues_isv = ($total_valor + $isv_neto) - $descuentos;

    // ACTUALIZAMOS EL IMPORTE DE LA FACTURA
    $update = "UPDATE facturas
        SET
            importe = ?,
            fecha = ?,
            usuario = ?
        WHERE facturas_id = ?";

    $stmt = $mysqli->prepare($update);

    if (!$stmt) {
        throw new Exception("No se pudo preparar la actualización de la factura: " . $mysqli->error);
    }

    $stmt->bind_param(
        "dsii",
        $total_despues_isv,
        $fecha,
        $usuario,
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
        6 => "Facturacion",
        7 => "",
        8 => $facturas_id
    );

    respuesta_json($datos);

} catch (Exception $e) {
    $datos = array(
        0 => "Error",
        1 => "No se pudo almacenar la factura. " . $e->getMessage(),
        2 => "error",
        3 => "btn-danger",
        4 => "",
        5 => ""
    );

    respuesta_json($datos);
}