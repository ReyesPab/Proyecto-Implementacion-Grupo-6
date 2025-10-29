<?php
// src/Views/compras/gestion-materia-prima.php

use App\models\comprasModel;

try {
    require_once dirname(__DIR__, 2) . '/models/comprasModel.php';
    $comprasModel = new comprasModel();
    $proveedores = $comprasModel->obtenerProveedores();
} catch (Exception $e) {
    error_log("Error al cargar datos para gestionar materia prima: " . $e->getMessage());
    $proveedores = [];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Gestión de Materia Prima - Sistema de Gestión</title>
    
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
        .btn-outline-warning:hover { background-color: #ffc107; color: black; }
        .btn-outline-danger:hover { background-color: #dc3545; color: white; }

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
        .table td:nth-child(1) { /* NOMBRE */ 
            min-width: 150px;
            max-width: 200px;
        }

        .table td:nth-child(2) { /* DESCRIPCION */
            min-width: 180px;
            max-width: 250px;
        }

        .table td:nth-child(3) { /* PROVEEDOR */
            min-width: 120px;
            max-width: 150px;
        }

        .table td:nth-child(4) { /* UNIDAD */
            min-width: 80px;
            max-width: 100px;
            text-align: center;
        }

        .table td:nth-child(5) { /* MINIMO */
            min-width: 80px;
            max-width: 100px;
            text-align: right;
        }

        .table td:nth-child(6) { /* MAXIMO */
            min-width: 80px;
            max-width: 100px;
            text-align: right;
        }

        .table td:nth-child(7) { /* PRECIO */
            min-width: 100px;
            max-width: 120px;
            text-align: right;
        }

        .table td:nth-child(8) { /* FECHA CREACION */
            min-width: 120px;
            max-width: 130px;
            white-space: nowrap;
        }

        .table td:nth-child(9) { /* ESTADO */
            min-width: 80px;
            max-width: 90px;
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
        .bg-estado-activo {
            background-color: #198754 !important;
        }

        .bg-estado-inactivo {
            background-color: #6c757d !important;
        }

        /* Price styling */
        .price {
            font-weight: 600;
            color: #198754;
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
                    <h1 class="h2 mb-0">Gestión de Materia Prima</h1>
                    <div class="d-flex gap-2">
                        <!-- Botón Registrar Materia Prima -->
                        <a href="/sistema/public/registrar-materia-prima" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Nueva Materia Prima
                        </a>
                        
                        <!-- Botón Exportar PDF -->
                        <button class="btn btn-success" onclick="exportarPDF()">
                            <i class="bi bi-file-pdf"></i> Exportar PDF
                        </button>
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
                                    <div class="col-md-4">
                                        <label for="filtro_nombre" class="form-label">Nombre de Materia Prima</label>
                                        <input type="text" class="form-control" id="filtro_nombre" 
                                               placeholder="Buscar por nombre...">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="filtro_proveedor" class="form-label">Proveedor</label>
                                        <select class="form-select" id="filtro_proveedor">
                                            <option value="">Todos los proveedores</option>
                                            <?php if (!empty($proveedores)): ?>
                                                <?php foreach ($proveedores as $proveedor): ?>
                                                    <option value="<?php echo $proveedor['NOMBRE']; ?>">
                                                        <?php echo htmlspecialchars($proveedor['NOMBRE']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <option value="">No hay proveedores disponibles</option>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-primary w-100" onclick="cargarMateriaPrima()">
                                            <i class="bi bi-search"></i> Buscar
                                        </button>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-refresh w-100" onclick="limpiarFiltros()" title="Limpiar filtros">
                                            <i class="bi bi-arrow-clockwise"></i> Limpiar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Loading -->
                    <div class="loading" id="loading">
                        <div class="loading-spinner"></div>
                        <p class="text-muted mt-2">Cargando materia prima...</p>
                    </div>

                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table table-hover" id="tablaMateriaPrima">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Descripción</th>
                                    <th>Proveedor</th>
                                    <th>Unidad</th>
                                    <th>Mínimo</th>
                                    <th>Máximo</th>
                                    <th>Precio Promedio</th>
                                    <th>Fecha Creación</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyMateriaPrima">
                                <!-- La materia prima se cargará aquí dinámicamente -->
                            </tbody>
                        </table>
                    </div>

                    <!-- No Results -->
                    <div class="text-center mt-4" id="sinResultados" style="display: none;">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            No se encontró materia prima con los filtros aplicados.
                        </div>
                    </div>

                    <!-- Resumen -->
                    <div class="row mt-4">
                        <div class="col-md-4">
                            <div class="card resumen-card">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <i class="bi bi-graph-up me-2"></i>Resumen de Materia Prima
                                    </h6>
                                    <div id="resumenMateriaPrima">
                                        <p>Total registros: <strong id="totalRegistros">0</strong></p>
                                        <p>Valor total: <strong id="valorTotal">L 0.00</strong></p>
                                        <p>Proveedores: <strong id="totalProveedores">0</strong></p>
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
            // Cargar materia prima automáticamente al iniciar
            cargarMateriaPrima();
            
            // Escuchar cambios en los filtros para búsqueda automática
            document.getElementById('filtro_nombre').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    cargarMateriaPrima();
                }
            });
            
            document.getElementById('filtro_proveedor').addEventListener('change', cargarMateriaPrima);
        });

        function cargarMateriaPrima() {
            const loading = document.getElementById('loading');
            const tbody = document.getElementById('tbodyMateriaPrima');
            const sinResultados = document.getElementById('sinResultados');
            
            loading.style.display = 'block';
            tbody.innerHTML = '';
            sinResultados.style.display = 'none';
            
            // Obtener filtros
            const filtros = {
                filtro_nombre: document.getElementById('filtro_nombre').value,
                filtro_proveedor: document.getElementById('filtro_proveedor').value
            };
            
            // Construir query string
            const queryParams = new URLSearchParams();
            Object.keys(filtros).forEach(key => {
                if (filtros[key]) {
                    queryParams.append(key, filtros[key]);
                }
            });
            
            fetch(`/sistema/public/index.php?route=compras&caso=listarMateriaPrima&${queryParams.toString()}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error en la respuesta del servidor: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                console.log('Datos recibidos:', data);
                
                if (data.status === 200 && data.data && data.data.length > 0) {
                    mostrarMateriaPrima(data.data);
                } else {
                    tbody.innerHTML = '';
                    sinResultados.style.display = 'block';
                    actualizarResumen([]);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                tbody.innerHTML = '<tr><td colspan="10" class="text-center text-danger">Error al cargar la materia prima: ' + error.message + '</td></tr>';
                actualizarResumen([]);
            })
            .finally(() => {
                loading.style.display = 'none';
            });
        }

        function mostrarMateriaPrima(materiaPrima) {
            const tbody = document.getElementById('tbodyMateriaPrima');
            const sinResultados = document.getElementById('sinResultados');
            
            if (materiaPrima.length === 0) {
                tbody.innerHTML = '';
                sinResultados.style.display = 'block';
                actualizarResumen([]);
                return;
            }
            
            let html = '';
            materiaPrima.forEach(item => {
                html += `
                    <tr>
                        <td>
                            <strong>${item.NOMBRE}</strong>
                        </td>
                        <td>
                            ${item.DESCRIPCION || '-'}
                        </td>
                        <td>
                            ${item.PROVEEDOR || 'Sin proveedor'}
                        </td>
                        <td class="text-center">
                            <span class="badge bg-secondary">${item.UNIDAD}</span>
                        </td>
                        <td class="text-end">
                            ${parseFloat(item.MINIMO).toFixed(2)}
                        </td>
                        <td class="text-end">
                            ${parseFloat(item.MAXIMO).toFixed(2)}
                        </td>
                        <td class="text-end price">
                            L ${parseFloat(item.PRECIO_PROMEDIO).toFixed(2)}
                        </td>
                        <td>
                            ${item.FECHA_CREACION_FORMATEADA}
                        </td>
                        <td class="text-center">
                            <span class="badge ${getBadgeClass(item.ESTADO)}">
                                ${item.ESTADO}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-outline-info" onclick="editarMateriaPrima(${item.ID_MATERIA_PRIMA})" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button type="button" class="btn btn-outline-warning" onclick="cambiarEstado(${item.ID_MATERIA_PRIMA}, 'INACTIVO', '${item.NOMBRE.replace(/'/g, "\\'")}')" title="Desactivar">
                                    <i class="bi bi-pause-circle"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            });
            
            tbody.innerHTML = html;
            actualizarResumen(materiaPrima);
        }

        function actualizarResumen(materiaPrima) {
            if (materiaPrima.length === 0) {
                document.getElementById('totalRegistros').textContent = '0';
                document.getElementById('valorTotal').textContent = 'L 0.00';
                document.getElementById('totalProveedores').textContent = '0';
                return;
            }
            
            // Calcular estadísticas
            const totalRegistros = materiaPrima.length;
            const valorTotal = materiaPrima.reduce((sum, item) => {
                // Usar precio promedio como valor representativo
                return sum + parseFloat(item.PRECIO_PROMEDIO || 0);
            }, 0);
            
            // Contar proveedores únicos
            const proveedoresUnicos = [...new Set(materiaPrima.map(item => item.PROVEEDOR).filter(Boolean))];
            
            document.getElementById('totalRegistros').textContent = totalRegistros;
            document.getElementById('valorTotal').textContent = 'L ' + valorTotal.toFixed(2);
            document.getElementById('totalProveedores').textContent = proveedoresUnicos.length;
        }

        function getBadgeClass(estado) {
            switch(estado) {
                case 'ACTIVO': return 'bg-estado-activo';
                case 'INACTIVO': return 'bg-estado-inactivo';
                default: return 'bg-secondary';
            }
        }

        function limpiarFiltros() {
            document.getElementById('filtro_nombre').value = '';
            document.getElementById('filtro_proveedor').value = '';
            cargarMateriaPrima();
        }

        function editarMateriaPrima(idMateriaPrima) {
            // Redirigir al formulario de edición
            window.location.href = `/sistema/public/editar-materia-prima?id=${idMateriaPrima}`;
        }

        function cambiarEstado(idMateriaPrima, nuevoEstado, nombreMateriaPrima) {
            const confirmacion = confirm(`¿Está seguro que desea ${nuevoEstado === 'INACTIVO' ? 'desactivar' : 'activar'} la materia prima: ${nombreMateriaPrima}?`);
            
            if (confirmacion) {
                // Aquí implementarías la lógica para cambiar el estado
                alert(`Función para ${nuevoEstado === 'INACTIVO' ? 'desactivar' : 'activar'} materia prima será implementada`);
                // fetch('/sistema/public/index.php?route=compras&caso=cambiarEstadoMateriaPrima', {
                //     method: 'POST',
                //     headers: { 'Content-Type': 'application/json' },
                //     body: JSON.stringify({
                //         id_materia_prima: idMateriaPrima,
                //         estado: nuevoEstado
                //     })
                // })
                // .then(response => response.json())
                // .then(data => {
                //     if (data.status === 200) {
                //         alert(data.message);
                //         cargarMateriaPrima();
                //     } else {
                //         alert(data.message);
                //     }
                // })
                // .catch(error => {
                //     console.error('Error:', error);
                //     alert('Error de conexión');
                // });
            }
        }

        function exportarPDF() {
            // Obtener todos los filtros actuales
            const filtros = {
                filtro_nombre: document.getElementById('filtro_nombre').value,
                filtro_proveedor: document.getElementById('filtro_proveedor').value
            };
            
            // Construir URL para exportar PDF con los mismos filtros
            const queryParams = new URLSearchParams();
            Object.keys(filtros).forEach(key => {
                if (filtros[key]) {
                    queryParams.append(key, filtros[key]);
                }
            });
            
            // Abrir en nueva ventana para generar el reporte PDF
            window.open(`/sistema/public/index.php?route=reporte-materia-prima-pdf&${queryParams.toString()}`, '_blank');
        }
    </script>
    
    <?php require_once dirname(__DIR__) . '/partials/footer.php'; ?>
</body>
</html>