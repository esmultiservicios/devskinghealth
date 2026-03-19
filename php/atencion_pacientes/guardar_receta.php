<?php
session_start();
include "../funtions.php";

header('Content-Type: application/json');

$pacientes_id = isset($_POST['receta_pacientes_id']) ? (int)$_POST['receta_pacientes_id'] : 0;
$colaboradorId = !empty($_POST['receta_colaboradorId']) ? (int)$_POST['receta_colaboradorId'] : (isset($_SESSION['colaborador_id']) ? (int)$_SESSION['colaborador_id'] : 0);
$servicioId = isset($_POST['receta_servicioId']) ? (int)$_POST['receta_servicioId'] : 0;
$empresa_id = isset($_SESSION['empresa_id']) ? (int)$_SESSION['empresa_id'] : 0;
$estado = 1;

$productos = isset($_POST['producto']) && is_array($_POST['producto']) ? $_POST['producto'] : [];
$productos_manuales = isset($_POST['producto_manual']) && is_array($_POST['producto_manual']) ? $_POST['producto_manual'] : [];
$cantidad = isset($_POST['cantidad']) && is_array($_POST['cantidad']) ? $_POST['cantidad'] : [];
$descripciones = isset($_POST['descripcion']) && is_array($_POST['descripcion']) ? $_POST['descripcion'] : [];

$fecha = date('Y-m-d H:i:s');

// Validaciones generales
if ($pacientes_id <= 0) {
    echo json_encode([
        "status" => "error",
        "message" => "Error: El paciente es requerido"
    ]);
    exit();
}

if ($servicioId <= 0) {
    echo json_encode([
        "status" => "error",
        "message" => "Error: El servicio es requerido"
    ]);
    exit();
}

if ($empresa_id <= 0) {
    echo json_encode([
        "status" => "error",
        "message" => "Error: No se encontró la empresa en la sesión"
    ]);
    exit();
}

if (count($cantidad) == 0) {
    echo json_encode([
        "status" => "error",
        "message" => "Error: Debe agregar al menos un medicamento a la receta"
    ]);
    exit();
}

if (count($cantidad) != count($descripciones)) {
    echo json_encode([
        "status" => "error",
        "message" => "Error: Los datos de cantidad y descripción no coinciden"
    ]);
    exit();
}

// Si producto_manual no viene con el mismo número de posiciones, lo normalizamos
$totalFilas = count($cantidad);
for ($x = count($productos_manuales); $x < $totalFilas; $x++) {
    $productos_manuales[$x] = '';
}

// Si producto tampoco viene completo, lo normalizamos
for ($x = count($productos); $x < $totalFilas; $x++) {
    $productos[$x] = '';
}

$mysqli = connect_mysqli();

if ($mysqli->connect_error) {
    echo json_encode([
        "status" => "error",
        "message" => "Conexión fallida: " . $mysqli->connect_error
    ]);
    exit();
}

$mysqli->begin_transaction();

try {
    // Insertar encabezado de receta
    $stmt_receta = $mysqli->prepare("
        INSERT INTO recetas (
            pacientes_id,
            colaborador_id,
            servicio_id,
            empresa_id,
            fecha,
            estado
        ) VALUES (?, ?, ?, ?, ?, ?)
    ");

    if (!$stmt_receta) {
        throw new Exception("Error al preparar la receta: " . $mysqli->error);
    }

    $stmt_receta->bind_param(
        "iiiisi",
        $pacientes_id,
        $colaboradorId,
        $servicioId,
        $empresa_id,
        $fecha,
        $estado
    );

    if (!$stmt_receta->execute()) {
        throw new Exception("Error al insertar la receta: " . $stmt_receta->error);
    }

    $receta_id = $stmt_receta->insert_id;

    // Insertar detalle de receta
    $stmt_detalle = $mysqli->prepare("
        INSERT INTO receta_detalles (
            receta_id,
            tipo_producto,
            productos_id,
            nombre_manual,
            cantidad,
            descripcion
        ) VALUES (?, ?, ?, ?, ?, ?)
    ");

    if (!$stmt_detalle) {
        throw new Exception("Error al preparar el detalle de receta: " . $mysqli->error);
    }

    for ($i = 0; $i < $totalFilas; $i++) {
        $producto_id = isset($productos[$i]) && $productos[$i] !== '' ? (int)$productos[$i] : null;
        $producto_manual = isset($productos_manuales[$i]) ? trim($productos_manuales[$i]) : '';
        $cantidad_producto = isset($cantidad[$i]) ? (float)$cantidad[$i] : 0;
        $descripcion_producto = isset($descripciones[$i]) ? trim($descripciones[$i]) : '';

        // Determinar si la fila es manual o de catálogo
        if ($producto_manual !== '') {
            $tipo_producto = 'MANUAL';
            $producto_id = null;
        } else {
            $tipo_producto = 'CATALOGO';
        }

        // Validar fila
        if ($cantidad_producto <= 0) {
            throw new Exception("Error: La cantidad debe ser mayor a cero en la fila " . ($i + 1));
        }

        if ($descripcion_producto === '') {
            throw new Exception("Error: La descripción es requerida en la fila " . ($i + 1));
        }

        if ($tipo_producto === 'CATALOGO' && empty($producto_id)) {
            throw new Exception("Error: Debe seleccionar un producto de catálogo o escribir uno manual en la fila " . ($i + 1));
        }

        if ($tipo_producto === 'MANUAL' && $producto_manual === '') {
            throw new Exception("Error: Debe escribir el medicamento manual en la fila " . ($i + 1));
        }

        $stmt_detalle->bind_param(
            "isisss",
            $receta_id,
            $tipo_producto,
            $producto_id,
            $producto_manual,
            $cantidad_producto,
            $descripcion_producto
        );

        if (!$stmt_detalle->execute()) {
            throw new Exception("Error al insertar el detalle en la fila " . ($i + 1) . ": " . $stmt_detalle->error);
        }
    }

    $mysqli->commit();

    $stmt_receta->close();
    $stmt_detalle->close();
    $mysqli->close();

    echo json_encode([
        "status" => "success",
        "message" => "Receta guardada exitosamente",
        "receta_id" => $receta_id
    ]);
} catch (Exception $e) {
    $mysqli->rollback();

    if (isset($stmt_receta) && $stmt_receta) {
        $stmt_receta->close();
    }

    if (isset($stmt_detalle) && $stmt_detalle) {
        $stmt_detalle->close();
    }

    $mysqli->close();

    echo json_encode([
        "status" => "error",
        "message" => "Error al guardar la receta: " . $e->getMessage()
    ]);
}