<?php
session_start();
use App\models\comprasModel;

// Obtener filtros desde la URL
$fecha_inicio = $_GET['fecha_inicio'] ?? '';
$fecha_fin = $_GET['fecha_fin'] ?? '';
$id_proveedor = $_GET['id_proveedor'] ?? '';
$estado_compra = $_GET['estado_compra'] ?? '';

try {
    require_once dirname(__DIR__, 2) . '/models/comprasModel.php';
    $comprasModel = new comprasModel();
    
    // Obtener compras con los filtros aplicados
    $resultado = $comprasModel->obtenerComprasParaReporte($fecha_inicio, $fecha_fin, $id_proveedor, $estado_compra);
    
    if (!$resultado['success']) {
        throw new Exception($resultado['message']);
    }
    
    $compras = $resultado['data'];
    
} catch (Exception $e) {
    die("Error al cargar compras para reporte: " . $e->getMessage());
}

// Agrupar compras por ID_COMPRA para evitar duplicados
$comprasAgrupadas = [];
foreach ($compras as $compra) {
    if (!isset($comprasAgrupadas[$compra['ID_COMPRA']])) {
        $comprasAgrupadas[$compra['ID_COMPRA']] = $compra;
    }
}

// Calcular totales
$totalCompras = count($comprasAgrupadas);
$montoTotal = array_sum(array_column($comprasAgrupadas, 'SUBTOTAL'));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Compras - Tesoro D' MIMI</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 0;
            padding: 20px;
            background-color: #f5f7fa;
        }
        .reporte-container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            border: 1px solid #ddd;
        }
        .header-reporte {
            background: #2c3e50;
            color: white;
            padding: 25px 30px;
            border-radius: 8px 8px 0 0;
            text-align: center;
        }
        .header-reporte h1 {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 5px;
            letter-spacing: 1px;
        }
        .header-reporte h2 {
            font-size: 20px;
            font-weight: normal;
            margin-bottom: 15px;
        }
        .info-filtros {
            background: #f8f9fa;
            padding: 15px 30px;
            border-bottom: 1px solid #dee2e6;
            font-size: 14px;
        }
        .filtro-item {
            margin-bottom: 5px;
        }
        .tabla-reporte {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 12px;
        }
        .tabla-reporte th {
            background-color: #34495e;
            color: white;
            border: 1px solid #2c3e50;
            padding: 12px 8px;
            text-align: left;
            font-weight: bold;
        }
        .tabla-reporte td {
            border: 1px solid #dee2e6;
            padding: 10px 8px;
        }
        .totales-section {
            padding: 20px 30px;
            background-color: #f8f9fa;
            border-top: 2px solid #dee2e6;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            padding: 5px 0;
            font-size: 14px;
        }
        .total-grande {
            font-size: 18px;
            font-weight: bold;
            border-top: 2px solid #2c3e50;
            padding-top: 10px;
            margin-top: 10px;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .estado-activa {
            background-color: #28a745;
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
        }
        .estado-anulada {
            background-color: #dc3545;
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
        }
        .estado-pendiente {
            background-color: #ffc107;
            color: black;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            padding: 20px 30px;
            color: #6c757d;
            font-size: 12px;
            border-top: 1px solid #dee2e6;
        }
    </style>
</head>
<body>
    <div class="reporte-container" id="contenido-pdf">
        <!-- Encabezado -->
        <div class="header-reporte">
            <h1>REPORTE DE COMPRAS</h1>
            <h2>Tesoro D' MIMI</h2>
            <div style="margin-top: 10px; font-size: 14px;">
                Generado el: <?= date('d/m/Y H:i') ?>
            </div>
        </div>

        <!-- Información de filtros aplicados -->
        <div class="info-filtros">
            <div class="filtro-item"><strong>Filtros aplicados:</strong></div>
            <div class="filtro-item">Fecha: <?= $fecha_inicio ? $fecha_inicio . ' a ' . $fecha_fin : 'Todas las fechas' ?></div>
            <div class="filtro-item">Proveedor: <?= $id_proveedor ? 'Filtrado' : 'Todos los proveedores' ?></div>
            <div class="filtro-item">Estado: <?= $estado_compra ? $estado_compra : 'Todos los estados' ?></div>
        </div>

        <!-- Tabla de compras -->
        <div style="padding: 0 30px;">
            <table class="tabla-reporte">
                <thead>
                    <tr>
                        <th width="8%">IdCompra</th>
                        <th width="15%">Proveedor</th>
                        <th width="12%">Usuario</th>
                        <th width="12%">FechaCompra</th>
                        <th width="12%">Subtotal</th>
                        <th width="12%">Descuento</th>
                        <th width="12%">Total</th>
                        <th width="10%">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $contador = 0;
                    foreach ($comprasAgrupadas as $compra): 
                        $contador++;
                        $descuento = floatval($compra['DESCUENTO'] ?? 0);
                        $subtotal = floatval($compra['SUBTOTAL'] ?? 0);
                        $totalCompra = $subtotal - ($subtotal * ($descuento / 100));
                    ?>
                    <tr>
                        <td class="text-center"><?= $compra['ID_COMPRA'] ?></td>
                        <td><?= htmlspecialchars($compra['PROVEEDOR']) ?></td>
                        <td><?= htmlspecialchars($compra['USUARIO']) ?></td>
                        <td class="text-center"><?= date('d/m/Y', strtotime($compra['FECHA_COMPRA'])) ?></td>
                        <td class="text-right">L <?= number_format($subtotal, 2) ?></td>
                        <td class="text-right"><?= number_format($descuento, 2) ?>%</td>
                        <td class="text-right">L <?= number_format($totalCompra, 2) ?></td>
                        <td class="text-center">
                            <span class="<?= getClaseEstado($compra['ESTADO_COMPRA']) ?>">
                                <?= $compra['ESTADO_COMPRA'] ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    
                    <?php if (empty($comprasAgrupadas)): ?>
                    <tr>
                        <td colspan="8" class="text-center" style="padding: 20px;">
                            No se encontraron compras con los filtros aplicados
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Totales -->
        <div class="totales-section">
            <div class="total-row">
                <div>Total de Compras:</div>
                <div class="text-right"><strong><?= $totalCompras ?></strong></div>
            </div>
            <div class="total-row total-grande">
                <div>MONTO TOTAL:</div>
                <div class="text-right">L <?= number_format($montoTotal, 2) ?></div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            Reporte generado automáticamente por el Sistema de Gestión Tesoro D' MIMI
        </div>
    </div>

    <script>
        function generarPDF() {
            const element = document.getElementById('contenido-pdf');
            
            // Configuración para html2pdf
            const opt = {
                margin: [10, 10, 10, 10],
                filename: 'reporte_compras_<?= date('Y-m-d') ?>.pdf',
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { 
                    scale: 2,
                    useCORS: true
                },
                jsPDF: { 
                    unit: 'mm', 
                    format: 'a4', 
                    orientation: 'landscape' 
                }
            };
            
            // Generar y descargar PDF
            html2pdf()
                .set(opt)
                .from(element)
                .save()
                .then(() => {
                    console.log('PDF de reporte generado exitosamente');
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

<?php
function getClaseEstado($estado) {
    switch($estado) {
        case 'ACTIVA': return 'estado-activa';
        case 'ANULADA': return 'estado-anulada';
        case 'PENDIENTE': return 'estado-pendiente';
        default: return 'estado-activa';
    }
}
?>