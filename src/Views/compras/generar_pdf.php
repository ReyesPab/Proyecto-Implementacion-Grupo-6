<?php
session_start();
use App\models\comprasModel;

$id_compra = $_GET['id_compra'] ?? null;

if (!$id_compra || !is_numeric($id_compra)) {
    die("ID de compra no válido");
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
    die("Error al cargar detalle de compra: " . $e->getMessage());
}

// Cálculos de totales
$subtotal = 0;
foreach ($detalles as $detalle) {
    $totalLinea = floatval($detalle['CANTIDAD']) * floatval($detalle['PRECIO_UNITARIO']);
    $subtotal += $totalLinea;
}

$descuentoPorcentaje = floatval($compra['DESCUENTO'] ?? 0);
$descuentoMonto = $subtotal * ($descuentoPorcentaje / 100);
$subtotalConDescuento = $subtotal - $descuentoMonto;
$tasaImpuestos = 0.00;
$totalImpuestos = 0.00;
$envio = floatval($compra['ENVIO'] ?? 0);
$totalFinal = $subtotalConDescuento + $totalImpuestos + $envio;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Orden de Compra #<?= $compra['ID_COMPRA'] ?> - Tesoro D' MIMI</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 0;
            padding: 20px;
            background-color: #f5f7fa;
        }
        .orden-compra-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            border: 1px solid #ddd;
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
            font-size: 14px;
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
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
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
    </style>
</head>
<body>
    <!-- Orden de Compra -->
    <div class="orden-compra-container" id="contenido-pdf">
        <!-- Encabezado -->
        <div class="header-orden">
            <h1>ORDEN DE COMPRA</h1>
            <h2>Tesoro D' MIMI</h2>
            <div class="header-info">
                <div>
                    <strong>Fecha:</strong> <?= date('d-m-Y', strtotime($compra['FECHA_COMPRA'])) ?>
                </div>
                <div>
                    <strong>No. de orden:</strong> <?= $compra['ID_COMPRA'] ?>
                </div>
                <div class="estado-badge">
                    <?= $compra['ESTADO_COMPRA'] ?>
                </div>
            </div>
        </div>

        <!-- Información de Empresa y Vendedor -->
        <div class="info-section">
            <div class="info-grid">
                <!-- Columna izquierda: Empresa -->
                <div class="info-column">
                    <h3>Nombre de la empresa</h3>
                    <div>
                        <strong>Tesoro D' MIMI</strong><br>
                        Domicilio: Av. Principal #123<br>
                        Ciudad, Estado, Código Postal<br>
                        Tegucigalpa, 11101 Honduras
                    </div>
                    
                    <h3 style="margin-top: 20px;">Enviar a</h3>
                    <div>
                        <strong>Tesoro D' MIMI</strong><br>
                        Departamento: Recursos Humanos<br>
                        Domicilio: Av. Principal #123<br>
                        Tegucigalpa, 11101 Honduras<br>
                        Teléfono: 504 9369-1281
                    </div>
                </div>

                <!-- Columna derecha: Vendedor -->
                <div class="info-column">
                    <h3>Vendedor</h3>
                    <div>
                        <strong>Compañía: <?= htmlspecialchars($compra['PROVEEDOR']) ?></strong><br>
                        Contacto: <?= htmlspecialchars($compra['CONTACTO'] ?? 'JOSÉ LÓPEZ') ?><br>
                        Domicilio: <?= htmlspecialchars($compra['DIRECCION'] ?? 'República de El Salvador 68, Centro') ?><br>
                        Ciudad, Estado, Código Postal<br>
                        Cualquiera, 06000 Ciudad de México<br>
                        Teléfono: <?= htmlspecialchars($compra['TELEFONO'] ?? '5578654435') ?>
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
                    <?php foreach ($detalles as $detalle): 
                        $totalLinea = floatval($detalle['CANTIDAD']) * floatval($detalle['PRECIO_UNITARIO']);
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($detalle['MATERIA_PRIMA']) ?></td>
                        <td class="text-center"><?= number_format(floatval($detalle['CANTIDAD']), 0) ?></td>
                        <td class="text-right">L <?= number_format(floatval($detalle['PRECIO_UNITARIO']), 2) ?></td>
                        <td class="text-right">L <?= number_format($totalLinea, 2) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Totales -->
        <div class="totales-section">
            <div style="margin-bottom: 15px;">
                <strong>Notas / Observaciones:</strong><br>
                <?= !empty($compra['OBSERVACION']) ? htmlspecialchars($compra['OBSERVACION']) : 'Sin observaciones' ?>
            </div>
            
            <div class="total-row">
                <div class="total-label">SUBTOTAL</div>
                <div class="total-value">L <?= number_format($subtotal, 2) ?></div>
            </div>
            
            <div class="total-row">
                <div class="total-label">DESCUENTO (%)</div>
                <div class="total-value"><?= number_format($descuentoPorcentaje, 2) ?>%</div>
            </div>
            
            <div class="total-row">
                <div class="total-label">SUBTOTAL MENOS DESCUENTO</div>
                <div class="total-value">L <?= number_format($subtotalConDescuento, 2) ?></div>
            </div>
            
            <div class="total-row">
                <div class="total-label">TASA DE IMPUESTOS</div>
                <div class="total-value"><?= number_format($tasaImpuestos, 2) ?>%</div>
            </div>
            
            <div class="total-row">
                <div class="total-label">TOTAL IMPUESTOS</div>
                <div class="total-value">L <?= number_format($totalImpuestos, 2) ?></div>
            </div>
            
            <div class="total-row">
                <div class="total-label">ENVÍO / ALMACENAJE</div>
                <div class="total-value">L <?= number_format($envio, 2) ?></div>
            </div>
            
            <div class="total-row total-grande">
                <div class="total-label">TOTAL</div>
                <div class="total-value">L <?= number_format($totalFinal, 2) ?></div>
            </div>
        </div>

        <!-- Firma -->
        <div class="firma-section">
            <div class="firma-line">
                <strong><?= htmlspecialchars($compra['USUARIO'] ?? 'MARTHA SÁNCHEZ') ?>, GERENTE DE COMPRAS</strong>
            </div>
        </div>
    </div>

    <!-- Botones de acción -->
    <div class="btn-group">
        <button type="button" class="btn btn-secondary" onclick="window.history.back()">
            <i class="bi bi-arrow-left"></i> Volver
        </button>
        <button type="button" class="btn btn-primary" onclick="generarPDF()">
            <i class="bi bi-download"></i> Descargar PDF
        </button>
    </div>

    <script>
        function generarPDF() {
            const element = document.getElementById('contenido-pdf');
            
            // Configuración para html2pdf
            const opt = {
                margin: [10, 10, 10, 10],
                filename: 'orden_compra_<?= $compra['ID_COMPRA'] ?>.pdf',
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { 
                    scale: 2,
                    useCORS: true
                },
                jsPDF: { 
                    unit: 'mm', 
                    format: 'a4', 
                    orientation: 'portrait' 
                }
            };
            
            // Generar y descargar PDF
            html2pdf()
                .set(opt)
                .from(element)
                .save()
                .then(() => {
                    console.log('PDF de compra generado exitosamente');
                })
                .catch(error => {
                    console.error('Error generando PDF:', error);
                    alert('Error al generar el PDF. Intente nuevamente.');
                });
        }

        // Generar PDF automáticamente al cargar la página
        window.onload = function() {
            setTimeout(() => {
                generarPDF();
            }, 1000);
        };
    </script>
</body>
</html>