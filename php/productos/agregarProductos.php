<?php
session_start();
require_once '../funtions.php'; // Asegúrate de incluir las funciones necesarias
require_once '../BaseDataAccess.php'; // Si el archivo está en el directorio superior

// Crear una instancia de BaseDataAccess
$db = new BaseDataAccess();

$usuario = $_SESSION['colaborador_id'];
$nombre = cleanStringStrtolower($_POST['nombre']);

$categoria = !empty(trim($_POST['categoria'] ?? '')) ? $_POST['categoria'] : 0;
$medida = !empty(trim($_POST['medida'] ?? '')) ? $_POST['medida'] : 0;
$almacen = !empty(trim($_POST['almacen'] ?? '')) ? $_POST['almacen'] : 0; // Usar el nombre correcto del campo

$concentracion = trim($_POST['concentracion'] ?? '');
$cantidad = !empty(trim($_POST['cantidad'] ?? '')) ? $_POST['cantidad'] : 0;
$precio_compra = !empty(trim($_POST['precio_compra'] ?? '')) ? $_POST['precio_compra'] : 0;
$precio_venta = !empty(trim($_POST['precio_venta'] ?? '')) ? $_POST['precio_venta'] : 0;
$cantidad_minima = !empty(trim($_POST['cantidad_minima'] ?? '')) ? intval($_POST['cantidad_minima']) : 0;
$cantidad_maxima = !empty(trim($_POST['cantidad_maxima'] ?? '')) ? intval($_POST['cantidad_maxima']) : 0;

$estado = $_POST['producto_activo'] ?? 2;
$isv = $_POST['producto_isv_factura'] ?? 2;

$descripcion = cleanStringStrtolower($_POST['descripcion']);
$fecha = date("Y-m-d");
$fecha_registro = date("Y-m-d H:i:s");

// Verificar que no exista el registro
$query = "SELECT productos_id FROM productos WHERE nombre = ?";
$parameters = [$nombre];
$result = $db->executeScalarQuery($query, $parameters);

if (empty($result)) {
    // Obtener el siguiente correlativo
    $productos_id = $db->getCorrelativo('productos_id', 'productos');
    
    // Insertar el nuevo producto
    $insert = "INSERT INTO productos (productos_id, almacen_id, medida_id, concentracion, nombre, descripcion, categoria_producto_id, cantidad, precio_compra, precio_venta, cantidad_minima, cantidad_maxima, estado, isv, colaborador_id, fecha_registro) 
               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $parameters = [
        $productos_id, $almacen, $medida, $concentracion, $nombre, $descripcion, $categoria, $cantidad,
        $precio_compra, $precio_venta, $cantidad_minima, $cantidad_maxima, $estado, $isv, $usuario, $fecha_registro
    ];
    $db->executeNonQuery($insert, $parameters);
    
    // Preparar datos de respuesta
    $datos = [
        0 => "Almacenado", 
        1 => "Registro Almacenado Correctamente", 
        2 => "success",
        3 => "btn-primary",
        4 => "formulario_productos",
        5 => "Registro",
        6 => "Productos", // Función de la tabla que llamaremos para que actualice (DataTable Bootstrap)
        7 => "modal_productos" // Modals para cierre automático
    ];

    // Consultar la categoría del producto
    $query_categoria = "SELECT nombre FROM categoria_producto WHERE categoria_producto_id = ?";
    $result_categoria = $db->executeScalarQuery($query_categoria, [$categoria]);

    $categoria_producto = "";
    if (!empty($result_categoria)) {
        $categoria_producto = $result_categoria[0]['nombre'];
    }

    // Actualizar los movimientos de los productos
    if ($categoria_producto == "Producto" || $categoria_producto == "Insumos") {
        $movimientos_id = $db->getCorrelativo('movimientos_id', 'movimientos');
        $documento = "Entrada Productos ".$movimientos_id;
        $comentario_movimientos = "Ingreso de Producto";
        
        if ($cantidad > 0) {
            // Corregir la inserción en movimientos
            $insert_movimiento = "INSERT INTO movimientos (movimientos_id, productos_id, documento, cantidad_entrada, cantidad_salida, saldo, fecha_registro, comentario) 
                                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            // Calcular el saldo
            $saldo = $cantidad; // Suponiendo que el saldo inicial es igual a la cantidad de entrada
            $parameters_movimiento = [
                $movimientos_id, $productos_id, $documento, $cantidad, 0, $saldo, $fecha_registro, $comentario_movimientos
            ];
            $db->executeNonQuery($insert_movimiento, $parameters_movimiento);
        }
    }

    // Insertar en historial
    $historial_numero  = $db->getCorrelativo('historial_id', 'historial');

    $estado_historial = "Agregar";
    $observacion_historial = "Se ha agregado un nuevo producto: $nombre con código: $productos_id";
    $modulo = "Productos";

    $insert_historial = "
    INSERT INTO historial (
        historial_id , 
        pacientes_id, 
        expediente, 
        modulo, 
        codigo, 
        colaborador_id, 
        servicio_id, 
        fecha, 
        status, 
        observacion, 
        usuario, 
        fecha_registro
    ) 
    VALUES (
        ?, 
        '0', 
        '0', 
        ?, 
        ?, 
        '0', 
        '0', 
        ?, 
        ?, 
        ?, 
        ?, 
        ?
    )";
    
    $historialParameters = [
        $historial_numero, 
        $modulo, 
        $productos_id, 
        $fecha, 
        $estado_historial, 
        $observacion_historial,
        $usuario, 
        $fecha_registro
    ];

    $db->executeNonQuery($insert_historial, $historialParameters);

} else {
    $datos = [
        0 => "Error", 
        1 => "Lo sentimos, este registro ya existe y no se puede almacenar", 
        2 => "error",
        3 => "btn-danger",
        4 => "",
        5 => ""
    ];
}

// Devolver los datos como JSON
echo json_encode($datos);