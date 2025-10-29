<?php
// src/Views/compras/gestion-proveedores.php
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Gestión de Proveedores - Sistema de Gestión</title>
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    
    <style>
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            border: none;
        }
        
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px 10px 0 0 !important;
            padding: 20px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
        }
        
        .btn-success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
        }
        
        .btn-warning {
            background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
            border: none;
        }
        
        .btn-danger {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            border: none;
        }
        
        .badge-activo {
            background-color: #28a745;
            color: white;
        }
        
        .badge-inactivo {
            background-color: #6c757d;
            color: white;
        }
        
        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }
        
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
        
        .btn-group-sm .btn {
            padding: 0.2rem 0.4rem;
            font-size: 0.75rem;
            border-radius: 0.2rem;
        }
    </style>
</head>

<body>
    <?php require_once dirname(__DIR__) . '/partials/header.php'; ?>
    <?php require_once dirname(__DIR__) . '/partials/sidebar.php'; ?>
    
    <main id="main" class="main">
        <div class="container-fluid">
            
            <!-- Header -->
            <div class="page-header mb-4">
                <div class="d-flex justify-content-between align-items-center">
                    <h1 class="h2 mb-0">Gestión de Proveedores</h1>
                    <div class="d-flex gap-2">
                        <a href="/sistema/public/registrar-proveedor" class="btn btn-success">
                            <i class="bi bi-building-add"></i> Nuevo Proveedor
                        </a>
                          <button id="btnExportarPDF" class="btn btn-danger">
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
                                        <label for="filtro_nombre" class="form-label">Nombre del Proveedor</label>
                                        <input type="text" class="form-control" id="filtro_nombre" 
                                               placeholder="Buscar por nombre...">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="filtro_estado" class="form-label">Estado</label>
                                        <select class="form-select" id="filtro_estado">
                                            <option value="">Todos los estados</option>
                                            <option value="ACTIVO">Activo</option>
                                            <option value="INACTIVO">Inactivo</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-primary w-100" onclick="cargarProveedores()">
                                            <i class="bi bi-search"></i> Buscar
                                        </button>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-outline-secondary w-100" onclick="limpiarFiltros()">
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
                        <p class="text-muted mt-2">Cargando proveedores...</p>
                    </div>

                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table table-hover table-striped" id="tablaProveedores">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Contacto</th>
                                    <th>Teléfono</th>
                                    <th>Correo</th>
                                    <th>Dirección</th>
                                    <th>Estado</th>
                                    <th>Fecha Creación</th>
                                    <th>Creado Por</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyProveedores">
                                <!-- Los proveedores se cargarán aquí dinámicamente -->
                            </tbody>
                        </table>
                    </div>

                    <!-- No Results -->
                    <div class="text-center mt-4" id="sinResultados" style="display: none;">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            No se encontraron proveedores con los filtros aplicados.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal Cambiar Estado -->
    <div class="modal fade" id="modalCambiarEstado" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCambiarEstadoTitle">Cambiar Estado del Proveedor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="cambiar_estado_id_proveedor">
                    <p id="modalCambiarEstadoMensaje"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btnConfirmarCambioEstado">Confirmar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>

    <script>
        let tablaProveedores = null;

        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('btnExportarPDF').addEventListener('click', exportarPDF);
            // Inicializar DataTables con configuración básica
            inicializarDataTable();
            
            // Cargar proveedores automáticamente al iniciar
            cargarProveedores();
            
            // Escuchar cambios en los filtros para búsqueda automática
            document.getElementById('filtro_nombre').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    cargarProveedores();
                }
            });
            
            document.getElementById('filtro_estado').addEventListener('change', cargarProveedores);
        });

        function inicializarDataTable() {
            tablaProveedores = $('#tablaProveedores').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
                },
                pageLength: 10,
                responsive: true,
                order: [[0, 'asc']], // Ordenar por nombre (columna 0)
                searching: false, // Desactivar búsqueda global de DataTables (usamos nuestros filtros)
                info: true,
                paging: true,
                autoWidth: false,
                columns: [
                    { data: 'nombre' },      // Columna 0: Nombre
                    { data: 'contacto' },    // Columna 1: Contacto
                    { data: 'telefono' },    // Columna 2: Teléfono
                    { data: 'correo' },      // Columna 3: Correo
                    { data: 'direccion' },   // Columna 4: Dirección
                    { data: 'estado' },      // Columna 5: Estado
                    { data: 'fecha_creacion' }, // Columna 6: Fecha Creación
                    { data: 'creado_por' },  // Columna 7: Creado Por
                    { 
                        data: 'acciones',    // Columna 8: Acciones
                        orderable: false,    // No ordenable
                        searchable: false    // No buscable
                    }
                ],
                // Deshabilitar la funcionalidad de búsqueda interna de DataTables
                // ya que tenemos nuestros propios filtros
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>><"row"<"col-sm-12"tr>><"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>'
            });
        }

        function cargarProveedores() {
            const loading = document.getElementById('loading');
            const sinResultados = document.getElementById('sinResultados');
            
            loading.style.display = 'block';
            sinResultados.style.display = 'none';
            
            // Limpiar la tabla
            if (tablaProveedores) {
                tablaProveedores.clear().draw();
            }
            
            // Obtener filtros
            const filtros = {
                filtro_nombre: document.getElementById('filtro_nombre').value,
                filtro_estado: document.getElementById('filtro_estado').value
            };
            
            // Construir query string
            const queryParams = new URLSearchParams();
            Object.keys(filtros).forEach(key => {
                if (filtros[key]) {
                    queryParams.append(key, filtros[key]);
                }
            });
            
            fetch(`/sistema/public/index.php?route=compras&caso=listarProveedores&${queryParams.toString()}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error en la respuesta del servidor: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                if (data.status === 200 && data.data && data.data.length > 0) {
                    mostrarProveedores(data.data);
                } else {
                    mostrarSinResultados();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarError('Error al cargar los proveedores: ' + error.message);
            })
            .finally(() => {
                loading.style.display = 'none';
            });
        }

        function mostrarProveedores(proveedores) {
            const sinResultados = document.getElementById('sinResultados');
            sinResultados.style.display = 'none';
            
            // Preparar datos para DataTables
            const datosProveedores = proveedores.map(proveedor => {
                return {
                    nombre: `<strong>${proveedor.NOMBRE}</strong>`,
                    contacto: proveedor.CONTACTO || '-',
                    telefono: proveedor.TELEFONO || '-',
                    correo: proveedor.CORREO || '-',
                    direccion: proveedor.DIRECCION || '-',
                    estado: `<span class="badge ${proveedor.ESTADO === 'ACTIVO' ? 'badge-activo' : 'badge-inactivo'}">${proveedor.ESTADO}</span>`,
                    fecha_creacion: proveedor.FECHA_CREACION_FORMATEADA,
                    creado_por: proveedor.CREADO_POR || 'SISTEMA',
                    acciones: `
                        <div class="btn-group btn-group-sm" role="group">
                            ${proveedor.ESTADO === 'ACTIVO' ? 
                                `<button type="button" class="btn btn-outline-warning" onclick="cambiarEstado(${proveedor.ID_PROVEEDOR}, 'INACTIVO', '${proveedor.NOMBRE.replace(/'/g, "\\'")}')" title="Desactivar Proveedor">
                                    <i class="bi bi-pause-circle"></i>
                                </button>` :
                                `<button type="button" class="btn btn-outline-success" onclick="cambiarEstado(${proveedor.ID_PROVEEDOR}, 'ACTIVO', '${proveedor.NOMBRE.replace(/'/g, "\\'")}')" title="Activar Proveedor">
                                    <i class="bi bi-play-circle"></i>
                                </button>`
                            }
                            <button type="button" class="btn btn-outline-info" onclick="editarProveedor(${proveedor.ID_PROVEEDOR})" title="Editar Proveedor">
                                <i class="bi bi-pencil"></i>
                            </button>
                        </div>
                    `
                };
            });
            
            // Agregar datos a DataTables
            tablaProveedores.rows.add(datosProveedores).draw();
        }

        function mostrarSinResultados() {
            const sinResultados = document.getElementById('sinResultados');
            sinResultados.style.display = 'block';
            
            if (tablaProveedores) {
                tablaProveedores.clear().draw();
            }
        }

        function mostrarError(mensaje) {
            const sinResultados = document.getElementById('sinResultados');
            sinResultados.innerHTML = `
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    ${mensaje}
                </div>
            `;
            sinResultados.style.display = 'block';
            
            if (tablaProveedores) {
                tablaProveedores.clear().draw();
            }
        }

        function cambiarEstado(idProveedor, nuevoEstado, nombreProveedor) {
            const modal = new bootstrap.Modal(document.getElementById('modalCambiarEstado'));
            const titulo = document.getElementById('modalCambiarEstadoTitle');
            const mensaje = document.getElementById('modalCambiarEstadoMensaje');
            const btnConfirmar = document.getElementById('btnConfirmarCambioEstado');
            
            document.getElementById('cambiar_estado_id_proveedor').value = idProveedor;
            
            const accion = nuevoEstado === 'ACTIVO' ? 'activar' : 'desactivar';
            titulo.textContent = `${nuevoEstado === 'ACTIVO' ? 'Activar' : 'Desactivar'} Proveedor`;
            mensaje.innerHTML = `¿Está seguro que desea <strong>${accion}</strong> al proveedor: <strong>${nombreProveedor}</strong>?`;
            
            // Configurar el evento del botón confirmar
            btnConfirmar.onclick = function() {
                confirmarCambioEstado(idProveedor, nuevoEstado, modal);
            };
            
            modal.show();
        }

        function confirmarCambioEstado(idProveedor, nuevoEstado, modal) {
            fetch('/sistema/public/index.php?route=compras&caso=cambiarEstadoProveedor', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    id_proveedor: idProveedor,
                    estado: nuevoEstado
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 200) {
                    alert(data.message);
                    modal.hide();
                    cargarProveedores(); // Recargar la lista
                } else {
                    alert(data.message || 'Error al cambiar el estado del proveedor');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error de conexión. Intente nuevamente.');
            });
        }

        function editarProveedor(idProveedor) {
    // Redirigir al formulario de edición
    window.location.href = `/sistema/public/editar-proveedor?id=${idProveedor}`;
}

        function limpiarFiltros() {
            document.getElementById('filtro_nombre').value = '';
            document.getElementById('filtro_estado').value = '';
            cargarProveedores();
        }

        // Función para exportar a PDF
function exportarPDF() {
    const btn = document.getElementById('btnExportarPDF');
    const originalText = btn.innerHTML;
    
    // Mostrar loading
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Generando PDF...';
    btn.disabled = true;
    
    // Obtener filtros actuales
    const filtros = {
        filtro_nombre: document.getElementById('filtro_nombre').value,
        filtro_estado: document.getElementById('filtro_estado').value
    };
    
    // Construir query string
    const queryParams = new URLSearchParams();
    Object.keys(filtros).forEach(key => {
        if (filtros[key]) {
            queryParams.append(key, filtros[key]);
        }
    });
    
    fetch(`/sistema/public/index.php?route=compras&caso=exportarProveedoresPDF&${queryParams.toString()}`)
    .then(response => response.json())
    .then(data => {
        if (data.status === 200 && data.data && data.data.length > 0) {
            generarPDF(data.data, filtros);
        } else {
            alert('No hay datos para exportar con los filtros aplicados.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al generar el PDF: ' + error.message);
    })
    .finally(() => {
        // Restaurar botón
        btn.innerHTML = '<i class="bi bi-file-pdf"></i> Exportar PDF';
        btn.disabled = false;
    });
}

// Función para generar el PDF con el mismo estilo que usuarios
function generarPDF(proveedores, filtros) {
    // Crear contenido HTML para el PDF
    const contenidoHTML = crearContenidoPDF(proveedores, filtros);
    
    // Crear elemento temporal
    const element = document.createElement('div');
    element.innerHTML = contenidoHTML;
    
    // Configuración para html2pdf
    const opt = {
        margin: [10, 10, 10, 10],
        filename: `reporte_proveedores_${new Date().toISOString().split('T')[0]}.pdf`,
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { 
            scale: 2,
            useCORS: true,
            logging: true
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
            console.log('PDF generado y descargado exitosamente');
        })
        .catch(error => {
            console.error('Error generando PDF:', error);
            // Fallback: abrir en nueva ventana para imprimir
            generarPDFFallback(proveedores, filtros);
        });
}

// Función para crear el contenido del PDF con el mismo estilo
function crearContenidoPDF(proveedores, filtros) {
    // Formatear fecha actual
    const fechaActual = new Date().toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
    
    // Calcular estadísticas
    const totalProveedores = proveedores.length;
    const activos = proveedores.filter(p => p.ESTADO === 'ACTIVO').length;
    const inactivos = proveedores.filter(p => p.ESTADO === 'INACTIVO').length;
    
    return `
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Reporte de Proveedores</title>
        <style>
            body { 
                font-family: 'Arial', sans-serif; 
                margin: 15px; 
                font-size: 12px; 
                color: #333;
            }
            .header { 
                text-align: center; 
                margin-bottom: 20px; 
                border-bottom: 2px solid #333; 
                padding-bottom: 10px; 
            }
            .title { 
                font-size: 18px; 
                font-weight: bold; 
                color: #2c3e50; 
                margin-bottom: 5px;
            }
            .subtitle { 
                font-size: 12px; 
                color: #7f8c8d; 
                margin-bottom: 5px; 
            }
            .fecha { 
                text-align: right; 
                margin-bottom: 15px; 
                font-size: 10px; 
                color: #666; 
            }
            .resumen { 
                margin-bottom: 15px; 
                font-size: 11px;
                padding: 8px;
                background-color: #f8f9fa;
                border-left: 4px solid #3498db;
            }
            .estadisticas {
                display: flex;
                justify-content: space-between;
                margin-bottom: 15px;
                font-size: 10px;
            }
            .estadistica-item {
                text-align: center;
                padding: 8px;
                background-color: #ecf0f1;
                border-radius: 5px;
                flex: 1;
                margin: 0 5px;
            }
            .estadistica-valor {
                font-size: 16px;
                font-weight: bold;
                color: #2c3e50;
            }
            .estadistica-label {
                font-size: 9px;
                color: #7f8c8d;
            }
            table { 
                width: 100%; 
                border-collapse: collapse; 
                margin-top: 15px; 
                font-size: 9px;
                page-break-inside: auto;
            }
            th { 
                background-color: #34495e; 
                color: white;
                border: 1px solid #2c3e50; 
                padding: 8px; 
                text-align: left; 
                font-weight: bold;
                font-size: 10px;
            }
            td { 
                border: 1px solid #bdc3c7; 
                padding: 6px; 
                vertical-align: top;
            }
            tr:nth-child(even) {
                background-color: #f8f9fa;
            }
            .estado-activo { 
                background-color: #27ae60; 
                color: white; 
                padding: 3px 6px; 
                border-radius: 3px; 
                font-size: 8px;
                font-weight: bold;
            }
            .estado-inactivo { 
                background-color: #e74c3c; 
                color: white; 
                padding: 3px 6px; 
                border-radius: 3px; 
                font-size: 8px;
                font-weight: bold;
            }
            .footer { 
                margin-top: 20px; 
                text-align: center; 
                font-size: 9px; 
                color: #7f8c8d; 
                border-top: 1px solid #bdc3c7; 
                padding-top: 10px; 
            }
            .total { 
                font-weight: bold; 
                margin-top: 8px; 
                color: #2c3e50;
            }
            .filtros-aplicados {
                background-color: #fff3cd;
                border: 1px solid #ffeaa7;
                border-radius: 5px;
                padding: 8px;
                margin-bottom: 15px;
                font-size: 10px;
            }
            .filtro-item {
                margin: 2px 0;
            }
            @media print {
                body { margin: 0; }
                .header { margin-top: 20px; }
            }
        </style>
    </head>
    <body>
        <div class="header">
            <div class="title">REPORTE DE PROVEEDORES DEL SISTEMA</div>
            <div class="subtitle">Sistema de Gestión de Compras y Proveedores</div>
        </div>
        
        <div class="fecha">
            Generado el: ${fechaActual}
        </div>
        
        ${obtenerFiltrosHTML(filtros)}
        
        <div class="estadisticas">
            <div class="estadistica-item">
                <div class="estadistica-valor">${totalProveedores}</div>
                <div class="estadistica-label">TOTAL PROVEEDORES</div>
            </div>
            <div class="estadistica-item">
                <div class="estadistica-valor">${activos}</div>
                <div class="estadistica-label">PROVEEDORES ACTIVOS</div>
            </div>
            <div class="estadistica-item">
                <div class="estadistica-valor">${inactivos}</div>
                <div class="estadistica-label">PROVEEDORES INACTIVOS</div>
            </div>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th width="15%">Nombre</th>
                    <th width="12%">Contacto</th>
                    <th width="10%">Teléfono</th>
                    <th width="15%">Correo Electrónico</th>
                    <th width="18%">Dirección</th>
                    <th width="8%">Estado</th>
                    <th width="10%">Fecha Creación</th>
                    <th width="7%">Creado Por</th>
                </tr>
            </thead>
            <tbody>
                ${proveedores.map((proveedor, index) => {
                    const estadoClass = proveedor.ESTADO === 'ACTIVO' ? 'estado-activo' : 'estado-inactivo';
                    
                    return `
                    <tr>
                        <td>${index + 1}</td>
                        <td><strong>${proveedor.NOMBRE || 'N/A'}</strong></td>
                        <td>${proveedor.CONTACTO || 'N/A'}</td>
                        <td>${proveedor.TELEFONO || 'N/A'}</td>
                        <td>${proveedor.CORREO || 'N/A'}</td>
                        <td>${proveedor.DIRECCION || 'N/A'}</td>
                        <td><span class="${estadoClass}">${proveedor.ESTADO || 'N/A'}</span></td>
                        <td>${formatearFechaPDF(proveedor.FECHA_CREACION)}</td>
                        <td>${proveedor.CREADO_POR || 'SISTEMA'}</td>
                    </tr>
                    `;
                }).join('')}
            </tbody>
        </table>
        
        <div class="footer">
            <div class="total">Reporte generado automáticamente por el sistema de gestión</div>
            <div>Página 1 de 1</div>
        </div>
    </body>
    </html>
    `;
}

// Función para formatear fechas en el PDF
function formatearFechaPDF(fecha) {
    if (!fecha || fecha === '0000-00-00 00:00:00' || fecha === '0000-00-00') return 'N/A';
    
    try {
        const fechaObj = new Date(fecha);
        if (isNaN(fechaObj.getTime())) return 'N/A';
        return fechaObj.toLocaleDateString('es-ES');
    } catch (e) {
        return 'N/A';
    }
}

// Función para generar el HTML de filtros aplicados
function obtenerFiltrosHTML(filtros) {
    const filtrosAplicados = [];
    
    if (filtros.filtro_nombre) {
        filtrosAplicados.push(`<div class="filtro-item"><strong>Nombre:</strong> ${filtros.filtro_nombre}</div>`);
    }
    
    if (filtros.filtro_estado) {
        const estadoTexto = filtros.filtro_estado === 'ACTIVO' ? 'Activos' : 
                           filtros.filtro_estado === 'INACTIVO' ? 'Inactivos' : filtros.filtro_estado;
        filtrosAplicados.push(`<div class="filtro-item"><strong>Estado:</strong> ${estadoTexto}</div>`);
    }
    
    if (filtrosAplicados.length > 0) {
        return `
            <div class="filtros-aplicados">
                <strong>Filtros Aplicados:</strong>
                ${filtrosAplicados.join('')}
            </div>
        `;
    }
    
    return '<div class="filtros-aplicados"><strong>Filtros Aplicados:</strong> Todos los proveedores</div>';
}

// Método fallback en caso de error con html2pdf
function generarPDFFallback(proveedores, filtros) {
    const ventana = window.open('', '_blank');
    const contenidoHTML = crearContenidoPDF(proveedores, filtros);
    
    ventana.document.write(contenidoHTML);
    ventana.document.close();
    
    setTimeout(() => {
        ventana.print();
    }, 500);
}
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
</body>
</html>