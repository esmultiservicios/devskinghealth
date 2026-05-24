<?php
// paginar.php
session_start();
include "../funtions.php";

header('Content-Type: application/json; charset=utf-8');

$db_main = DBIZZY;
$mysqli = connect_mysqli();
$mysqliOtro = connect_mysqli_db($db_main);

if (!$mysqli || !$mysqliOtro) {
    echo json_encode(array(
        0 => '<div class="alert alert-danger mb-0"><i class="fas fa-exclamation-triangle mr-1"></i> No se pudo conectar con la base de datos.</div>',
        1 => ''
    ));
    exit;
}

$mysqli->set_charset("utf8mb4");
$mysqliOtro->set_charset("utf8mb4");

function limpiar_entero($valor)
{
    if ($valor === "" || $valor === null) {
        return 0;
    }

    return (int)$valor;
}

function limpiar_texto($valor)
{
    if ($valor === null) {
        return "";
    }

    return trim((string)$valor);
}

function h($valor)
{
    return htmlspecialchars($valor ?? '', ENT_QUOTES, 'UTF-8');
}

function ejecutar_stmt($mysqli, $sql, $types = "", $params = array())
{
    $stmt = $mysqli->prepare($sql);

    if (!$stmt) {
        throw new Exception("Error al preparar consulta: " . $mysqli->error);
    }

    if ($types != "" && count($params) > 0) {
        $bind_names = array();
        $bind_names[] = $types;

        for ($i = 0; $i < count($params); $i++) {
            $bind_names[] = &$params[$i];
        }

        call_user_func_array(array($stmt, 'bind_param'), $bind_names);
    }

    if (!$stmt->execute()) {
        throw new Exception("Error al ejecutar consulta: " . $stmt->error);
    }

    return $stmt;
}

function obtener_secuencia_facturacion($mysqliOtro, $secuencia_facturacion_id, &$cache_secuencia)
{
    $secuencia_facturacion_id = (int)$secuencia_facturacion_id;

    if (isset($cache_secuencia[$secuencia_facturacion_id])) {
        return $cache_secuencia[$secuencia_facturacion_id];
    }

    $datos = array(
        "prefijo" => "",
        "relleno" => 0
    );

    if ($secuencia_facturacion_id <= 0) {
        $cache_secuencia[$secuencia_facturacion_id] = $datos;
        return $datos;
    }

    $sql = "SELECT prefijo, relleno
            FROM secuencia_facturacion
            WHERE secuencia_facturacion_id = ?
            LIMIT 1";

    $stmt = ejecutar_stmt($mysqliOtro, $sql, "i", array($secuencia_facturacion_id));
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $prefijo = "";
        $relleno = 0;

        $stmt->bind_result($prefijo, $relleno);
        $stmt->fetch();

        $datos = array(
            "prefijo" => $prefijo,
            "relleno" => $relleno
        );
    }

    $stmt->close();

    $cache_secuencia[$secuencia_facturacion_id] = $datos;

    return $datos;
}

function obtener_totales_factura($mysqli, $facturas_id)
{
    $facturas_id = (int)$facturas_id;

    $totales = array(
        "precio" => 0,
        "descuento" => 0,
        "isv_neto" => 0,
        "total" => 0
    );

    $sql = "SELECT cantidad, precio, descuento, isv_valor
            FROM facturas_detalle
            WHERE facturas_id = ?";

    $stmt = ejecutar_stmt($mysqli, $sql, "i", array($facturas_id));
    $result = $stmt->get_result();

    $importe = 0;
    $descuento = 0;
    $isv_neto = 0;

    while ($registrodetalles = $result->fetch_assoc()) {
        $cantidad = (float)$registrodetalles["cantidad"];
        $precio_unitario = (float)$registrodetalles["precio"];

        $importe += ($precio_unitario * $cantidad);
        $descuento += (float)$registrodetalles["descuento"];
        $isv_neto += (float)$registrodetalles["isv_valor"];
    }

    $stmt->close();

    $total = ($importe + $isv_neto) - $descuento;

    $totales["precio"] = $importe;
    $totales["descuento"] = $descuento;
    $totales["isv_neto"] = $isv_neto;
    $totales["total"] = $total;

    return $totales;
}

function badge_estado_factura($estado_fila)
{
    $estado_fila = (int)$estado_fila;

    if ($estado_fila == 1) {
        return '<span class="badge px-3 py-2" style="font-size:13px; border-radius:20px; color:#8a5a00; background:#fff8e5; border:1px solid #f0ad4e;">
                    <i class="fas fa-edit mr-1"></i> Borrador
                </span>';
    }

    if ($estado_fila == 2) {
        return '<span class="badge px-3 py-2" style="font-size:13px; border-radius:20px; color:#0b7a32; background:#ecfff4; border:1px solid #b9ebcc;">
                    <i class="fas fa-check-circle mr-1"></i> Pagada
                </span>';
    }

    if ($estado_fila == 3) {
        return '<span class="badge px-3 py-2" style="font-size:13px; border-radius:20px; color:#b30021; background:#fff1f3; border:1px solid #f1b7c0;">
                    <i class="fas fa-ban mr-1"></i> Cancelada
                </span>';
    }

    if ($estado_fila == 4) {
        return '<span class="badge px-3 py-2" style="font-size:13px; border-radius:20px; color:#005f73; background:#e9fbff; border:1px solid #9de2ef;">
                    <i class="fas fa-hand-holding-usd mr-1"></i> Crédito
                </span>';
    }

    return '<span class="badge badge-light px-3 py-2" style="font-size:13px; border-radius:20px;">Sin estado</span>';
}

function badge_factura($numero)
{
    if ($numero == "Aún no se ha generado") {
        return '<span class="badge px-3 py-2" style="font-size:13px; border-radius:20px; color:#777; background:#f1f1f1;">
                    <i class="fas fa-clock mr-1"></i> Aún no se ha generado
                </span>';
    }

    return '<span class="badge px-3 py-2" style="font-size:13px; border-radius:8px; color:#005f8f; background:#f5fbff; border:1px solid #b8dfff;">
                <i class="fas fa-file-invoice mr-1"></i> ' . h($numero) . '
            </span>';
}

try {
    $colaborador_id = isset($_SESSION['colaborador_id']) ? limpiar_entero($_SESSION['colaborador_id']) : 0;
    $type = isset($_SESSION['type']) ? limpiar_entero($_SESSION['type']) : 0;

    $paginaActual = isset($_POST['partida']) ? limpiar_entero($_POST['partida']) : 1;
    $fechai = isset($_POST['fechai']) ? limpiar_texto($_POST['fechai']) : "";
    $fechaf = isset($_POST['fechaf']) ? limpiar_texto($_POST['fechaf']) : "";
    $dato = isset($_POST['dato']) ? limpiar_texto($_POST['dato']) : "";
    $clientes = isset($_POST['clientes']) ? limpiar_entero($_POST['clientes']) : 0;
    $profesional = isset($_POST['profesional']) ? limpiar_entero($_POST['profesional']) : 0;
    $estado = isset($_POST['estado']) ? limpiar_entero($_POST['estado']) : 0;

    if ($paginaActual <= 0) {
        $paginaActual = 1;
    }

    $where = "WHERE f.estado = ?";
    $types = "i";
    $params = array($estado);

    if ($fechai != "" && $fechaf != "" && $dato == "") {
        $where .= " AND f.fecha BETWEEN ? AND ?";
        $types .= "ss";
        $params[] = $fechai;
        $params[] = $fechaf;
    }

    if ($estado == 2 || $estado == 4) {
        if ($clientes != 0) {
            $where .= " AND f.pacientes_id = ? AND f.usuario = ?";
            $types .= "ii";
            $params[] = $clientes;
            $params[] = $colaborador_id;
        }

        if ($profesional != 0) {
            $where .= " AND f.colaborador_id = ?";
            $types .= "i";
            $params[] = $profesional;
        }

        if ($dato != "") {
            $like_dato_general = "%" . $dato . "%";
            $like_dato_inicio = $dato . "%";

            $where .= " AND f.usuario = ? 
                        AND (
                            CONCAT(p.nombre,' ',p.apellido) LIKE ? 
                            OR CONCAT(p.apellido,' ',p.nombre) LIKE ?
                            OR p.nombre LIKE ?
                            OR p.apellido LIKE ? 
                            OR p.identidad LIKE ? 
                            OR f.number LIKE ?
                        )";

            $types .= "issssss";
            $params[] = $colaborador_id;
            $params[] = $like_dato_general;
            $params[] = $like_dato_general;
            $params[] = $like_dato_inicio;
            $params[] = $like_dato_inicio;
            $params[] = $like_dato_inicio;
            $params[] = $like_dato_inicio;
        }
    } else {
        if ($clientes != 0) {
            $where .= " AND f.pacientes_id = ?";
            $types .= "i";
            $params[] = $clientes;
        }

        if ($profesional != 0) {
            $where .= " AND f.colaborador_id = ?";
            $types .= "i";
            $params[] = $profesional;
        }

        if ($dato != "") {
            $like_dato_general = "%" . $dato . "%";
            $like_dato_inicio = $dato . "%";

            $where .= " AND (
                            CONCAT(p.nombre,' ',p.apellido) LIKE ? 
                            OR CONCAT(p.apellido,' ',p.nombre) LIKE ?
                            OR p.nombre LIKE ?
                            OR p.apellido LIKE ? 
                            OR p.identidad LIKE ? 
                            OR f.number LIKE ?
                        )";

            $types .= "ssssss";
            $params[] = $like_dato_general;
            $params[] = $like_dato_general;
            $params[] = $like_dato_inicio;
            $params[] = $like_dato_inicio;
            $params[] = $like_dato_inicio;
            $params[] = $like_dato_inicio;
        }
    }

    $sql_count = "SELECT f.facturas_id
                  FROM facturas AS f
                  INNER JOIN pacientes AS p
                      ON f.pacientes_id = p.pacientes_id
                  INNER JOIN servicios AS s
                      ON f.servicio_id = s.servicio_id
                  INNER JOIN colaboradores AS c
                      ON f.colaborador_id = c.colaborador_id
                  $where";

    $stmt_count = ejecutar_stmt($mysqli, $sql_count, $types, $params);
    $stmt_count->store_result();

    $nroLotes = 10;
    $nroProductos = $stmt_count->num_rows;
    $nroPaginas = ceil($nroProductos / $nroLotes);

    $stmt_count->close();

    $lista = '';
    $tabla = '';

    if ($nroPaginas > 1) {
        $lista .= '<nav aria-label="Paginación de facturas">';
        $lista .= '<ul class="pagination pagination-sm justify-content-center mt-3">';

        if ($paginaActual > 1) {
            $lista .= '<li class="page-item">
                            <a class="page-link" href="javascript:pagination(1);void(0);">
                                <i class="fas fa-angle-double-left"></i> Inicio
                            </a>
                       </li>';

            $lista .= '<li class="page-item">
                            <a class="page-link" href="javascript:pagination(' . ($paginaActual - 1) . ');void(0);">
                                <i class="fas fa-angle-left"></i> Anterior
                            </a>
                       </li>';
        }

        $lista .= '<li class="page-item active">
                        <a class="page-link" href="javascript:void(0);">
                            Página ' . $paginaActual . ' de ' . $nroPaginas . '
                        </a>
                   </li>';

        if ($paginaActual < $nroPaginas) {
            $lista .= '<li class="page-item">
                            <a class="page-link" href="javascript:pagination(' . ($paginaActual + 1) . ');void(0);">
                                Siguiente <i class="fas fa-angle-right"></i>
                            </a>
                       </li>';

            $lista .= '<li class="page-item">
                            <a class="page-link" href="javascript:pagination(' . $nroPaginas . ');void(0);">
                                Última <i class="fas fa-angle-double-right"></i>
                            </a>
                       </li>';
        }

        $lista .= '</ul>';
        $lista .= '</nav>';
    }

    $limit = ($paginaActual <= 1) ? 0 : $nroLotes * ($paginaActual - 1);

    $sql_registro = "SELECT 
                        f.facturas_id AS facturas_id, 
                        DATE_FORMAT(f.fecha, '%d/%m/%Y') AS fecha, 
                        CONCAT(p.nombre,' ',p.apellido) AS paciente, 
                        p.identidad AS identidad, 
                        CONCAT(c.nombre,' ',c.apellido) AS profesional, 
                        f.estado AS estado, 
                        s.nombre AS consultorio, 
                        f.secuencia_facturacion_id AS secuencia_facturacion_id,
                        f.number AS numero
                     FROM facturas AS f
                     INNER JOIN pacientes AS p
                        ON f.pacientes_id = p.pacientes_id
                     INNER JOIN servicios AS s
                        ON f.servicio_id = s.servicio_id
                     INNER JOIN colaboradores AS c
                        ON f.colaborador_id = c.colaborador_id
                     $where
                     ORDER BY f.number DESC, f.facturas_id DESC
                     LIMIT ?, ?";

    $types_registro = $types . "ii";
    $params_registro = $params;
    $params_registro[] = $limit;
    $params_registro[] = $nroLotes;

    $stmt_registro = ejecutar_stmt($mysqli, $sql_registro, $types_registro, $params_registro);
    $result = $stmt_registro->get_result();

    $tabla .= '
    <div class="table-responsive">
        <table class="table table-striped table-hover mb-0" style="font-size:13px;">
            <thead>
                <tr style="background:#1297a5; color:#fff;">
                    <th class="text-center align-middle py-3" width="4%">No.</th>
                    <th class="text-center align-middle py-3" width="7%">Fecha</th>
                    <th class="text-center align-middle py-3" width="13%">Factura</th>
                    <th class="align-middle py-3" width="16%">Cliente</th>
                    <th class="text-center align-middle py-3" width="10%">Identidad</th>
                    <th class="align-middle py-3" width="12%">Profesional</th>
                    <th class="align-middle py-3" width="9%">Servicio</th>
                    <th class="text-right align-middle py-3" width="7%">Importe</th>
                    <th class="text-right align-middle py-3" width="6%">ISV</th>
                    <th class="text-right align-middle py-3" width="7%">Descuento</th>
                    <th class="text-right align-middle py-3" width="7%">Neto</th>
                    <th class="text-center align-middle py-3" width="8%">Estado</th>
                    <th class="text-center align-middle py-3" width="9%">Acciones</th>
                </tr>
            </thead>
            <tbody>
    ';

    $i = $limit + 1;
    $cache_secuencia = array();

    while ($registro2 = $result->fetch_assoc()) {
        $facturas_id = (int)$registro2['facturas_id'];

        $totales = obtener_totales_factura($mysqli, $facturas_id);

        $precio = $totales["precio"];
        $descuento = $totales["descuento"];
        $isv_neto = $totales["isv_neto"];
        $total = $totales["total"];

        $secuencia = obtener_secuencia_facturacion(
            $mysqliOtro,
            $registro2['secuencia_facturacion_id'],
            $cache_secuencia
        );

        if ((int)$registro2['numero'] == 0) {
            $numero = "Aún no se ha generado";
        } else {
            $numero = $secuencia['prefijo'] . rellenarDigitos($registro2['numero'], $secuencia['relleno']);
        }

        $estado_fila = (int)$registro2['estado'];
        $estado_badge = badge_estado_factura($estado_fila);

        $opciones = '';

        if ($estado_fila == 1) {
            $opciones .= '<a class="dropdown-item" href="javascript:pay(' . $facturas_id . ');void(0);">
                            <i class="fas fa-file-invoice text-primary mr-2"></i> Generar factura
                          </a>';

            $opciones .= '<div class="dropdown-divider"></div>';

            $opciones .= '<a class="dropdown-item text-danger" href="javascript:deleteBill(' . $facturas_id . ');void(0);">
                            <i class="fas fa-trash-alt mr-2"></i> Eliminar factura
                          </a>';
        }

        if ($estado_fila == 2) {
            $opciones .= '<a class="dropdown-item" href="javascript:printBill(' . $facturas_id . ');void(0);">
                            <i class="fas fa-print text-secondary mr-2"></i> Imprimir factura
                          </a>';

            $opciones .= '<a class="dropdown-item" href="javascript:mailBill(' . $facturas_id . ');void(0);">
                            <i class="far fa-paper-plane text-info mr-2"></i> Enviar factura
                          </a>';
        }

        if ($estado_fila == 3) {
            $opciones .= '<a class="dropdown-item" href="javascript:printBill(' . $facturas_id . ');void(0);">
                            <i class="fas fa-print text-secondary mr-2"></i> Imprimir factura
                          </a>';
        }

        if ($estado_fila == 4) {
            $opciones .= '<a class="dropdown-item" href="javascript:pago(' . $facturas_id . ');void(0);">
                            <i class="fas fa-money-check-alt text-success mr-2"></i> Pagar crédito
                          </a>';

            $opciones .= '<a class="dropdown-item" href="javascript:printBill(' . $facturas_id . ');void(0);">
                            <i class="fas fa-print text-secondary mr-2"></i> Imprimir factura
                          </a>';
        }

        if ($opciones == '') {
            $opciones = '<span class="dropdown-item text-muted"><i class="fas fa-info-circle mr-2"></i> Sin acciones disponibles</span>';
        }

        $acciones = '
            <div class="btn-group">
                <button class="btn btn-primary dropdown-toggle shadow-sm" 
                        type="button" 
                        id="dropdownFactura' . $facturas_id . '" 
                        data-toggle="dropdown" 
                        aria-haspopup="true" 
                        aria-expanded="false"
                        style="border-radius:6px; padding:7px 14px; font-size:13px;">
                    <i class="fas fa-cog mr-1"></i> Acciones
                </button>

                <div class="dropdown-menu dropdown-menu-right shadow border-0" 
                     aria-labelledby="dropdownFactura' . $facturas_id . '" 
                     style="border-radius:8px;">
                    ' . $opciones . '
                </div>
            </div>
        ';

        $tabla .= '
            <tr style="height:56px;">
                <td class="text-center align-middle font-weight-bold py-3">' . $i . '</td>

                <td class="text-center align-middle py-3">
                    <span class="badge badge-light border px-3 py-2" style="font-size:13px; border-radius:8px;">
                        <i class="fas fa-calendar-alt text-primary mr-1"></i> ' . h($registro2['fecha']) . '
                    </span>
                </td>

                <td class="text-center align-middle py-3">
                    ' . badge_factura($numero) . '
                </td>

                <td class="align-middle py-3">
                    <div style="line-height:1.35;">
                        <div class="font-weight-bold text-dark">
                            <i class="fas fa-user text-info mr-1"></i> ' . h($registro2['paciente']) . '
                        </div>
                    </div>
                </td>

                <td class="text-center align-middle py-3">
                    <span class="badge badge-light border px-3 py-2" style="font-size:13px; border-radius:8px;">
                        <i class="fas fa-id-card text-muted mr-1"></i> ' . h($registro2['identidad']) . '
                    </span>
                </td>

                <td class="align-middle py-3">
                    <span class="font-weight-bold text-dark">
                        <i class="fas fa-user-md text-primary mr-1"></i> ' . h($registro2['profesional']) . '
                    </span>
                </td>

                <td class="align-middle py-3">
                    <span class="text-dark">' . h($registro2['consultorio']) . '</span>
                </td>

                <td class="text-right align-middle py-3">
                    <span class="font-weight-bold text-dark">L ' . number_format($precio, 2) . '</span>
                </td>

                <td class="text-right align-middle py-3">
                    <span class="font-weight-bold text-info">L ' . number_format($isv_neto, 2) . '</span>
                </td>

                <td class="text-right align-middle py-3">
                    <span class="font-weight-bold text-danger">L ' . number_format($descuento, 2) . '</span>
                </td>

                <td class="text-right align-middle py-3">
                    <span class="badge px-3 py-2" style="font-size:13px; border-radius:8px; color:#0b7a32; background:#ecfff4; border:1px solid #86d99b;">
                        L ' . number_format($total, 2) . '
                    </span>
                </td>

                <td class="text-center align-middle py-3">
                    ' . $estado_badge . '
                </td>

                <td class="text-center align-middle py-3">
                    ' . $acciones . '
                </td>
            </tr>
        ';

        $i++;
    }

    $stmt_registro->close();

    if ($nroProductos == 0) {
        $tabla .= '
            <tr>
                <td colspan="13" class="text-center py-5">
                    <div class="text-danger font-weight-bold" style="font-size:15px;">
                        <i class="fas fa-search mr-2"></i> No se encontraron resultados
                    </div>
                    <div class="text-muted mt-1">
                        Intente buscar por paciente, identidad o número de factura.
                    </div>
                </td>
            </tr>
        ';
    } else {
        $tabla .= '
            <tr>
                <td colspan="13" class="text-center py-4">
                    <span class="badge badge-light border px-4 py-2" style="font-size:14px; border-radius:20px;">
                        <i class="fas fa-file-invoice text-info mr-1"></i>
                        Total de registros encontrados:
                        <strong>' . number_format($nroProductos) . '</strong>
                    </span>
                </td>
            </tr>
        ';
    }

    $tabla .= '
            </tbody>
        </table>
    </div>';

    $array = array(
        0 => $tabla,
        1 => $lista
    );

    echo json_encode($array);

    $mysqli->close();
    $mysqliOtro->close();

} catch (Exception $e) {
    $tabla = '
    <div class="alert alert-danger mb-0">
        <i class="fas fa-exclamation-triangle mr-1"></i>
        Error: ' . h($e->getMessage()) . '
    </div>';

    echo json_encode(array(
        0 => $tabla,
        1 => ''
    ));
}