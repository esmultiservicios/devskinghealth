<?php
session_start();   
include "../funtions.php";

//CONEXION A DB
$mysqli = connect_mysqli(); 	

$paginaActual = $_POST['partida'];
$colaborador_id = $_POST['colaborador_id'];

if($colaborador_id == ""){
	$where = "";
}else{
	$where = "WHERE sc.colaborador_id = '$colaborador_id'";
}

$query = "SELECT sc.servicios_colaboradores_id AS 'servicios_colaboradores_id', CONCAT(c.nombre, ' ', c.apellido) AS 'colaborador', s.nombre AS 'servicio', sc.colaborador_id AS 'colaborador_id'
	FROM servicios_colaboradores AS sc
	INNER JOIN servicios AS s
	ON sc.servicio_id = s.servicio_id
	INNER JOIN colaboradores AS c
	ON sc.colaborador_id = c.colaborador_id
	".$where."
	ORDER by sc.colaborador_id";	
 $result = $mysqli->query($query);
 $nroProductos = $result->num_rows;
  
  
 $nroLotes = 3;
 $nroPaginas = ceil($nroProductos/$nroLotes);
 $lista = '';
 $tabla = '';

 if($paginaActual > 1){
	$lista = $lista.'<li class="page-item"><a class="page-link" href="javajcript:paginationAsignacionServiciosColaboradores('.(1).');void(0);">Inicio</a></li>';
 }

 if($paginaActual > 1){
	$lista = $lista.'<li class="page-item"><a class="page-link" href="javajcript:paginationAsignacionServiciosColaboradores('.($paginaActual-1).');void(0);">Anterior '.($paginaActual-1).'</a></li>';
 }

 if($paginaActual < $nroPaginas){
	$lista = $lista.'<li class="page-item"><a class="page-link" href="javajcript:paginationAsignacionServiciosColaboradores('.($paginaActual+1).');void(0);">Siguiente '.($paginaActual+1).' de '.$nroPaginas.'</a></li>';
 }

 if($paginaActual > 1){
	$lista = $lista.'<li class="page-item"><a class="page-link" href="javajcript:paginationAsignacionServiciosColaboradores('.($nroPaginas).');void(0);">Ultima</a></li>';
 }

 if($paginaActual <= 1){
	$limit = 0;
 }else{
	$limit = $nroLotes*($paginaActual-1);
 }


$registro = "SELECT sc.servicios_colaboradores_id AS 'servicios_colaboradores_id', CONCAT(c.nombre, ' ', c.apellido) AS 'colaborador', s.nombre AS 'servicio', sc.colaborador_id AS 'colaborador_id'
	FROM servicios_colaboradores AS sc
	INNER JOIN servicios AS s
	ON sc.servicio_id = s.servicio_id
	INNER JOIN colaboradores AS c
	ON sc.colaborador_id = c.colaborador_id
	".$where."
	ORDER by sc.colaborador_id
	LIMIT $limit, $nroLotes";
$result = $mysqli->query($registro);		


$tabla = $tabla.'<table class="table table-striped table-condensed table-hover">
					<tr>
					  <th width="43.33%">Profesional</th>
					  <th width="43.33%">Servicio</th>							  
					  <th width="10.33%">Opciones</th>
					</tr>';
					
while($registro2 = $result->fetch_assoc()){						
	$tabla = $tabla.'<tr>
	   <td>'.$registro2['colaborador'].'</td>		
	   <td>'.$registro2['servicio'].'</td>	   
	   <td>
		   <a style="text-decoration:none; "href="javascript:modal_eliminarAsignacionColaborador('.$registro2['servicios_colaboradores_id'].','.$registro2['colaborador_id'].');void(0);" class="fas fa-trash fa-lg"></a>
	   </td>
  </tr>';
}
	

if($nroProductos == 0){
	$tabla = $tabla.'<tr>
	   <td colspan="7" style="color:#C7030D">No se encontraron resultados</td>
	</tr>';		
}else{
   $tabla = $tabla.'<tr>
	  <td colspan="7"><b><p ALIGN="center">Total de Registros Encontrados '.$nroProductos.'</p></b>
   </tr>';		
}

$tabla = $tabla.'</table>';

$array = array(0 => $tabla,
			   1 => $lista);

echo json_encode($array);

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN	
?>