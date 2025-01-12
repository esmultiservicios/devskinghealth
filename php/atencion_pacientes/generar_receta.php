<?php
session_start();
include "../funtions.php";  // Incluye funciones y conexión

header("Content-Type: text/html;charset=utf-8");

require_once '../../dompdf/vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

//CONEXION A DB
$mysqli = connect_mysqli();  // Usas la conexión definida en funtions.php
$db_main = DB_MAIN;

// Configurar Dompdf
$options = new Options();
$options->set('margin-bottom', 0);
$options->set('margin-left', 0);
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);

$receta_id = $_GET['receta_id'];

$mysqli->set_charset('utf8');

$stmt = $mysqli->prepare("
    SELECT CONCAT(IFNULL(p.nombre, ''), ' ', IFNULL(p.apellido, '')) AS paciente, 
           r.fecha, 
           CONCAT(IFNULL(d.nombre, ''), ' ', IFNULL(d.apellido, '')) AS doctor, 
           rd.productos_id, 
           rd.cantidad, 
           rd.descripcion, 
           prod.nombre AS producto_nombre, 
           e.rtn AS empresa_rtn, 
           e.nombre, 
           e.ubicacion AS empresa_ubicacion
    FROM recetas r
    INNER JOIN pacientes p ON r.pacientes_id = p.pacientes_id
    INNER JOIN colaboradores d ON r.colaborador_id = d.colaborador_id
    INNER JOIN receta_detalles rd ON r.receta_id = rd.receta_id
    INNER JOIN {$db_main}.productos prod ON rd.productos_id = prod.productos_id
    INNER JOIN empresa e ON r.empresa_id = e.empresa_id
    WHERE r.receta_id = ?");

$stmt->bind_param('i', $receta_id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_all(MYSQLI_ASSOC);

if (!$data) {
    die("No se encontraron datos para el paciente con ID $paciente_id.");
}

// Preparar datos para la plantilla
$paciente = $data[0]['paciente'] ?? 'N/A';
$fecha = $data[0]['fecha'] ?? date('Y-m-d');
$doctor = $data[0]['doctor'] ?? 'N/A';
$empresa_rtn = $data[0]['empresa_rtn'] ?? 'N/A';
$empresa = $data[0]['nombre'] ?? 'N/A';
$empresa_ubicacion = $data[0]['empresa_ubicacion'] ?? 'N/A';

$productos = array_map(function ($row) {
    return [
        'nombre' => $row['producto_nombre'],
        'cantidad' => $row['cantidad'],
        'descripcion' => $row['descripcion']
    ];
}, $data);

// Incluir la plantilla HTML
ob_start();
include 'plantilla_receta.php';
$html = ob_get_clean();

// Generar el PDF
$dompdf->loadHtml($html);
//$dompdf->setPaper(array(0, 0, 612, 396), 'portrait'); // Media Carta: Ancho igual, alto dividido entre 2
//$dompdf->setPaper(array(0, 0, 612, 396), 'portrait'); // Media Carta
$dompdf->setPaper(array(0, 0, 612, 396), 'portrait'); // Tamaño carta en orientación vertical

/*
Explicación:
1. array(0, 0, 612, 792):
   El primer par (0, 0) indica el punto de inicio (esquina superior izquierda).
   612 es el ancho (8.5 pulgadas).
   792 es el alto (11 pulgadas).
2. 'portrait':
Define la orientación vertical.

*/

$dompdf->render();

// Descargar o mostrar el PDF
$dompdf->stream("Receta_$paciente.pdf", ["Attachment" => false]);