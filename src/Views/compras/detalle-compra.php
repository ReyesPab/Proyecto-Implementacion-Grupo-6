<?php
session_start();
use App\models\comprasModel;

$id_compra = $_GET['id_compra'] ?? null;

if (!$id_compra || !is_numeric($id_compra)) {
    header('Location: /sistema/public/index.php?route=consultar-compras');
    exit;
}

try {
    require_once dirname(__DIR__, 2) . '/models/comprasModel.php';
    $comprasModel = new comprasModel();
    $resultado = $comprasModel->obtenerDetalleCompra($id_compra);
    
    if (!$resultado['success']) {
        throw new Exception($resultado['message']);
    }
    
    $compra = $resultado['data']['compra'];
    $detalles = $resultado['data']['detalles'];
    
} catch (Exception $e) {
    error_log("Error al cargar detalle de compra: " . $e->getMessage());
    header('Location: /sistema/public/index.php?route=consultar-compras');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Orden de Compra #<?php echo $compra['ID_COMPRA']; ?> - Tesoro D' MIMI</title>
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <style>
        :root {
            --primary-color: #2c3e50;
            --border-color: #dee2e6;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f5f7fa;
            color: #333;
            line-height: 1.4;
        }
        
        .main {
            padding: 20px;
            max-width: 1000px;
            margin: 0 auto;
        }
        
        .orden-compra-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            border: 1px solid #ddd;
            margin-bottom: 25px;
        }
        
        .header-orden {
            background: #2c3e50;
            color: white;
            padding: 25px 30px;
            border-radius: 8px 8px 0 0;
            text-align: center;
        }
        
        .header-orden h1 {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 5px;
            letter-spacing: 1px;
        }
        
        .header-orden h2 {
            font-size: 20px;
            font-weight: normal;
            margin-bottom: 15px;
        }
        
        .header-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 10px;
        }
        
        .info-section {
            padding: 25px 30px;
            border-bottom: 1px solid #eee;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }
        
        .info-column h3 {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 15px;
            color: #2c3e50;
            border-bottom: 2px solid #2c3e50;
            padding-bottom: 5px;
        }
        
        .info-line {
            margin-bottom: 8px;
            display: flex;
        }
        
        .info-label {
            font-weight: bold;
            min-width: 120px;
            color: #555;
        }
        
        .info-value {
            flex: 1;
        }
        
        .tabla-detalles {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 14px;
        }
        
        .tabla-detalles th {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 12px 8px;
            text-align: left;
            font-weight: bold;
            color: #2c3e50;
        }
        
        .tabla-detalles td {
            border: 1px solid #dee2e6;
            padding: 10px 8px;
        }
        
        .totales-section {
            padding: 20px 30px;
            background-color: #f8f9fa;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            padding: 5px 0;
        }
        
        .total-label {
            font-weight: normal;
        }
        
        .total-value {
            font-weight: bold;
            min-width: 150px;
            text-align: right;
        }
        
        .total-grande {
            font-size: 18px;
            font-weight: bold;
            border-top: 2px solid #2c3e50;
            padding-top: 10px;
            margin-top: 10px;
        }
        
        .firma-section {
            margin-top: 40px;
            text-align: center;
            padding: 20px 30px;
            border-top: 1px solid #ddd;
        }
        
        .firma-line {
            margin-top: 60px;
            border-top: 1px solid #333;
            padding-top: 10px;
            display: inline-block;
            min-width: 250px;
        }
        
        .estado-badge {
            background: #27ae60;
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
        }
        
        .btn-group {
            text-align: center;
            padding: 20px;
        }
        
        .btn {
            border-radius: 6px;
            padding: 10px 20px;
            font-weight: 600;
            border: none;
            transition: all 0.3s ease;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin: 0 5px;
            text-decoration: none;
            cursor: pointer;
        }
        
        .btn-primary {
            background: #2c3e50;
            color: white;
        }
        
        .btn-primary:hover {
            background: #1a252f;
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .mb-3 {
            margin-bottom: 15px;
        }
        
        @media print {
            body {
                background: white;
            }
            
            .orden-compra-container {
                box-shadow: none;
                border: none;
            }
            
            .btn-group {
                display: none;
            }
        }
        
        @media (max-width: 768px) {
            .main {
                padding: 10px;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .header-info {
                flex-direction: column;
                gap: 10px;
                text-align: center;
            }
            
            .tabla-detalles {
                font-size: 12px;
            }
        }
    </style>
</head>

<body>
    <main id="main" class="main">
        <!-- Orden de Compra -->
        <div class="orden-compra-container">
            <!-- Encabezado -->
            <div class="header-orden">
                <h1>ORDEN DE COMPRA</h1>
                <h2>Tesoro D' MIMI</h2>
                <div class="header-info">
                    <div>
                        <strong>Fecha:</strong> <?php echo date('d-m-Y', strtotime($compra['FECHA_COMPRA'])); ?>
                    </div>
                    <div>
                        <strong>No. de orden:</strong> <?php echo $compra['ID_COMPRA']; ?>
                    </div>
                    <div class="estado-badge">
                        <?php echo $compra['ESTADO_COMPRA']; ?>
                    </div>
                </div>
            </div>

            <!-- Información de Empresa y Vendedor -->
            <div class="info-section">
                <div class="info-grid">
                    <!-- Columna izquierda: Empresa -->
                    <div class="info-column">
                        <h3>Nombre de la empresa</h3>
                        <div class="info-line">
                            <div class="info-value">
                                <strong>Tesoro D' MIMI</strong><br>
                                Domicilio: Av. Principal #123<br>
                                Ciudad, Estado, Código Postal<br>
                                Tegucigalpa, 11101 Honduras
                            </div>
                        </div>
                        
                        <h3 style="margin-top: 20px;">Enviar a</h3>
                        <div class="info-line">
                            <div class="info-value">
                                <strong>Tesoro D' MIMI</strong><br>
                                Departamento: Recursos Humanos<br>
                                Domicilio: Av. Principal #123<br>
                                Tegucigalpa, 11101 Honduras<br>
                                Teléfono: 504 9369-1281
                            </div>
                        </div>
                    </div>

                    <!-- Columna derecha: Vendedor -->
                    <div class="info-column">
                        <h3>Vendedor</h3>
                        <div class="info-line">
                            <div class="info-value">
                                <strong>Compañía: <?php echo htmlspecialchars($compra['PROVEEDOR']); ?></strong><br>
                                Contacto: <?php echo htmlspecialchars($compra['CONTACTO'] ?? 'JOSÉ LÓPEZ'); ?><br>
                                Domicilio: <?php echo htmlspecialchars($compra['DIRECCION'] ?? 'República de El Salvador 68, Centro'); ?><br>
                                Ciudad, Estado, Código Postal<br>
                                Cualquiera, 06000 Ciudad de México<br>
                                Teléfono: <?php echo htmlspecialchars($compra['TELEFONO'] ?? '5578654435'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla de Artículos -->
            <div class="info-section">
                <table class="tabla-detalles">
                    <thead>
                        <tr>
                            <th>DESCRIPCIÓN</th>
                            <th>CANTIDAD</th>
                            <th>PRECIO UNITARIO</th>
                            <th>TOTAL</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $subtotal = 0;
                        foreach ($detalles as $detalle): 
                            $totalLinea = floatval($detalle['CANTIDAD']) * floatval($detalle['PRECIO_UNITARIO']);
                            $subtotal += $totalLinea;
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($detalle['MATERIA_PRIMA']); ?></td>
                            <td class="text-center"><?php echo number_format(floatval($detalle['CANTIDAD']), 0); ?></td>
                            <td class="text-right">L <?php echo number_format(floatval($detalle['PRECIO_UNITARIO']), 2); ?></td>
                            <td class="text-right">L <?php echo number_format($totalLinea, 2); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Totales -->
            <div class="totales-section">
                <div class="info-line mb-3">
                    <div class="info-label">Notas / Observaciones</div>
                    <div class="info-value"><?php echo !empty($compra['OBSERVACION']) ? htmlspecialchars($compra['OBSERVACION']) : 'Sin observaciones'; ?></div>
                </div>
                
                <?php
                // Cálculos de totales
                $descuentoPorcentaje = floatval($compra['DESCUENTO'] ?? 0);
                $descuentoMonto = $subtotal * ($descuentoPorcentaje / 100);
                $subtotalConDescuento = $subtotal - $descuentoMonto;
                $tasaImpuestos = 0.00; // Siempre 0.00% como solicitaste
                $totalImpuestos = 0.00; // Siempre 0.00
                $envio = floatval($compra['ENVIO'] ?? 0);
                $totalFinal = $subtotalConDescuento + $totalImpuestos + $envio;
                ?>
                
                <div class="total-row">
                    <div class="total-label">SUBTOTAL</div>
                    <div class="total-value">L <?php echo number_format($subtotal, 2); ?></div>
                </div>
                
                <div class="total-row">
                    <div class="total-label">DESCUENTO (%)</div>
                    <div class="total-value"><?php echo number_format($descuentoPorcentaje, 2); ?>%</div>
                </div>
                
                <div class="total-row">
                    <div class="total-label">SUBTOTAL MENOS DESCUENTO</div>
                    <div class="total-value">L <?php echo number_format($subtotalConDescuento, 2); ?></div>
                </div>
                
                <div class="total-row">
                    <div class="total-label">TASA DE IMPUESTOS</div>
                    <div class="total-value"><?php echo number_format($tasaImpuestos, 2); ?>%</div>
                </div>
                
                <div class="total-row">
                    <div class="total-label">TOTAL IMPUESTOS</div>
                    <div class="total-value">L <?php echo number_format($totalImpuestos, 2); ?></div>
                </div>
                
                <div class="total-row">
                    <div class="total-label">ENVÍO / ALMACENAJE</div>
                    <div class="total-value">L <?php echo number_format($envio, 2); ?></div>
                </div>
                
                <div class="total-row total-grande">
                    <div class="total-label">TOTAL</div>
                    <div class="total-value">L <?php echo number_format($totalFinal, 2); ?></div>
                </div>
            </div>

            <!-- Firma -->
            <div class="firma-section">
                <div class="firma-line">
                    <strong><?php echo htmlspecialchars($compra['USUARIO'] ?? 'MARTHA SÁNCHEZ'); ?>, GERENTE DE COMPRAS</strong>
                </div>
            </div>
        </div>

        <!-- Botones de acción -->
        <div class="btn-group">
            <button type="button" class="btn btn-secondary" onclick="window.history.back()">
                <i class="bi bi-arrow-left"></i> Volver
            </button>
            <button type="button" class="btn btn-primary" onclick="window.print()">
                <i class="bi bi-printer"></i> Imprimir
            </button>
        </div>
    </main>

    <script>
        function imprimirDetalle() {
            window.print();
        }
    </script>
</body>
</html>