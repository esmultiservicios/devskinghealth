<?php
session_start();
include '../funtions.php';

//CONEXION A DB
$db_main = DBIZZY;
$mysqli = connect_mysqli(); 
$mysqliOtro = connect_mysqli_db($db_main); 

$colaborador_id = $_SESSION['colaborador_id'];
$type = $_SESSION['type'];
$fechai = $_POST['fechai'];
$fechaf = $_POST['fechaf'];
$usuario = $_SESSION['colaborador_id'];
$empresa_id = $_SESSION['empresa_id'];
$estado = $_POST['estado'];

if ($estado == 1) {
    $in = 'IN(2,4)';
} else if ($estado == 4) {
    $in = 'IN(4)';
} else {
    $in = 'IN(3)';
}

$consulta = "
SELECT 
    f.facturas_id AS 'facturas_id', 
    f.fecha AS 'fecha', 
    p.identidad AS 'identidad', 
    CONCAT(p.nombre, ' ', p.apellido) AS 'paciente', 
    sc.prefijo AS 'prefijo', 
    f.number AS 'numero', 
    s.nombre AS 'servicio', 
    CONCAT(c.nombre, ' ', c.apellido) AS 'profesional', 
    sc.relleno AS 'relleno', 
    DATE_FORMAT(f.fecha, '%d/%m/%Y') AS 'fecha1', 
    f.pacientes_id AS 'pacientes_id', 
    f.cierre AS 'cierre', 
    (CASE WHEN f.tipo_factura = 1 THEN 'Contado' ELSE 'Crédito' END) AS 'tipo_documento', 
    f.tipo_factura,
    (
        SELECT SUM(fd.precio * fd.cantidad) 
        FROM facturas_detalle AS fd
        WHERE fd.facturas_id = f.facturas_id
    ) AS 'total_precio',
    (
        SELECT SUM(fd.cantidad) 
        FROM facturas_detalle AS fd
        WHERE fd.facturas_id = f.facturas_id
    ) AS 'cantidad',
    (
        SELECT SUM(fd.descuento) 
        FROM facturas_detalle AS fd
        WHERE fd.facturas_id = f.facturas_id
    ) AS 'descuento',
    (
        SELECT SUM(fd.isv_valor) 
        FROM facturas_detalle AS fd
        WHERE fd.facturas_id = f.facturas_id
    ) AS 'isv_neto',
    (
        SELECT SUM(fd.precio * fd.cantidad) + SUM(fd.isv_valor) - SUM(fd.descuento)
        FROM facturas_detalle AS fd
        WHERE fd.facturas_id = f.facturas_id
    ) AS 'total',
    (
        SELECT SUM(fd.precio)
        FROM facturas_detalle AS fd
        WHERE fd.facturas_id = f.facturas_id
    ) AS 'precio',
    'CAMII' AS 'origen'
FROM esmultiservicios_skincenter_cami.facturas AS f
INNER JOIN esmultiservicios_skincenter_cami.pacientes AS p ON f.pacientes_id = p.pacientes_id
INNER JOIN esmultiservicios_skincenter_izzy.secuencia_facturacion AS sc ON f.secuencia_facturacion_id = sc.secuencia_facturacion_id
INNER JOIN esmultiservicios_skincenter_cami.servicios AS s ON f.servicio_id = s.servicio_id
INNER JOIN esmultiservicios_skincenter_cami.colaboradores AS c ON f.colaborador_id = c.colaborador_id
WHERE f.fecha BETWEEN '$fechai' AND '$fechaf' AND f.estado $in AND f.empresa_id = $empresa_id

UNION ALL

SELECT 
    f.facturas_id AS 'facturas_id', 
    f.fecha AS 'fecha', 
    c.rtn AS 'identidad', 
    c.nombre AS 'paciente',
    sf.prefijo AS 'prefijo', 
    f.number AS 'numero', 
    '' AS 'servicio', 
    CONCAT(co.nombre, ' ', co.apellido) AS 'profesional', 
    sf.relleno AS 'relleno', 
    DATE_FORMAT(f.fecha, '%d/%m/%Y') AS 'fecha1', 
    '' AS 'pacientes_id', 
    '' AS 'cierre', 
    CASE 
        WHEN f.tipo_factura = 1 THEN 'Contado' 
        ELSE 'Crédito' 
    END AS 'tipo_documento', 
    f.tipo_factura,
    (
        SELECT SUM(fd.cantidad * fd.precio) 
        FROM esmultiservicios_skincenter_izzy.facturas_detalles AS fd
        WHERE fd.facturas_id = f.facturas_id
    ) AS 'total_precio',
    (
        SELECT SUM(fd.cantidad) 
        FROM esmultiservicios_skincenter_izzy.facturas_detalles AS fd
        WHERE fd.facturas_id = f.facturas_id
    ) AS 'cantidad',
    (
        SELECT SUM(fd.descuento) 
        FROM esmultiservicios_skincenter_izzy.facturas_detalles AS fd
        WHERE fd.facturas_id = f.facturas_id
    ) AS 'descuento',
    (
        SELECT SUM(fd.isv_valor) 
        FROM esmultiservicios_skincenter_izzy.facturas_detalles AS fd
        WHERE fd.facturas_id = f.facturas_id
    ) AS 'isv_neto',
    (
        SELECT SUM(fd.cantidad * fd.precio) + SUM(fd.isv_valor) - SUM(fd.descuento)
        FROM esmultiservicios_skincenter_izzy.facturas_detalles AS fd
        WHERE fd.facturas_id = f.facturas_id
    ) AS 'total',
    (
        SELECT SUM(fd.precio)
        FROM esmultiservicios_skincenter_izzy.facturas_detalles AS fd
        WHERE fd.facturas_id = f.facturas_id
    ) AS 'precio',
    'IZZY' AS 'origen'
FROM esmultiservicios_skincenter_izzy.facturas AS f
INNER JOIN esmultiservicios_skincenter_izzy.clientes AS c ON f.clientes_id = c.clientes_id
INNER JOIN esmultiservicios_skincenter_izzy.colaboradores AS co ON f.colaboradores_id = co.colaboradores_id
INNER JOIN esmultiservicios_skincenter_izzy.colaboradores AS co1 ON f.usuario = co1.colaboradores_id
INNER JOIN esmultiservicios_skincenter_izzy.secuencia_facturacion AS sf ON f.secuencia_facturacion_id = sf.secuencia_facturacion_id
INNER JOIN esmultiservicios_skincenter_izzy.documento AS d ON sf.documento_id = d.documento_id
WHERE f.fecha BETWEEN '$fechai' AND '$fechaf' AND f.estado $in AND f.empresa_id = $empresa_id AND sf.documento_id = 1

ORDER BY numero DESC";


$result = $mysqli->query($consulta) or die($mysqli->error);

$arreglo = array('data' => []);

while ($data = $result->fetch_assoc()) {
    $facturas_id = $data['facturas_id'];

    $numero = $data['numero'] == 0 ? 'Aún no se ha generado' : $data['prefijo'] . rellenarDigitos($data['numero'], $data['relleno']);
    $data['factura'] = $numero;

    $estado_ = match ($estado) {
        1 => 'Borrador',
        2 => 'Pagada',
        3 => 'Cancelada',
        4 => 'Crédito',
        default => ''
    };

    $data['estado'] = $estado_;

    $arreglo['data'][] = $data;
}

// Devolver tanto las facturas como los totales por tipo de pago
echo json_encode([
    'data' => $arreglo['data']
]);

$result->free();

$mysqli->close();
