<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

$puesto_id = $_POST['puesto_id'];
$consulta = "SELECT colaborador_id, CONCAT(nombre, ' ', apellido) AS 'colaborador'
    FROM colaboradores 
	WHERE puesto_id = '$puesto_id'"; 
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
?>