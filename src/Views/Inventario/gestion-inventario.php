<?php 
require_once __DIR__ . '/../partials/header.php'; 
require_once __DIR__ . '/../partials/sidebar.php'; 
?>

<main id="main" class="main">
    <div class="pagetitle">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h2">Gestión de Inventario - Materia Prima</h1>
        </div>

        <div class="card">
            <div class="card-body">
                <!-- Controles de ordenamiento y filtros -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="ordenarPor" class="form-label">Ordenar por:</label>
                        <select id="ordenarPor" class="form-select" style="max-width: 200px;">
                            <option value="nombre">Nombre (A-Z)</option>
                            <option value="cantidad">Cantidad (Mayor a menor)</option>
                            <option value="estado">Estado</option>
                            <option value="fecha">Última Actualización</option>
                        </select>
                    </div>
                    <div class="col-md-6 text-end">
                        <button id="btnExportarPDF" class="btn btn-danger me-2">
                            <i class="bi bi-file-pdf"></i> Exportar PDF
                        </button>
                        <button id="btnRefrescar" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-clockwise"></i> Refrescar
                        </button>
                    </div>
                </div>

                <!-- Filtros adicionales -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="filtroEstado" class="form-label">Filtrar por Estado:</label>
                        <select id="filtroEstado" class="form-select">
                            <option value="">Todos los estados</option>
                            <option value="CRITICO">Crítico</option>
                            <option value="BAJO">Bajo</option>
                            <option value="NORMAL">Normal</option>
                            <option value="EXCESO">Exceso</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="filtroBusqueda" class="form-label">Buscar:</label>
                        <input type="text" id="filtroBusqueda" class="form-control" placeholder="Buscar por nombre...">
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button id="btnAplicarFiltros" class="btn btn-primary me-2">Aplicar Filtros</button>
                        <button id="btnLimpiarFiltros" class="btn btn-outline-secondary">Limpiar</button>
                    </div>
                </div>

                <div id="loadingMessage" class="alert alert-info text-center">
                    <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                    Cargando inventario...
                </div>
                <div id="errorMessage" class="alert alert-danger text-center" style="display: none;">
                    Error al cargar el inventario. Verifica la consola para más detalles.
                </div>
                
                <div class="table-responsive">
                    <table id="tablaInventario" class="table table-striped table-bordered" style="display: none; width: 100%;">
                        <thead>
                            <tr>
                                <th>Materia Prima</th>
                                <th>Descripción</th>
                                <th>Unidad</th>
                                <th>Cantidad</th>
                                <th>Mínimo</th>
                                <th>Máximo</th>
                                <th>Estado</th>
                                <th>Última Actualización</th>
                                <th>Actualizado Por</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Los datos se cargan via JavaScript -->
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <nav id="paginacion" style="display: none;">
                    <ul class="pagination justify-content-center">
                        <li class="page-item disabled" id="btnAnterior">
                            <a class="page-link" href="#" tabindex="-1">Anterior</a>
                        </li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item" id="btnSiguiente">
                            <a class="page-link" href="#">Siguiente</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <!-- Modal Ajustar Inventario -->
    <div class="modal fade" id="modalAjustarInventario" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ajustar Inventario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formAjustarInventario">
                        <input type="hidden" id="ajustar_id_materia_prima" name="ID_MATERIA_PRIMA">
                        <input type="hidden" id="ajustar_nombre_materia_prima" name="NOMBRE_MATERIA_PRIMA">
                        
                        <div class="mb-3">
                            <label class="form-label">Materia Prima:</label>
                            <p class="form-control-plaintext fw-bold" id="display_nombre_materia_prima"></p>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Stock Actual:</label>
                            <p class="form-control-plaintext" id="display_stock_actual"></p>
                        </div>
                        
                        <div class="mb-3">
                            <label for="ajustar_tipo_movimiento" class="form-label">Tipo de Movimiento:</label>
                            <select class="form-select" id="ajustar_tipo_movimiento" name="TIPO_MOVIMIENTO" required>
                                <option value="">Seleccionar...</option>
                                <option value="ENTRADA">Entrada</option>
                                <option value="SALIDA">Salida</option>
                                <option value="AJUSTE">Ajuste</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="ajustar_cantidad" class="form-label">Cantidad:</label>
                            <input type="number" class="form-control" id="ajustar_cantidad" name="CANTIDAD" 
                                   step="0.01" min="0.01" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="ajustar_descripcion" class="form-label">Descripción:</label>
                            <textarea class="form-control" id="ajustar_descripcion" name="DESCRIPCION" 
                                      rows="3" required></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btnConfirmarAjuste">Ajustar Inventario</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Ver Historial -->
    <div class="modal fade" id="modalVerHistorial" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Historial de Movimientos</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="historial_id_materia_prima">
                    <p class="fw-bold" id="historial_nombre_materia_prima"></p>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="historial_fecha_inicio" class="form-label">Fecha Inicio:</label>
                            <input type="date" class="form-control" id="historial_fecha_inicio">
                        </div>
                        <div class="col-md-6">
                            <label for="historial_fecha_fin" class="form-label">Fecha Fin:</label>
                            <input type="date" class="form-control" id="historial_fecha_fin">
                        </div>
                    </div>
                    
                    <button type="button" class="btn btn-primary mb-3" id="btnFiltrarHistorial">
                        <i class="bi bi-filter"></i> Filtrar
                    </button>
                    
                    <div class="table-responsive">
                        <table class="table table-sm table-striped" id="tablaHistorial">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Tipo</th>
                                    <th>Cantidad</th>
                                    <th>Usuario</th>
                                    <th>Descripción</th>
                                </tr>
                            </thead>
                            <tbody id="cuerpoHistorial">
                                <!-- Historial se carga aquí -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Solo jQuery y html2pdf -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<script>
class GestionInventario {
    constructor() {
        this.inventario = [];
        this.inventarioFiltrado = [];
        this.paginaActual = 1;
        this.itemsPorPagina = 10;
        this.init();
    }

    async init() {
        await this.cargarInventario();
        this.configurarEventos();
    }

    async cargarInventario() {
        try {
            console.log("🔍 Iniciando carga de inventario...");
            
            const response = await fetch('index.php?route=inventario&caso=listar');
            console.log("📦 Respuesta HTTP:", response.status, response.statusText);
            
            if (!response.ok) {
                throw new Error(`Error HTTP: ${response.status} - ${response.statusText}`);
            }
            
            const text = await response.text();
            console.log("📄 Respuesta cruda:", text);
            
            let data;
            try {
                data = JSON.parse(text);
            } catch (e) {
                console.error("❌ Error parseando JSON:", e);
                throw new Error("Respuesta no es JSON válido");
            }
            
            console.log("📊 Datos JSON recibidos:", data);
            
            if (data && data.status === 200 && data.data && data.data.inventario) {
                console.log("📦 Items en inventario:", data.data.inventario.length);
                this.inventario = data.data.inventario;
                this.inventarioFiltrado = [...this.inventario];
                this.mostrarInventario();
            } else {
                console.error("❌ Estructura de datos inesperada:", data);
                throw new Error("Estructura de respuesta inesperada");
            }
            
        } catch (error) {
            console.error('❌ Error cargando inventario:', error);
            this.mostrarError(error.message);
        }
    }

    mostrarError(mensaje) {
        const loadingMessage = document.getElementById('loadingMessage');
        const errorMessage = document.getElementById('errorMessage');
        
        loadingMessage.style.display = 'none';
        errorMessage.textContent = `Error: ${mensaje}`;
        errorMessage.style.display = 'block';
    }

    mostrarInventario() {
        const loadingMessage = document.getElementById('loadingMessage');
        const errorMessage = document.getElementById('errorMessage');
        const tabla = document.getElementById('tablaInventario');
        const tbody = tabla.querySelector('tbody');
        const paginacion = document.getElementById('paginacion');

        // Ocultar mensajes
        loadingMessage.style.display = 'none';
        errorMessage.style.display = 'none';

        if (!this.inventarioFiltrado || this.inventarioFiltrado.length === 0) {
            console.log("📭 No hay items en el inventario");
            errorMessage.textContent = "No hay registros en el inventario";
            errorMessage.style.display = 'block';
            tabla.style.display = 'none';
            paginacion.style.display = 'none';
            return;
        }

        console.log("📋 Mostrando", this.inventarioFiltrado.length, "items en la tabla");

        // Calcular paginación
        const totalPaginas = Math.ceil(this.inventarioFiltrado.length / this.itemsPorPagina);
        const inicio = (this.paginaActual - 1) * this.itemsPorPagina;
        const fin = inicio + this.itemsPorPagina;
        const itemsPagina = this.inventarioFiltrado.slice(inicio, fin);

        // Limpiar tabla
        tbody.innerHTML = '';

        // Llenar tabla
        itemsPagina.forEach(item => {
            const estado = item.ESTADO_INVENTARIO || 'N/A';
            let badgeClass = 'bg-secondary';
            let texto = estado;
            
            switch(estado.toUpperCase()) {
                case 'CRITICO':
                    badgeClass = 'bg-danger';
                    texto = 'Crítico';
                    break;
                case 'BAJO':
                    badgeClass = 'bg-warning';
                    texto = 'Bajo';
                    break;
                case 'NORMAL':
                    badgeClass = 'bg-success';
                    texto = 'Normal';
                    break;
                case 'EXCESO':
                    badgeClass = 'bg-info';
                    texto = 'Exceso';
                    break;
            }

            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${item.NOMBRE || 'N/A'}</td>
                <td>${item.DESCRIPCION || 'Sin descripción'}</td>
                <td>${item.UNIDAD || 'N/A'}</td>
                <td class="text-end">${parseFloat(item.CANTIDAD || 0).toFixed(2)}</td>
                <td class="text-end">${parseFloat(item.MINIMO || 0).toFixed(2)}</td>
                <td class="text-end">${parseFloat(item.MAXIMO || 0).toFixed(2)}</td>
                <td><span class="badge ${badgeClass}">${texto}</span></td>
                <td>${item.FECHA_ACTUALIZACION ? new Date(item.FECHA_ACTUALIZACION).toLocaleString('es-ES') : 'N/A'}</td>
                <td>${item.ACTUALIZADO_POR || 'SISTEMA'}</td>
                <td>
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-outline-primary" 
                                onclick="gestionInventario.ajustarInventario(
                                    ${item.ID_MATERIA_PRIMA}, 
                                    '${(item.NOMBRE || '').replace(/'/g, "\\'")}', 
                                    ${item.CANTIDAD || 0}
                                )" 
                                title="Ajustar Inventario">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button type="button" class="btn btn-outline-info" 
                                onclick="gestionInventario.verHistorial(
                                    ${item.ID_MATERIA_PRIMA}, 
                                    '${(item.NOMBRE || '').replace(/'/g, "\\'")}'
                                )" 
                                title="Ver Historial">
                            <i class="bi bi-clock-history"></i>
                        </button>
                    </div>
                </td>
            `;
            tbody.appendChild(row);
        });

        // Actualizar paginación
        this.actualizarPaginacion(totalPaginas);

        // Mostrar elementos
        tabla.style.display = 'table';
        paginacion.style.display = 'block';

        console.log("✅ Tabla de inventario cargada correctamente");
    }

    actualizarPaginacion(totalPaginas) {
        const paginacion = document.getElementById('paginacion');
        const btnAnterior = document.getElementById('btnAnterior');
        const btnSiguiente = document.getElementById('btnSiguiente');
        
        // Actualizar estado de botones
        btnAnterior.classList.toggle('disabled', this.paginaActual === 1);
        btnSiguiente.classList.toggle('disabled', this.paginaActual === totalPaginas);
        
        // Actualizar números de página
        const paginationList = paginacion.querySelector('.pagination');
        let paginationHTML = `
            <li class="page-item ${this.paginaActual === 1 ? 'disabled' : ''}" id="btnAnterior">
                <a class="page-link" href="#" tabindex="-1">Anterior</a>
            </li>
        `;
        
        // Mostrar máximo 5 páginas
        let inicioPagina = Math.max(1, this.paginaActual - 2);
        let finPagina = Math.min(totalPaginas, inicioPagina + 4);
        
        if (finPagina - inicioPagina < 4) {
            inicioPagina = Math.max(1, finPagina - 4);
        }
        
        for (let i = inicioPagina; i <= finPagina; i++) {
            paginationHTML += `
                <li class="page-item ${i === this.paginaActual ? 'active' : ''}">
                    <a class="page-link" href="#" data-pagina="${i}">${i}</a>
                </li>
            `;
        }
        
        paginationHTML += `
            <li class="page-item ${this.paginaActual === totalPaginas ? 'disabled' : ''}" id="btnSiguiente">
                <a class="page-link" href="#">Siguiente</a>
            </li>
        `;
        
        paginationList.innerHTML = paginationHTML;
        
        // Re-asignar eventos
        this.configurarEventosPaginacion();
    }

    configurarEventosPaginacion() {
        document.getElementById('btnAnterior').addEventListener('click', (e) => {
            e.preventDefault();
            if (this.paginaActual > 1) {
                this.paginaActual--;
                this.mostrarInventario();
            }
        });
        
        document.getElementById('btnSiguiente').addEventListener('click', (e) => {
            e.preventDefault();
            const totalPaginas = Math.ceil(this.inventarioFiltrado.length / this.itemsPorPagina);
            if (this.paginaActual < totalPaginas) {
                this.paginaActual++;
                this.mostrarInventario();
            }
        });
        
        // Eventos para números de página
        document.querySelectorAll('.pagination .page-link[data-pagina]').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                this.paginaActual = parseInt(e.target.getAttribute('data-pagina'));
                this.mostrarInventario();
            });
        });
    }

    configurarEventos() {
        // Ordenamiento
        document.getElementById('ordenarPor').addEventListener('change', (e) => this.ordenarInventario(e.target.value));
        document.getElementById('btnRefrescar').addEventListener('click', () => this.recargarInventario());
        
        // Exportación
        document.getElementById('btnExportarPDF').addEventListener('click', () => this.exportarPDF());
        
        // Filtros
        document.getElementById('btnAplicarFiltros').addEventListener('click', () => this.aplicarFiltros());
        document.getElementById('btnLimpiarFiltros').addEventListener('click', () => this.limpiarFiltros());
        
        // Ajuste de inventario
        document.getElementById('btnConfirmarAjuste').addEventListener('click', () => this.confirmarAjuste());
        
        // Historial
        document.getElementById('btnFiltrarHistorial').addEventListener('click', () => this.filtrarHistorial());
        
        // Evento para Enter en búsqueda
        document.getElementById('filtroBusqueda').addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                this.aplicarFiltros();
            }
        });
    }

    ordenarInventario(criterio) {
        switch(criterio) {
            case 'nombre':
                this.inventarioFiltrado.sort((a, b) => (a.NOMBRE || '').localeCompare(b.NOMBRE || ''));
                break;
            case 'cantidad':
                this.inventarioFiltrado.sort((a, b) => (b.CANTIDAD || 0) - (a.CANTIDAD || 0));
                break;
            case 'estado':
                this.inventarioFiltrado.sort((a, b) => (a.ESTADO_INVENTARIO || '').localeCompare(b.ESTADO_INVENTARIO || ''));
                break;
            case 'fecha':
                this.inventarioFiltrado.sort((a, b) => new Date(b.FECHA_ACTUALIZACION || 0) - new Date(a.FECHA_ACTUALIZACION || 0));
                break;
        }
        this.paginaActual = 1;
        this.mostrarInventario();
    }

    aplicarFiltros() {
        const filtroEstado = document.getElementById('filtroEstado').value;
        const filtroBusqueda = document.getElementById('filtroBusqueda').value.toLowerCase();
        
        this.inventarioFiltrado = this.inventario.filter(item => {
            const estadoMatch = !filtroEstado || (item.ESTADO_INVENTARIO || '').toLowerCase() === filtroEstado.toLowerCase();
            const busquedaMatch = !filtroBusqueda || (item.NOMBRE || '').toLowerCase().includes(filtroBusqueda);
            
            return estadoMatch && busquedaMatch;
        });
        
        this.paginaActual = 1;
        this.mostrarInventario();
    }

    limpiarFiltros() {
        document.getElementById('filtroEstado').value = '';
        document.getElementById('filtroBusqueda').value = '';
        this.inventarioFiltrado = [...this.inventario];
        this.paginaActual = 1;
        this.mostrarInventario();
    }

    // ... (el resto de los métodos se mantienen igual que en la versión anterior)
    // Solo copia los métodos: ajustarInventario, confirmarAjuste, mostrarAlerta, 
    // exportarPDF, generarPDF, recargarInventario, verHistorial, filtrarHistorial, mostrarHistorial

    ajustarInventario(idMateriaPrima, nombre, stockActual) {
        document.getElementById('ajustar_id_materia_prima').value = idMateriaPrima;
        document.getElementById('ajustar_nombre_materia_prima').value = nombre;
        document.getElementById('display_nombre_materia_prima').textContent = nombre;
        document.getElementById('display_stock_actual').textContent = parseFloat(stockActual).toFixed(2);
        
        // Limpiar formulario
        document.getElementById('ajustar_tipo_movimiento').value = '';
        document.getElementById('ajustar_cantidad').value = '';
        document.getElementById('ajustar_descripcion').value = '';
        
        const modal = new bootstrap.Modal(document.getElementById('modalAjustarInventario'));
        modal.show();
    }

    async confirmarAjuste() {
        const formData = {
            id_materia_prima: document.getElementById('ajustar_id_materia_prima').value,
            cantidad: parseFloat(document.getElementById('ajustar_cantidad').value),
            tipo_movimiento: document.getElementById('ajustar_tipo_movimiento').value,
            descripcion: document.getElementById('ajustar_descripcion').value.trim(),
            id_usuario: 1,
            actualizado_por: 'ADMIN'
        };

        console.log("📤 Enviando datos:", formData);

        // Validaciones
        if (!formData.tipo_movimiento) {
            this.mostrarAlerta('Seleccione un tipo de movimiento', 'warning');
            return;
        }

        if (!formData.cantidad || formData.cantidad <= 0 || isNaN(formData.cantidad)) {
            this.mostrarAlerta('Ingrese una cantidad válida mayor a 0', 'warning');
            return;
        }

        if (!formData.descripcion) {
            this.mostrarAlerta('Ingrese una descripción para el movimiento', 'warning');
            return;
        }

        try {
            const btnConfirmar = document.getElementById('btnConfirmarAjuste');
            btnConfirmar.disabled = true;
            btnConfirmar.innerHTML = '<i class="bi bi-hourglass-split"></i> Procesando...';

            const response = await fetch('index.php?route=inventario&caso=actualizar', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(formData)
            });

            console.log("📨 Status de respuesta:", response.status);

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const result = await response.json();
            console.log("📊 Resultado JSON:", result);
            
            if (result.status === 200) {
                this.mostrarAlerta(result.message || 'Inventario actualizado correctamente', 'success');
                const modal = bootstrap.Modal.getInstance(document.getElementById('modalAjustarInventario'));
                modal.hide();
                await this.recargarInventario();
            } else {
                this.mostrarAlerta(result.message || 'Error al actualizar el inventario', 'error');
            }
        } catch (error) {
            console.error('❌ Error completo:', error);
            this.mostrarAlerta('Error de conexión: ' + error.message, 'error');
        } finally {
            const btnConfirmar = document.getElementById('btnConfirmarAjuste');
            btnConfirmar.disabled = false;
            btnConfirmar.innerHTML = 'Ajustar Inventario';
        }
    }

    mostrarAlerta(mensaje, tipo = 'info') {
        const alertClass = {
            'success': 'alert-success',
            'error': 'alert-danger',
            'warning': 'alert-warning',
            'info': 'alert-info'
        }[tipo] || 'alert-info';

        // Crear alerta temporal
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert ${alertClass} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            ${mensaje}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        // Insertar al inicio del contenido principal
        const main = document.querySelector('main');
        main.insertBefore(alertDiv, main.firstChild);
        
        // Auto-eliminar después de 5 segundos
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }

    async exportarPDF() {
        try {
            const response = await fetch('index.php?route=inventario&caso=exportar-pdf');
            const result = await response.json();
            
            if (result.status === 200) {
                this.generarPDF(result.data.inventario);
            } else {
                this.mostrarAlerta('Error al exportar el inventario', 'error');
            }
        } catch (error) {
            console.error('Error exportando PDF:', error);
            this.mostrarAlerta('Error de conexión al exportar', 'error');
        }
    }

    generarPDF(inventario) {
    // Formatear fecha actual
    const fechaActual = new Date().toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });

    // Función para formatear fechas
    const formatearFecha = (fecha) => {
        if (!fecha) return 'N/A';
        return new Date(fecha).toLocaleDateString('es-ES');
    };

    // Función para determinar clase del estado
    const getEstadoClass = (estado) => {
        const estadoUpper = estado ? estado.toUpperCase() : '';
        switch(estadoUpper) {
            case 'CRITICO': return 'estado-critico';
            case 'BAJO': return 'estado-bajo';
            case 'NORMAL': return 'estado-normal';
            case 'EXCESO': return 'estado-exceso';
            default: return 'estado-normal';
        }
    };

    const element = document.createElement('div');
    element.innerHTML = `
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Reporte de Inventario - Materia Prima</title>
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
            .estado-critico { 
                background-color: #e74c3c; 
                color: white; 
                padding: 3px 6px; 
                border-radius: 3px; 
                font-size: 8px;
                font-weight: bold;
            }
            .estado-bajo { 
                background-color: #f39c12; 
                color: white; 
                padding: 3px 6px; 
                border-radius: 3px; 
                font-size: 8px;
                font-weight: bold;
            }
            .estado-normal { 
                background-color: #27ae60; 
                color: white; 
                padding: 3px 6px; 
                border-radius: 3px; 
                font-size: 8px;
                font-weight: bold;
            }
            .estado-exceso { 
                background-color: #3498db; 
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
            .text-end { text-align: right; }
            .text-center { text-align: center; }
            @media print {
                body { margin: 0; }
                .header { margin-top: 20px; }
            }
        </style>
    </head>
    <body>
        <div class="header">
            <div class="title">REPORTE DE INVENTARIO - MATERIA PRIMA</div>
            <div class="subtitle">Sistema de Gestión de Inventarios</div>
        </div>
        
        <div class="fecha">
            Generado el: ${fechaActual}
        </div>
        
        <div class="resumen">
            <strong>Total de materiales en inventario: ${inventario.length}</strong>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th width="20%">Materia Prima</th>
                    <th width="10%">Unidad</th>
                    <th width="10%" class="text-end">Cantidad</th>
                    <th width="10%" class="text-end">Mínimo</th>
                    <th width="10%" class="text-end">Máximo</th>
                    <th width="15%">Estado</th>
                </tr>
            </thead>
            <tbody>
                ${inventario.map((item, index) => {
                    const estadoClass = getEstadoClass(item.ESTADO_INVENTARIO);
                    const estadoTexto = item.ESTADO_INVENTARIO || 'NORMAL';
                    
                    return `
                    <tr>
                        <td class="text-center">${index + 1}</td>
                        <td><strong>${item.NOMBRE || 'N/A'}</strong></td>
                        <td class="text-center">${item.UNIDAD || 'N/A'}</td>
                        <td class="text-end">${parseFloat(item.CANTIDAD || 0).toFixed(2)}</td>
                        <td class="text-end">${parseFloat(item.MINIMO || 0).toFixed(2)}</td>
                        <td class="text-end">${parseFloat(item.MAXIMO || 0).toFixed(2)}</td>
                        <td class="text-center"><span class="${estadoClass}">${estadoTexto}</span></td>
                       
                    </tr>
                    `;
                }).join('')}
            </tbody>
        </table>
        
        <div class="footer">
            <div class="total">Reporte generado automáticamente por el sistema de gestión de inventarios</div>
            <div>Página 1 de 1</div>
        </div>
    </body>
    </html>
    `;

    const opt = {
        margin: 1,
        filename: `inventario_materia_prima_${new Date().toISOString().split('T')[0]}.pdf`,
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2 },
        jsPDF: { unit: 'cm', format: 'a4', orientation: 'portrait' }
    };

    html2pdf().set(opt).from(element).save();
}

    async recargarInventario() {
        const loadingMessage = document.getElementById('loadingMessage');
        const errorMessage = document.getElementById('errorMessage');
        const tabla = document.getElementById('tablaInventario');
        const paginacion = document.getElementById('paginacion');
        
        loadingMessage.style.display = 'block';
        errorMessage.style.display = 'none';
        tabla.style.display = 'none';
        paginacion.style.display = 'none';
        
        await this.cargarInventario();
    }

   async verHistorial(idMateriaPrima, nombre) {
    document.getElementById('historial_id_materia_prima').value = idMateriaPrima;
    document.getElementById('historial_nombre_materia_prima').textContent = nombre;
    
    // Limpiar fechas
    const hoy = new Date();
    const haceUnMes = new Date();
    haceUnMes.setMonth(hoy.getMonth() - 1);
    
    document.getElementById('historial_fecha_inicio').value = haceUnMes.toISOString().split('T')[0];
    document.getElementById('historial_fecha_fin').value = hoy.toISOString().split('T')[0];
    
    // Cargar historial inicial (último mes)
    await this.filtrarHistorial();
    
    const modal = new bootstrap.Modal(document.getElementById('modalVerHistorial'));
    modal.show();
}

async filtrarHistorial() {
    const idMateriaPrima = document.getElementById('historial_id_materia_prima').value;
    const fechaInicio = document.getElementById('historial_fecha_inicio').value;
    const fechaFin = document.getElementById('historial_fecha_fin').value;
    
    console.log("🔍 Filtrando historial:", { idMateriaPrima, fechaInicio, fechaFin });
    
    try {
        // Construir parámetros de consulta
        let params = `id_materia_prima=${idMateriaPrima}`;
        
        if (fechaInicio) {
            params += `&fecha_inicio=${fechaInicio}`;
        }
        
        if (fechaFin) {
            params += `&fecha_fin=${fechaFin}`;
        }
        
        console.log("📤 Parámetros de consulta:", params);
        
        const response = await fetch(`index.php?route=inventario&caso=historial&${params}`);
        console.log("📦 Respuesta HTTP:", response.status);
        
        if (!response.ok) {
            throw new Error(`Error HTTP: ${response.status}`);
        }
        
        const result = await response.json();
        console.log("📊 Resultado historial:", result);
        
        if (result.status === 200 && result.data && result.data.historial) {
            this.mostrarHistorial(result.data.historial);
            this.mostrarAlerta(`Se encontraron ${result.data.historial.length} movimientos`, 'success');
        } else if (result.status === 200 && result.data && Array.isArray(result.data)) {
            // Si la respuesta viene en formato diferente
            this.mostrarHistorial(result.data);
            this.mostrarAlerta(`Se encontraron ${result.data.length} movimientos`, 'success');
        } else {
            console.warn("⚠️ Estructura de respuesta inesperada:", result);
            this.mostrarAlerta(result.message || 'No se encontraron movimientos', 'info');
            this.mostrarHistorial([]);
        }
    } catch (error) {
        console.error('❌ Error cargando historial:', error);
        
        // Mostrar datos de ejemplo para debugging
        if (error.message.includes('JSON')) {
            const text = await response.text();
            console.error('📄 Respuesta no JSON:', text);
        }
        
        this.mostrarAlerta('Error de conexión al cargar historial: ' + error.message, 'error');
        this.mostrarHistorial([]);
    }
}

mostrarHistorial(historial) {
    const cuerpoHistorial = document.getElementById('cuerpoHistorial');
    
    if (!historial || historial.length === 0) {
        cuerpoHistorial.innerHTML = `
            <tr>
                <td colspan="5" class="text-center text-muted py-3">
                    <i class="bi bi-inbox"></i><br>
                    No hay movimientos registrados en el período seleccionado
                </td>
            </tr>
        `;
        return;
    }
    
    // Ordenar por fecha más reciente primero
    historial.sort((a, b) => new Date(b.FECHA_MOVIMIENTO) - new Date(a.FECHA_MOVIMIENTO));
    
    cuerpoHistorial.innerHTML = historial.map(movimiento => `
        <tr>
            <td>${movimiento.FECHA_MOVIMIENTO ? new Date(movimiento.FECHA_MOVIMIENTO).toLocaleString('es-ES') : 'N/A'}</td>
            <td>
                <span class="badge ${
                    movimiento.TIPO_MOVIMIENTO === 'ENTRADA' ? 'bg-success' : 
                    movimiento.TIPO_MOVIMIENTO === 'SALIDA' ? 'bg-danger' : 'bg-info'
                }">
                    ${movimiento.TIPO_MOVIMIENTO || 'N/A'}
                </span>
            </td>
            <td class="text-end">${parseFloat(movimiento.CANTIDAD || 0).toFixed(2)}</td>
            <td>${movimiento.USUARIO || movimiento.CREADO_POR || 'SISTEMA'}</td>
            <td>${movimiento.DESCRIPCION || 'Sin descripción'}</td>
        </tr>
    `).join('');
    
    console.log("✅ Historial mostrado:", historial.length, "movimientos");
}
}

// Inicializar la gestión de inventario cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    window.gestionInventario = new GestionInventario();
});
</script>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>