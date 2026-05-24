<?php
session_start();   
include "../funtions.php";

header('Content-Type: application/json; charset=utf-8');

// CONEXION A DB
$mysqli = connect_mysqli(); 	
$mysqli->set_charset("utf8mb4");

function h($valor)
{
	return htmlspecialchars($valor ?? '', ENT_QUOTES, 'UTF-8');
}

$paginaActual = isset($_POST['partida']) ? (int)$_POST['partida'] : 1;
$fechai = isset($_POST['fechai']) ? $_POST['fechai'] : '';
$fechaf = isset($_POST['fechaf']) ? $_POST['fechaf'] : '';
$dato = isset($_POST['dato']) ? trim($_POST['dato']) : '';
$unidad = isset($_POST['unidad']) ? $_POST['unidad'] : '';
$colaborador = isset($_POST['colaborador']) ? $_POST['colaborador'] : '';
$fecha_registro = date('Y-m-d');

if ($paginaActual <= 0) {
	$paginaActual = 1;
}

$fechai = $mysqli->real_escape_string($fechai);
$fechaf = $mysqli->real_escape_string($fechaf);
$dato = $mysqli->real_escape_string($dato);
$unidad = $mysqli->real_escape_string($unidad);
$colaborador = $mysqli->real_escape_string($colaborador);

if ($colaborador != "") {
	$where = "WHERE CAST(ag.fecha_cita AS DATE) BETWEEN '$fechai' AND '$fechaf' AND ag.preclinica = 0 AND ag.colaborador_id = '$colaborador'";
} else if ($dato != "") {
	$where = "WHERE ag.preclinica = 0 AND (
		p.expediente LIKE '%$dato%' 
		OR CONCAT(p.nombre,' ',p.apellido) LIKE '%$dato%' 
		OR CONCAT(p.apellido,' ',p.nombre) LIKE '%$dato%' 
		OR p.apellido LIKE '$dato%' 
		OR p.nombre LIKE '$dato%' 
		OR p.identidad LIKE '$dato%'
	)";
} else {
	$where = "WHERE CAST(ag.fecha_cita AS DATE) BETWEEN '$fechai' AND '$fechaf' AND ag.preclinica = 0";
}

$query = "SELECT 
		ag.pacientes_id AS pacientes_id, 
		ag.agenda_id AS agenda_id, 
		p.expediente AS expediente, 
		p.identidad AS identidad, 
		CONCAT(p.apellido,' ',p.nombre) AS paciente, 
		DATE_FORMAT(CAST(ag.fecha_cita AS DATE), '%d/%m/%Y') AS fecha_cita, 
		ag.hora AS hora, 
		CONCAT(c.nombre,' ',c.apellido) AS colaborador, 
		s.nombre AS servicio, 
		ag.observacion AS observacion, 
		ag.comentario AS comentario, 
		CAST(ag.fecha_cita AS DATE) AS fecha, 
		pc.puesto_id AS puesto_id, 
		ag.servicio_id AS servicio_id, 
		CAST(ag.fecha_cita AS DATE) AS fecha_cita1
	FROM agenda AS ag
	INNER JOIN pacientes AS p
		ON ag.pacientes_id = p.pacientes_id
	INNER JOIN colaboradores AS c
		ON ag.colaborador_id = c.colaborador_id
	INNER JOIN servicios AS s
		ON ag.servicio_id = s.servicio_id
	INNER JOIN puesto_colaboradores AS pc
		ON c.puesto_id = pc.puesto_id		
	$where
	ORDER BY fecha_cita1, ag.hora";

$result = $mysqli->query($query) or die($mysqli->error);

$nroProductos = $result->num_rows;
$nroLotes = 25;
$nroPaginas = ceil($nroProductos / $nroLotes);

$lista = '';
$tabla = '';

if ($nroPaginas > 1) {
	$lista .= '<nav aria-label="Paginación de citas">';
	$lista .= '<ul class="pagination pagination-sm justify-content-center mt-3">';

	if ($paginaActual > 1) {
		$lista .= '
		<li class="page-item">
			<a class="page-link" href="javascript:pagination(1);void(0);">
				<i class="fas fa-angle-double-left"></i> Inicio
			</a>
		</li>';

		$lista .= '
		<li class="page-item">
			<a class="page-link" href="javascript:pagination('.($paginaActual - 1).');void(0);">
				<i class="fas fa-angle-left"></i> Anterior
			</a>
		</li>';
	}

	$lista .= '
	<li class="page-item active">
		<a class="page-link" href="javascript:void(0);">
			Página '.$paginaActual.' de '.$nroPaginas.'
		</a>
	</li>';

	if ($paginaActual < $nroPaginas) {
		$lista .= '
		<li class="page-item">
			<a class="page-link" href="javascript:pagination('.($paginaActual + 1).');void(0);">
				Siguiente <i class="fas fa-angle-right"></i>
			</a>
		</li>';

		$lista .= '
		<li class="page-item">
			<a class="page-link" href="javascript:pagination('.$nroPaginas.');void(0);">
				Última <i class="fas fa-angle-double-right"></i>
			</a>
		</li>';
	}

	$lista .= '</ul>';
	$lista .= '</nav>';
}

if ($paginaActual <= 1) {
	$limit = 0;
} else {
	$limit = $nroLotes * ($paginaActual - 1);
}

$registro = "SELECT 
		ag.pacientes_id AS pacientes_id, 
		ag.agenda_id AS agenda_id, 
		p.expediente AS expediente, 
		p.identidad AS identidad, 
		CONCAT(p.apellido,' ',p.nombre) AS paciente, 
		DATE_FORMAT(CAST(ag.fecha_cita AS DATE), '%d/%m/%Y') AS fecha_cita, 
		ag.hora AS hora, 
		CONCAT(c.nombre,' ',c.apellido) AS colaborador, 
		s.nombre AS servicio, 
		ag.observacion AS observacion, 
		ag.comentario AS comentario, 
		CAST(ag.fecha_cita AS DATE) AS fecha, 
		pc.puesto_id AS puesto_id, 
		ag.servicio_id AS servicio_id, 
		CAST(ag.fecha_cita AS DATE) AS fecha_cita1
	FROM agenda AS ag
	INNER JOIN pacientes AS p
		ON ag.pacientes_id = p.pacientes_id
	INNER JOIN colaboradores AS c
		ON ag.colaborador_id = c.colaborador_id
	INNER JOIN servicios AS s
		ON ag.servicio_id = s.servicio_id
	INNER JOIN puesto_colaboradores AS pc
		ON c.puesto_id = pc.puesto_id	
	$where
	ORDER BY fecha_cita1, ag.hora ASC 
	LIMIT $limit, $nroLotes";

$result = $mysqli->query($registro) or die($mysqli->error);

$tabla .= '
<div class="table-responsive">
	<table class="table table-striped table-hover mb-0" style="font-size:13px;">
		<thead>
			<tr style="background:#1297a5; color:#fff;">
				<th class="text-center align-middle py-3" width="4%">No.</th>
				<th class="text-center align-middle py-3" width="8%">Expediente</th>
				<th class="text-center align-middle py-3" width="10%">Identidad</th>
				<th class="align-middle py-3" width="15%">Paciente</th>
				<th class="text-center align-middle py-3" width="8%">Fecha Cita</th>
				<th class="text-center align-middle py-3" width="7%">Hora</th>
				<th class="align-middle py-3" width="13%">Profesional</th>				
				<th class="align-middle py-3" width="10%">Servicio</th>
				<th class="align-middle py-3" width="10%">Observación</th>
				<th class="align-middle py-3" width="10%">Comentario</th>				
				<th class="text-center align-middle py-3" width="9%">Registrar</th>
				<th class="text-center align-middle py-3" width="9%">Ausencias</th>
			</tr>
		</thead>
		<tbody>
';

$i = $limit + 1;

while ($registro2 = $result->fetch_assoc()) {
	if ($registro2['expediente'] == 0) {
		$expediente = "TEMP";
	} else {
		$expediente = $registro2['expediente'];
	}

	$agenda_id = (int)$registro2['agenda_id'];
	$pacientes_id = (int)$registro2['pacientes_id'];
	$expediente_js = (int)$registro2['expediente'];

	$identidad = trim($registro2['identidad']);
	$observacion = trim($registro2['observacion']);
	$comentario = trim($registro2['comentario']);

	if ($identidad == '') {
		$identidad_html = '<span class="badge badge-light px-3 py-2" style="border-radius:20px; color:#777; background:#f1f1f1;">Sin identidad</span>';
	} else {
		$identidad_html = '
		<span class="badge badge-light border px-3 py-2" style="font-size:13px; border-radius:8px;">
			<i class="fas fa-id-card text-muted mr-1"></i> '.h($identidad).'
		</span>';
	}

	if ($observacion == '') {
		$observacion_html = '<span class="badge badge-light px-3 py-2" style="border-radius:20px; color:#777; background:#f1f1f1;">Sin observación</span>';
	} else {
		$observacion_html = '
		<span class="text-dark">
			<i class="fas fa-notes-medical text-primary mr-1"></i> '.h($observacion).'
		</span>';
	}

	if ($comentario == '') {
		$comentario_html = '<span class="badge badge-light px-3 py-2" style="border-radius:20px; color:#777; background:#f1f1f1;">Sin comentario</span>';
	} else {
		$comentario_html = '
		<span class="text-dark">
			<i class="fas fa-comment-medical text-secondary mr-1"></i> '.h($comentario).'
		</span>';
	}

	$tabla .= '
		<tr style="height:58px;">
			<td class="text-center align-middle font-weight-bold py-3">'.$i.'</td>

			<td class="text-center align-middle py-3">
				<span class="badge px-3 py-2" style="font-size:13px; border-radius:8px; color:#005f8f; background:#f5fbff; border:1px solid #b8dfff;">
					<i class="fas fa-folder-open mr-1"></i> '.h($expediente).'
				</span>
			</td>

			<td class="text-center align-middle py-3">
				'.$identidad_html.'
			</td>

			<td class="align-middle py-3">
				<div style="line-height:1.35;">
					<div class="font-weight-bold text-dark">
						<i class="fas fa-user text-info mr-1"></i> '.h($registro2['paciente']).'
					</div>
				</div>
			</td>

			<td class="text-center align-middle py-3">
				<span class="badge badge-light border px-3 py-2" style="font-size:13px; border-radius:8px;">
					<i class="fas fa-calendar-alt text-primary mr-1"></i> '.h($registro2['fecha_cita']).'
				</span>
			</td>

			<td class="text-center align-middle py-3">
				<span class="badge px-3 py-2" style="font-size:13px; border-radius:20px; color:#8a5a00; background:#fff8e5; border:1px solid #f0ad4e;">
					<i class="fas fa-clock mr-1"></i> '.h($registro2['hora']).'
				</span>
			</td>

			<td class="align-middle py-3">
				<span class="font-weight-bold text-dark">
					<i class="fas fa-user-md text-primary mr-1"></i> '.h($registro2['colaborador']).'
				</span>
			</td>

			<td class="align-middle py-3">
				<span class="text-dark">
					<i class="fas fa-clinic-medical text-info mr-1"></i> '.h($registro2['servicio']).'
				</span>
			</td>

			<td class="align-middle py-3">
				'.$observacion_html.'
			</td>

			<td class="align-middle py-3">
				'.$comentario_html.'
			</td>

			<td class="text-center align-middle py-3">
				<a class="btn btn-primary shadow-sm d-inline-flex align-items-center justify-content-center" 
				   style="border-radius:6px; padding:8px 14px; font-size:13px; min-width:115px; white-space:nowrap;"
				   href="javascript:editarRegistro('.$agenda_id.','.$expediente_js.');void(0);" 
				   title="Agregar Preclínica">
					<i class="fas fa-notes-medical mr-2"></i> Preclínica
				</a>
			</td>

			<td class="text-center align-middle py-3">
				<a class="btn btn-danger shadow-sm d-inline-flex align-items-center justify-content-center" 
				   style="border-radius:6px; padding:8px 14px; font-size:13px; min-width:110px; white-space:nowrap;"
				   title="Usuario no se presentó a su cita" 
				   href="javascript:nosePresentoRegistro('.$agenda_id.','.$pacientes_id.');void(0);">
					<i class="fas fa-times-circle mr-2"></i> Ausencia
				</a>
			</td>
		</tr>
	';

	$i++;
}

if ($nroProductos == 0) {
	$tabla .= '
		<tr>
			<td colspan="12" class="text-center py-5">
				<div class="text-danger font-weight-bold" style="font-size:15px;">
					<i class="fas fa-search mr-2"></i> No se encontraron resultados
				</div>
				<div class="text-muted mt-1">
					Intente buscar por expediente, identidad, paciente o profesional.
				</div>
			</td>
		</tr>
	';
} else {
	$tabla .= '
		<tr>
			<td colspan="12" class="text-center py-4">
				<span class="badge badge-light border px-4 py-2" style="font-size:14px; border-radius:20px;">
					<i class="fas fa-calendar-check text-info mr-1"></i>
					Total de registros encontrados:
					<strong>'.number_format($nroProductos).'</strong>
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

$result->free();
$mysqli->close();