<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

$puesto_id = $_POST['puesto_id']; 

$consulta = "SELECT c.colaborador_id AS 'colaborador_id', CONCAT(c.nombre,' ',c.apellido) 'colaborador'
       FROM colaboradores AS c
       WHERE c.puesto_id = '$puesto_id' AND c.estatus = 1
       ORDER BY c.colaborador_id"; 
$result = $mysqli->query($consulta);	   
  
if($result->num_rows>0){
	while($consulta2 = $result->fetch_assoc()){
	    echo '<option value="'.$consulta2['colaborador_id'].'">'.$consulta2['colaborador'].'</option>';
	}
}else{
	echo '<option value="">No hay resultados que mostrar</option>';
}

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN