<?php
session_start();
include '../funtions.php';

$mysqli = connect_mysqli();
$mysqli->set_charset("utf8");

$colaborador_id = $_SESSION['colaborador_id'] ?? 0;
$type = $_SESSION['type'] ?? '';
$usuario = $_SESSION['colaborador_id'] ?? 0;

$fechai = $_POST['fechai'] ?? '';
$fechaf = $_POST['fechaf'] ?? '';
$clientes = $_POST['clientes'] ?? '';
$profesional = $_POST['profesional'] ?? '';
$estado = $_POST['estado'] ?? 1;
$buscar = trim($_POST['buscar'] ?? '');

if ($estado == 1) {
    $in = 'IN(2,4)';
} else if ($estado == 4) {
    $in = 'IN(4)';
} else {
    $in = 'IN(3)';
}

$where = [];
$where[] = "f.estado $in";

if ($buscar == '') {
    if ($fechai != '' && $fechaf != '') {
        $fechai_sql = $mysqli->real_escape_string($fechai);
        $fechaf_sql = $mysqli->real_escape_string($fechaf);

        $where[] = "f.fecha BETWEEN '$fechai_sql' AND '$fechaf_sql'";
    }
}

if ($clientes != '') {
    $clientes_sql = $mysqli->real_escape_string($clientes);
    $where[] = "f.pacientes_id = '$clientes_sql'";
}

if ($profesional != '') {
    $profesional_sql = $mysqli->real_escape_string($profesional);
    $where[] = "f.colaborador_id = '$profesional_sql'";
}

if ($buscar != '') {
    $buscar_sql = $mysqli->real_escape_string($buscar);

    $where[] = "(
        f.number LIKE '%$buscar_sql%'
        OR CONCAT(sc.prefijo, LPAD(f.number, sc.relleno, '0')) LIKE '%$buscar_sql%'
        OR sc.prefijo LIKE '%$buscar_sql%'
        OR p.identidad LIKE '%$buscar_sql%'
        OR p.nombre LIKE '%$buscar_sql%'
        OR p.apellido LIKE '%$buscar_sql%'
        OR CONCAT(p.nombre, ' ', p.apellido) LIKE '%$buscar_sql%'
        OR s.nombre LIKE '%$buscar_sql%'
        OR c.nombre LIKE '%$buscar_sql%'
        OR c.apellido LIKE '%$buscar_sql%'
        OR CONCAT(c.nombre, ' ', c.apellido) LIKE '%$buscar_sql%'
        OR DATE_FORMAT(f.fecha, '%d/%m/%Y') LIKE '%$buscar_sql%'
        OR CASE WHEN f.tipo_factura = 1 THEN 'Contado' ELSE 'Crédito' END LIKE '%$buscar_sql%'
    )";
}

$where_sql = implode(" AND ", $where);

$consulta = "
SELECT 
    f.facturas_id AS facturas_id,
    f.fecha AS fecha,
    DATE_FORMAT(f.fecha, '%d/%m/%Y') AS fecha1,
    p.identidad AS identidad,
    CONCAT(p.nombre, ' ', p.apellido) AS paciente,
    sc.prefijo AS prefijo,
    f.number AS numero,
    sc.relleno AS relleno,
    s.nombre AS servicio,
    CONCAT(c.nombre, ' ', c.apellido) AS profesional,
    f.pacientes_id AS pacientes_id,
    f.cierre AS cierre,
    f.tipo_factura AS tipo_factura,
    CASE 
        WHEN f.tipo_factura = 1 THEN 'Contado' 
        ELSE 'Crédito' 
    END AS tipo_documento,

    COALESCE((
        SELECT SUM(fd.precio * fd.cantidad)
        FROM facturas_detalle AS fd
        WHERE fd.facturas_id = f.facturas_id
    ), 0) AS total_precio,

    COALESCE((
        SELECT SUM(fd.cantidad)
        FROM facturas_detalle AS fd
        WHERE fd.facturas_id = f.facturas_id
    ), 0) AS cantidad,

    COALESCE((
        SELECT SUM(fd.descuento)
        FROM facturas_detalle AS fd
        WHERE fd.facturas_id = f.facturas_id
    ), 0) AS descuento,

    COALESCE((
        SELECT SUM(fd.isv_valor)
        FROM facturas_detalle AS fd
        WHERE fd.facturas_id = f.facturas_id
    ), 0) AS isv_neto,

    COALESCE((
        SELECT SUM((fd.precio * fd.cantidad) + fd.isv_valor - fd.descuento)
        FROM facturas_detalle AS fd
        WHERE fd.facturas_id = f.facturas_id
    ), 0) AS total,

    COALESCE((
        SELECT SUM(fd.precio * fd.cantidad)
        FROM facturas_detalle AS fd
        WHERE fd.facturas_id = f.facturas_id
    ), 0) AS precio

FROM facturas AS f
INNER JOIN pacientes AS p 
    ON f.pacientes_id = p.pacientes_id
INNER JOIN esmultiservicios_skincenter_izzy.secuencia_facturacion AS sc 
    ON f.secuencia_facturacion_id = sc.secuencia_facturacion_id
INNER JOIN servicios AS s 
    ON f.servicio_id = s.servicio_id
INNER JOIN colaboradores AS c 
    ON f.colaborador_id = c.colaborador_id
WHERE $where_sql
ORDER BY f.number DESC
";

$result = $mysqli->query($consulta) or die($mysqli->error);

$arreglo = array('data' => array());

while ($data = $result->fetch_assoc()) {
    $numero = $data['numero'] == 0 
        ? 'Aún no se ha generado' 
        : $data['prefijo'] . rellenarDigitos($data['numero'], $data['relleno']);

    $data['factura'] = $numero;

    if ($data['tipo_factura'] == 1) {
        $data['tipo_documento'] = 'Contado';
    } else {
        $data['tipo_documento'] = 'Crédito';
    }

    if ($estado == 1) {
        $data['estado'] = 'Borrador';
    } else if ($estado == 2) {
        $data['estado'] = 'Pagada';
    } else if ($estado == 3) {
        $data['estado'] = 'Cancelada';
    } else if ($estado == 4) {
        $data['estado'] = 'Crédito';
    } else {
        $data['estado'] = '';
    }

    $data['precio'] = number_format((float)$data['precio'], 2, '.', '');
    $data['isv_neto'] = number_format((float)$data['isv_neto'], 2, '.', '');
    $data['descuento'] = number_format((float)$data['descuento'], 2, '.', '');
    $data['total'] = number_format((float)$data['total'], 2, '.', '');

    $arreglo['data'][] = $data;
}

echo json_encode($arreglo);

$result->free();
$mysqli->close();