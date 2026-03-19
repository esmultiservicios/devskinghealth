<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receta</title>
    <style>
        body {
            /*font-family: Arial, sans-serif;*/
            font-family: 'Helvetica';
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-size: 14px; /* Tamaño de fuente base */
            overflow: hidden; /* Evita contenido extra */
        }
        .container {
            width: 100%;
            margin: 0;
            padding: 0;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header img {
            width: 110px;
            height: 70px;
        }
        .header .empresa-info {
            text-align: right;
            align-self: flex-start; /* Alinea la información de la empresa con la parte superior del contenedor */
            line-height: 0.5; /* Reduce la separación entre líneas */
            font-size: 14px; /* Tamaño de fuente para la información de la empresa */
        }
        .header .empresa-info h2 {
            font-size: 14px; /* Tamaño de fuente para el nombre de la empresa */
        }
        .content {
            margin-bottom: 20px;
        }
        .content .info {
            margin-bottom: 8px;
            font-size: 14px; /* Tamaño de fuente para la información de la receta */
        }
        .content .info strong {
            display: inline-block;
            width: 100px;
            font-size: 16px; /* Tamaño de fuente para los títulos de la información de la receta */
            line-height: 0.5; /* Reducir la separación entre líneas */
        }
        .productos {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px; /* Tamaño de fuente para la tabla de productos */
        }
        .productos th, .productos td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
            vertical-align: middle; /* Centrar el contenido verticalmente */
        }
        .productos th {
            background-color: #f0f0f0;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Encabezado con logo y datos de la empresa -->
        <div class="header">
            <img src="<?php echo SERVERURL; ?>img/logo_factura.jpg" width="250px" height="100px">
            <div class="empresa-info">
                <h2><?php echo $empresa; ?></h2>
                <p><strong>RTN:</strong> <?php echo $empresa_rtn; ?></p>
                <p><strong>Ubicación:</strong> <?php echo $empresa_ubicacion; ?></p>
            </div>
        </div>

        <!-- Información de la receta -->
        <div class="content">
            <div class="info">
                <strong>Paciente:</strong> <?php echo $paciente; ?>
            </div>
            <div class="info">
                <strong>Fecha:</strong> <?php echo $fecha; ?>
            </div>
            <div class="info">
                <strong>Doctor:</strong> <?php echo $doctor; ?>
            </div>
        </div>

        <!-- Tabla de productos -->
        <table class="productos">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Descripción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($productos as $index => $producto): ?>
                    <tr>
                        <td><?php echo $index + 1; ?></td>                        
                        <td><?php echo $producto['nombre']; ?></td>
                        <td><?php echo $producto['cantidad']; ?></td>
                        <td><?php echo $producto['descripcion']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>