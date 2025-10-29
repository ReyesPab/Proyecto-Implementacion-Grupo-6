<?php
// src/Views/compras/consultar-compras.php

use App\models\comprasModel;

try {
    require_once dirname(__DIR__, 2) . '/models/comprasModel.php';
    $comprasModel = new comprasModel();
    $proveedores = $comprasModel->obtenerProveedores();
} catch (Exception $e) {
    error_log("Error al cargar datos para consultar compras: " . $e->getMessage());
    $proveedores = []; // Asegurar que siempre sea un array
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Consultar Compras - Sistema de Gestión</title>
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        .btn-group-sm .btn {
            padding: 0.2rem 0.4rem;
            font-size: 0.75rem;
            border-radius: 0.2rem;
        }

        .bi {
            font-size: 0.8rem;
        }

        .btn-group {
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            gap: 1px;
        }

        .btn-outline-primary:hover { background-color: #0d6efd; color: white; }
        .btn-outline-info:hover { background-color: #17a2b8; color: white; }
        .btn-outline-success:hover { background-color: #198754; color: white; }

        /* Tabla general */
        .table {
            font-size: 0.85rem !important;
            width: 100% !important;
        }

        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
            white-space: nowrap;
            font-size: 0.9rem;
            padding: 12px 8px;
            border-bottom: 2px solid #dee2e6;
        }

        .table td {
            vertical-align: middle;
            word-wrap: break-word;
            word-break: break-word;
            padding: 10px 8px;
            line-height: 1.3;
            border-bottom: 1px solid #dee2e6;
        }

        .badge {
            font-size: 0.7em;
            padding: 4px 8px;
        }

        /* Columnas específicas con mejor manejo de texto */
        .table td:nth-child(1) { /* PROVEEDOR */ 
            min-width: 120px;
            max-width: 150px;
        }

        .table td:nth-child(2) { /* USUARIO */
            min-width: 100px;
            max-width: 120px;
        }

        .table td:nth-child(3) { /* MATERIA_PRIMA */
            min-width: 140px;
            max-width: 180px;
        }

        .table td:nth-child(4) { /* CANTIDAD */
            min-width: 80px;
            max-width: 100px;
            text-align: center;
        }

        .table td:nth-child(5) { /* UNIDAD */
            min-width: 80px;
            max-width: 100px;
            text-align: center;
        }

        .table td:nth-child(6),
        .table td:nth-child(7) { /* PRECIO UNITARIO y SUBTOTAL */
            min-width: 100px;
            max-width: 120px;
            text-align: right;
        }

        .table td:nth-child(8) { /* FECHA */
            min-width: 100px;
            max-width: 110px;
            white-space: nowrap;
        }

        .table td:nth-child(9) { /* ESTADO */
            min-width: 90px;
            max-width: 100px;
            text-align: center;
        }

        /* Columna de acciones compacta */
        .table th:last-child,
        .table td:last-child {
            width: 120px !important;
            min-width: 120px !important;
            max-width: 120px !important;
            padding: 8px 4px !important;
            text-align: center;
        }

        /* Loading styles */
        .loading {
            display: none;
            text-align: center;
            padding: 40px;
        }

        .loading-spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #0d6efd;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 2s linear infinite;
            margin: 0 auto 15px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Resumen card styles */
        .resumen-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
        }

        .resumen-card .card-body {
            padding: 20px;
        }

        .resumen-card h6 {
            color: white;
            margin-bottom: 15px;
            font-weight: 600;
        }

        .resumen-card p {
            margin-bottom: 8px;
            font-size: 0.95rem;
        }

        /* Filter styles */
        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 6px;
            font-size: 0.85rem;
        }

        .form-control, .form-select {
            border: 1px solid #ced4da;
            border-radius: 6px;
            padding: 8px 12px;
            font-size: 0.85rem;
        }

        .btn-refresh {
            background: #6c757d;
            color: white;
            border: none;
            padding: 8px 12px;
        }

        .btn-refresh:hover {
            background: #5a6268;
        }

        /* Header styles */
        .page-header {
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .table {
                font-size: 0.8rem !important;
            }
            
            .table th,
            .table td {
                padding: 8px 6px;
            }
        }

        /* Hover effects */
        .table tbody tr:hover {
            background-color: rgba(13, 110, 253, 0.04);
        }

        /* Badge colors */
        .bg-estado-activa {
            background-color: #198754 !important;
        }

        .bg-estado-anulada {
            background-color: #dc3545 !important;
        }

        .bg-estado-pendiente {
            background-color: #ffc107 !important;
            color: #000 !important;
        }
    </style>
</head>

<body>
    <?php require_once dirname(__DIR__) . '/partials/header.php'; ?>
<?php require_once dirname(__DIR__) . '/partials/sidebar.php'; ?>
    <main id="main" class="main">
        <div class="container-fluid">
            
                        <!-- Header -->
            <div class="page-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h1 class="h2 mb-0">Lista de Compras</h1>
                    <div class="d-flex gap-2">
                        <!-- Botón Realizar Pedido -->
                        <a href="/sistema/public/registrar-compras" class="btn btn-primary">
                            <i class="bi bi-cart-plus"></i> Realizar Pedido
                        </a>
                        
                        <!-- Dropdown para Exportar -->
                        <div class="dropdown">
                            <button class="btn btn-success dropdown-toggle" type="button" id="dropdownExportar" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-download"></i> Exportar Reporte
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="dropdownExportar">
                                <li><a class="dropdown-item" href="#" onclick="exportarPDF()"><i class="bi bi-file-pdf"></i> Exportar PDF</a></li>
                                <li><a class="dropdown-item" href="#" onclick="exportarExcel()"><i class="bi bi-file-spreadsheet"></i> Exportar Excel</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Card -->
            <div class="card">
                <div class="card-body">
                    <!-- Filtros -->
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <div class="bg-light p-3 rounded">
                                <div class="row g-3 align-items-end">
                                    <div class="col-md-3">
                                        <label for="fecha_inicio" class="form-label">Fecha Inicio</label>
                                        <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="fecha_fin" class="form-label">Fecha Fin</label>
                                        <input type="date" class="form-control" id="fecha_fin" name="fecha_fin">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="id_proveedor" class="form-label">Proveedor</label>
                                        <select class="form-select" id="id_proveedor" name="id_proveedor">
                                            <option value="">Todos los proveedores</option>
                                            <?php if (!empty($proveedores)): ?>
                                                <?php foreach ($proveedores as $proveedor): ?>
                                                    <option value="<?php echo $proveedor['ID_PROVEEDOR']; ?>">
                                                        <?php echo htmlspecialchars($proveedor['NOMBRE']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <option value="">No hay proveedores disponibles</option>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="estado_compra" class="form-label">Estado</label>
                                        <select class="form-select" id="estado_compra" name="estado_compra">
                                            <option value="">Todos</option>
                                            <option value="ACTIVA">Activa</option>
                                            <option value="ANULADA">Anulada</option>
                                            <option value="PENDIENTE">Pendiente</option>
                                        </select>
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-refresh w-100" onclick="limpiarFiltros()" title="Limpiar filtros">
                                            <i class="bi bi-arrow-clockwise"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Loading -->
                    <div class="loading" id="loading">
                        <div class="loading-spinner"></div>
                        <p class="text-muted mt-2">Cargando compras...</p>
                    </div>

                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table table-hover" id="tablaCompras">
                            <thead>
                                <tr>
                                    <th>Proveedor</th>
                                    <th>Usuario</th>
                                    <th>Materia Prima</th>
                                    <th>Cantidad</th>
                                    <th>Unidad</th>
                                    <th>Precio Unitario</th>
                                    <th>Subtotal</th>
                                    <th>Fecha</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyCompras">
                                <!-- Las compras se cargarán aquí dinámicamente -->
                            </tbody>
                        </table>
                    </div>

                    <!-- No Results -->
                    <div class="text-center mt-4" id="sinResultados" style="display: none;">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            No se encontraron compras con los filtros aplicados.
                        </div>
                    </div>

                    <!-- Resumen -->
                    <div class="row mt-4">
                        <div class="col-md-4">
                            <div class="card resumen-card">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <i class="bi bi-graph-up me-2"></i>Resumen de Compras
                                    </h6>
                                    <div id="resumenCompras">
                                        <p>Total de compras: <strong id="totalCompras">0</strong></p>
                                        <p>Monto total: <strong id="montoTotal">L 0.00</strong></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
 
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Cargar compras automáticamente al iniciar
            cargarCompras();
            
            // Escuchar cambios en los filtros para búsqueda automática
            document.getElementById('fecha_inicio').addEventListener('change', cargarCompras);
            document.getElementById('fecha_fin').addEventListener('change', cargarCompras);
            document.getElementById('id_proveedor').addEventListener('change', cargarCompras);
            document.getElementById('estado_compra').addEventListener('change', cargarCompras);
        });

        function cargarCompras() {
            const loading = document.getElementById('loading');
            const tbody = document.getElementById('tbodyCompras');
            const sinResultados = document.getElementById('sinResultados');
            
            loading.style.display = 'block';
            tbody.innerHTML = '';
            sinResultados.style.display = 'none';
            
            // Obtener filtros
            const filtros = {
                fecha_inicio: document.getElementById('fecha_inicio').value,
                fecha_fin: document.getElementById('fecha_fin').value,
                id_proveedor: document.getElementById('id_proveedor').value,
                estado_compra: document.getElementById('estado_compra').value
            };
            
            // Construir query string
            const queryParams = new URLSearchParams();
            Object.keys(filtros).forEach(key => {
                if (filtros[key]) {
                    queryParams.append(key, filtros[key]);
                }
            });
            
            fetch(`/sistema/public/index.php?route=compras&caso=listar&${queryParams.toString()}`)
            .then(response => {
                console.log('Response status:', response.status);
                if (!response.ok) {
                    throw new Error('Error en la respuesta del servidor: ' + response.status);
                }
                return response.text();
            })
            .then(text => {
                console.log('Respuesta recibida:', text);
                try {
                    const data = JSON.parse(text);
                    console.log('Datos parseados:', data);
                    
                    if (data.status === 200 && data.data && data.data.length > 0) {
                        mostrarCompras(data.data);
                    } else {
                        tbody.innerHTML = '';
                        sinResultados.style.display = 'block';
                        actualizarResumen([]);
                    }
                } catch (e) {
                    console.error('Error parseando JSON:', e);
                    console.error('Texto recibido:', text);
                    throw new Error('Respuesta no válida del servidor');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                tbody.innerHTML = '<tr><td colspan="10" class="text-center text-danger">Error al cargar las compras: ' + error.message + '</td></tr>';
                actualizarResumen([]);
            })
            .finally(() => {
                loading.style.display = 'none';
            });
        }

        function mostrarCompras(compras) {
            const tbody = document.getElementById('tbodyCompras');
            const sinResultados = document.getElementById('sinResultados');
            
            if (compras.length === 0) {
                tbody.innerHTML = '';
                sinResultados.style.display = 'block';
                actualizarResumen([]);
                return;
            }
            
            // Agrupar compras por ID_COMPRA para mostrar mejor
            const comprasAgrupadas = {};
            compras.forEach(compra => {
                if (!comprasAgrupadas[compra.ID_COMPRA]) {
                    comprasAgrupadas[compra.ID_COMPRA] = {
                        info: compra,
                        detalles: []
                    };
                }
                comprasAgrupadas[compra.ID_COMPRA].detalles.push(compra);
            });
            
            let html = '';
            Object.keys(comprasAgrupadas).forEach(idCompra => {
                const compra = comprasAgrupadas[idCompra];
                
                // Mostrar cada detalle de la compra
                compra.detalles.forEach((detalle, index) => {
                    html += `
                        <tr>
                            ${index === 0 ? `
                            <td>
                                <strong>${detalle.PROVEEDOR}</strong>
                            </td>
                            <td>
                                ${detalle.USUARIO}
                            </td>
                            ` : `
                            <td></td>
                            <td></td>
                            `}
                            <td>${detalle.MATERIA_PRIMA}</td>
                            <td>${parseFloat(detalle.CANTIDAD).toFixed(2)}</td>
                            <td>${detalle.UNIDAD}</td>
                            <td>L ${parseFloat(detalle.PRECIO_UNITARIO).toFixed(2)}</td>
                            <td>L ${parseFloat(detalle.SUBTOTAL).toFixed(2)}</td>
                            ${index === 0 ? `
                            <td>${new Date(detalle.FECHA_COMPRA).toLocaleDateString()}</td>
                            <td>
                                <span class="badge ${getBadgeClass(detalle.ESTADO_COMPRA)}">
                                    ${detalle.ESTADO_COMPRA}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <button type="button" class="btn btn-outline-info" onclick="verDetalle(${detalle.ID_COMPRA})" title="Ver detalle completo">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-success" onclick="descargarReporte(${detalle.ID_COMPRA})" title="Descargar PDF">
                                        <i class="bi bi-download"></i>
                                    </button>
                                </div>
                            </td>
                            ` : `
                            <td></td>
                            <td></td>
                            <td></td>
                            `}
                        </tr>
                    `;
                });
            });
            
            tbody.innerHTML = html;
            actualizarResumen(compras);
        }

        function actualizarResumen(compras) {
            if (compras.length === 0) {
                document.getElementById('totalCompras').textContent = '0';
                document.getElementById('montoTotal').textContent = 'L 0.00';
                return;
            }
            
            // Calcular total único de compras (sin duplicados por ID_COMPRA)
            const comprasUnicas = [...new Set(compras.map(c => c.ID_COMPRA))];
            const montoTotal = compras.reduce((sum, compra) => sum + parseFloat(compra.SUBTOTAL), 0);
            
            document.getElementById('totalCompras').textContent = comprasUnicas.length;
            document.getElementById('montoTotal').textContent = 'L ' + montoTotal.toFixed(2);
        }

        function getBadgeClass(estado) {
            switch(estado) {
                case 'ACTIVA': return 'bg-estado-activa';
                case 'ANULADA': return 'bg-estado-anulada';
                case 'PENDIENTE': return 'bg-estado-pendiente';
                default: return 'bg-secondary';
            }
        }

        function limpiarFiltros() {
            document.getElementById('fecha_inicio').value = '';
            document.getElementById('fecha_fin').value = '';
            document.getElementById('id_proveedor').value = '';
            document.getElementById('estado_compra').value = '';
            cargarCompras();
        }

        function verDetalle(idCompra) {
            window.location.href = `/sistema/public/index.php?route=detalle-compra&id_compra=${idCompra}`;
        }

        function descargarReporte(idCompra) {
    // Abrir en nueva ventana para descargar el PDF
    window.open(`/sistema/public/index.php?route=generar-pdf&id_compra=${idCompra}`, '_blank');
}

                function exportarPDF() {
    // Obtener todos los filtros actuales
    const filtros = {
        fecha_inicio: document.getElementById('fecha_inicio').value,
        fecha_fin: document.getElementById('fecha_fin').value,
        id_proveedor: document.getElementById('id_proveedor').value,
        estado_compra: document.getElementById('estado_compra').value
    };
    
    // Construir URL para exportar PDF con los mismos filtros
    const queryParams = new URLSearchParams();
    Object.keys(filtros).forEach(key => {
        if (filtros[key]) {
            queryParams.append(key, filtros[key]);
        }
    });
    
    // Abrir en nueva ventana para generar el reporte PDF
    window.open(`/sistema/public/index.php?route=reporte-compras-pdf&${queryParams.toString()}`, '_blank');
}

        function exportarExcel() {
            // Obtener todos los filtros actuales
            const filtros = {
                fecha_inicio: document.getElementById('fecha_inicio').value,
                fecha_fin: document.getElementById('fecha_fin').value,
                id_proveedor: document.getElementById('id_proveedor').value,
                estado_compra: document.getElementById('estado_compra').value
            };
            
            // Construir URL para exportar Excel con los mismos filtros
            const queryParams = new URLSearchParams();
            Object.keys(filtros).forEach(key => {
                if (filtros[key]) {
                    queryParams.append(key, filtros[key]);
                }
            });
            
            // Aquí llamarías a tu endpoint para generar Excel
            alert('Exportando a Excel con los filtros actuales...');
            // window.open(`/sistema/public/generar_excel_compras.php?${queryParams.toString()}`, '_blank');
        }
    </script>
    <?php require_once dirname(__DIR__) . '/partials/footer.php'; ?>
</body>
</html>