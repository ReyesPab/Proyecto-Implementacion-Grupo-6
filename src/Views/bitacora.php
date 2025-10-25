<?php require_once 'partials/header.php'; ?>
<?php require_once 'partials/sidebar.php'; ?>

<main id="main" class="main">
    <div class="pagetitle">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h2">Bitácora del Sistema</h1>
            <div class="d-flex gap-2">
                <button id="btnExportar" class="btn btn-success">
                    <i class="bi bi-file-earmark-excel"></i> Exportar
                </button>
                <button id="btnLimpiarFiltros" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-clockwise"></i> Limpiar Filtros
                </button>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <!-- Filtros -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label for="filtroUsuario" class="form-label">Usuario</label>
                        <select id="filtroUsuario" class="form-select">
                            <option value="">Todos los usuarios</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="filtroAccion" class="form-label">Acción</label>
                        <select id="filtroAccion" class="form-select">
                            <option value="">Todas las acciones</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="filtroFechaInicio" class="form-label">Fecha Inicio</label>
                        <input type="date" id="filtroFechaInicio" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label for="filtroFechaFin" class="form-label">Fecha Fin</label>
                        <input type="date" id="filtroFechaFin" class="form-control">
                    </div>
                </div>

                <!-- Buscador -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="input-group">
                            <input type="text" id="buscadorGlobal" class="form-control" placeholder="Buscar en bitácora...">
                            <button class="btn btn-outline-primary" type="button" id="btnBuscar">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select id="registrosPorPagina" class="form-select">
                            <option value="10">10 registros</option>
                            <option value="25">25 registros</option>
                            <option value="50">50 registros</option>
                            <option value="100" selected>100 registros</option>
                            <option value="200">200 registros</option>
                        </select>
                    </div>
                </div>

                <!-- Mensajes -->
                <div id="loadingMessage" class="alert alert-info text-center">
                    <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                    Cargando bitácora...
                </div>
                <div id="errorMessage" class="alert alert-danger text-center" style="display: none;">
                    Error al cargar la bitácora. Verifica la consola para más detalles.
                </div>
                
                <!-- Tabla -->
                <table id="tablaBitacora" class="table table-striped table-bordered" style="display: none;">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Fecha y Hora</th>
                            <th>Usuario</th>
                            <th>Acción</th>
                            <th>Descripción</th>
                            <th>Objeto</th>
                            <th>Creado Por</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Los datos se cargan via JavaScript -->
                    </tbody>
                </table>

                <!-- Paginación -->
                <div id="paginacion" class="d-flex justify-content-between align-items-center mt-3" style="display: none;">
                    <div id="infoPaginacion" class="text-muted"></div>
                    <nav>
                        <ul id="paginacionLista" class="pagination mb-0">
                            <!-- Los números de página se generan dinámicamente -->
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Vendor JS Files -->
<script src="/sistema/src/Views/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- Main JS File -->
<script src="/sistema/src/Views/assets/js/main.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

<script>
    // Función de depuración para ver la respuesta real
async function debugBitacora() {
    try {
        console.log("🔍 [DEBUG] Probando endpoint de bitácora...");
        const response = await fetch('index.php?route=user&caso=obtener-bitacora');
        const text = await response.text();
        console.log("📄 [DEBUG] Respuesta cruda:", text);
        
        try {
            const data = JSON.parse(text);
            console.log("📊 [DEBUG] Estructura JSON:", data);
            console.log("🔍 [DEBUG] Keys del objeto:", Object.keys(data));
            if (data.data) {
                console.log("📋 [DEBUG] Keys de data:", Object.keys(data.data));
                console.log("📝 [DEBUG] Tipo de data.bitacora:", typeof data.data.bitacora);
                if (data.data.bitacora) {
                    console.log("✅ [DEBUG] data.bitacora es array:", Array.isArray(data.data.bitacora));
                    console.log("📊 [DEBUG] Número de registros:", data.data.bitacora.length);
                }
            }
        } catch (e) {
            console.error("❌ [DEBUG] Error parseando JSON:", e);
        }
    } catch (error) {
        console.error("❌ [DEBUG] Error en fetch:", error);
    }
}

// Ejecutar depuración al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    debugBitacora();
});
class BitacoraSistema {
    constructor() {
        this.datosBitacora = [];
        this.paginaActual = 1;
        this.registrosPorPagina = 100;
        this.filtros = {
            usuario: '',
            accion: '',
            fechaInicio: '',
            fechaFin: '',
            busqueda: ''
        };
        this.init();
    }

    async init() {
        await this.cargarFiltros();
        await this.cargarBitacora();
        this.configurarEventos();
    }

    async cargarFiltros() {
        try {
            // Cargar usuarios para filtro
            const responseUsuarios = await fetch('index.php?route=user&caso=listar');
            const dataUsuarios = await responseUsuarios.json();
            
            if (dataUsuarios.status === 200 && dataUsuarios.data && dataUsuarios.data.usuarios) {
                const selectUsuario = document.getElementById('filtroUsuario');
                dataUsuarios.data.usuarios.forEach(usuario => {
                    const option = document.createElement('option');
                    option.value = usuario.USUARIO;
                    option.textContent = `${usuario.USUARIO} - ${usuario.NOMBRE_USUARIO}`;
                    selectUsuario.appendChild(option);
                });
            }

            // Cargar acciones únicas (se cargarán después de obtener los datos)
        } catch (error) {
            console.error('Error cargando filtros:', error);
        }
    }

// En la clase BitacoraSistema, modificar el método cargarBitacora:
async cargarBitacora() {
    try {
        console.log("🔍 Iniciando carga de bitácora...");
        
        const loadingMessage = document.getElementById('loadingMessage');
        const errorMessage = document.getElementById('errorMessage');
        
        // Mostrar loading
        loadingMessage.style.display = 'block';
        errorMessage.style.display = 'none';
        
        const response = await fetch('index.php?route=user&caso=obtener-bitacora');
        console.log("📦 Respuesta HTTP:", response.status, response.statusText);
        
        if (!response.ok) {
            throw new Error(`Error HTTP: ${response.status} - ${response.statusText}`);
        }
        
        const text = await response.text();
        console.log("📄 Respuesta cruda:", text);
        
        let data;
        try {
            data = JSON.parse(text);
            console.log("📊 Datos JSON recibidos:", data);
        } catch (e) {
            console.error("❌ Error parseando JSON:", e);
            throw new Error("Respuesta no es JSON válido");
        }
        
        // Múltiples formas de verificar la estructura de la respuesta
        let bitacoraData = [];
        
        if (data && data.status === 200) {
            // Caso 1: data.bitacora existe
            if (data.data && data.data.bitacora !== undefined) {
                bitacoraData = data.data.bitacora;
                console.log("✅ Estructura: data.data.bitacora - Registros:", bitacoraData.length);
            }
            // Caso 2: data es directamente el array
            else if (Array.isArray(data.data)) {
                bitacoraData = data.data;
                console.log("✅ Estructura: data.data (array directo) - Registros:", bitacoraData.length);
            }
            // Caso 3: bitacora está en el nivel raíz
            else if (data.bitacora !== undefined) {
                bitacoraData = data.bitacora;
                console.log("✅ Estructura: data.bitacora - Registros:", bitacoraData.length);
            }
            // Caso 4: data es el array directamente
            else if (Array.isArray(data)) {
                bitacoraData = data;
                console.log("✅ Estructura: data (array raíz) - Registros:", bitacoraData.length);
            }
            else {
                console.warn("⚠️ Estructura no reconocida, intentando extraer datos...");
                // Intentar encontrar cualquier array en la respuesta
                for (let key in data) {
                    if (Array.isArray(data[key])) {
                        bitacoraData = data[key];
                        console.log(`✅ Encontrado array en key '${key}' - Registros:`, bitacoraData.length);
                        break;
                    }
                }
                
                // Si no se encontró ningún array, buscar en data.data
                if (bitacoraData.length === 0 && data.data) {
                    for (let key in data.data) {
                        if (Array.isArray(data.data[key])) {
                            bitacoraData = data.data[key];
                            console.log(`✅ Encontrado array en data.data['${key}'] - Registros:`, bitacoraData.length);
                            break;
                        }
                    }
                }
            }
        } else {
            console.error("❌ Status no es 200:", data?.status);
            throw new Error(`Error en la respuesta: ${data?.message || 'Status no es 200'}`);
        }
        
        console.log("📋 Datos finales de bitácora:", bitacoraData);
        
        if (Array.isArray(bitacoraData)) {
            this.datosBitacora = bitacoraData;
            this.mostrarBitacora();
            this.cargarFiltroAcciones();
        } else {
            throw new Error("No se pudo extraer datos de bitácora de la respuesta");
        }
        
    } catch (error) {
        console.error('❌ Error cargando bitácora:', error);
        this.mostrarError(error.message);
    }
}

    cargarFiltroAcciones() {
        const accionesUnicas = [...new Set(this.datosBitacora.map(item => item.ACCION))].sort();
        const selectAccion = document.getElementById('filtroAccion');
        
        accionesUnicas.forEach(accion => {
            const option = document.createElement('option');
            option.value = accion;
            option.textContent = accion;
            selectAccion.appendChild(option);
        });
    }

    mostrarBitacora() {
        const loadingMessage = document.getElementById('loadingMessage');
        const errorMessage = document.getElementById('errorMessage');
        const tabla = document.getElementById('tablaBitacora');
        const paginacion = document.getElementById('paginacion');

        // Ocultar mensajes
        loadingMessage.style.display = 'none';
        errorMessage.style.display = 'none';

        if (!this.datosBitacora || this.datosBitacora.length === 0) {
            console.log("📭 No hay registros en la bitácora");
            errorMessage.textContent = "No hay registros en la bitácora";
            errorMessage.style.display = 'block';
            return;
        }

        console.log("🔄 Mostrando", this.datosBitacora.length, "registros en la tabla");

        // Aplicar filtros
        let datosFiltrados = this.aplicarFiltros(this.datosBitacora);
        
        // Calcular paginación
        const totalRegistros = datosFiltrados.length;
        const totalPaginas = Math.ceil(totalRegistros / this.registrosPorPagina);
        const inicio = (this.paginaActual - 1) * this.registrosPorPagina;
        const fin = inicio + this.registrosPorPagina;
        const datosPagina = datosFiltrados.slice(inicio, fin);

        // Mostrar tabla
        tabla.style.display = 'table';
        this.actualizarTabla(datosPagina);
        
        // Mostrar y actualizar paginación
        paginacion.style.display = 'flex';
        this.actualizarPaginacion(totalRegistros, totalPaginas);
    }

    aplicarFiltros(datos) {
        return datos.filter(item => {
            // Filtro por usuario
            if (this.filtros.usuario && item.USUARIO !== this.filtros.usuario) {
                return false;
            }
            
            // Filtro por acción
            if (this.filtros.accion && item.ACCION !== this.filtros.accion) {
                return false;
            }
            
            // Filtro por fecha
            if (this.filtros.fechaInicio || this.filtros.fechaFin) {
                const fechaRegistro = new Date(item.FECHA).toISOString().split('T')[0];
                
                if (this.filtros.fechaInicio && fechaRegistro < this.filtros.fechaInicio) {
                    return false;
                }
                
                if (this.filtros.fechaFin && fechaRegistro > this.filtros.fechaFin) {
                    return false;
                }
            }
            
            // Búsqueda global
            if (this.filtros.busqueda) {
                const busqueda = this.filtros.busqueda.toLowerCase();
                const camposBusqueda = [
                    item.USUARIO,
                    item.ACCION,
                    item.DESCRIPCION,
                    item.OBJETO,
                    item.CREADO_POR
                ].join(' ').toLowerCase();
                
                if (!camposBusqueda.includes(busqueda)) {
                    return false;
                }
            }
            
            return true;
        });
    }

    actualizarTabla(datos) {
        const tbody = document.querySelector('#tablaBitacora tbody');
        tbody.innerHTML = '';

        datos.forEach(item => {
            const fila = document.createElement('tr');
            
            // Formatear fecha
            const fecha = new Date(item.FECHA);
            const fechaFormateada = fecha.toLocaleString('es-ES');
            
            fila.innerHTML = `
                <td>${item.ID_BITACORA}</td>
                <td>${fechaFormateada}</td>
                <td>${item.USUARIO || 'N/A'}</td>
                <td><span class="badge bg-primary">${item.ACCION}</span></td>
                <td>${item.DESCRIPCION || 'N/A'}</td>
                <td>${item.OBJETO || 'N/A'}</td>
                <td>${item.CREADO_POR || 'SISTEMA'}</td>
            `;
            
            tbody.appendChild(fila);
        });
    }

    actualizarPaginacion(totalRegistros, totalPaginas) {
        const infoPaginacion = document.getElementById('infoPaginacion');
        const paginacionLista = document.getElementById('paginacionLista');
        
        // Actualizar información
        const inicio = (this.paginaActual - 1) * this.registrosPorPagina + 1;
        const fin = Math.min(inicio + this.registrosPorPagina - 1, totalRegistros);
        infoPaginacion.textContent = `Mostrando ${inicio} - ${fin} de ${totalRegistros} registros`;
        
        // Generar números de página
        paginacionLista.innerHTML = '';
        
        // Botón anterior
        const liAnterior = document.createElement('li');
        liAnterior.className = `page-item ${this.paginaActual === 1 ? 'disabled' : ''}`;
        liAnterior.innerHTML = `
            <a class="page-link" href="#" data-pagina="${this.paginaActual - 1}">
                <i class="bi bi-chevron-left"></i>
            </a>
        `;
        paginacionLista.appendChild(liAnterior);
        
        // Números de página
        const paginasMostrar = this.generarNumerosPagina(totalPaginas);
        
        paginasMostrar.forEach(pagina => {
            const li = document.createElement('li');
            li.className = `page-item ${pagina === this.paginaActual ? 'active' : ''}`;
            
            if (pagina === '...') {
                li.innerHTML = '<span class="page-link">...</span>';
            } else {
                li.innerHTML = `
                    <a class="page-link" href="#" data-pagina="${pagina}">${pagina}</a>
                `;
            }
            
            paginacionLista.appendChild(li);
        });
        
        // Botón siguiente
        const liSiguiente = document.createElement('li');
        liSiguiente.className = `page-item ${this.paginaActual === totalPaginas ? 'disabled' : ''}`;
        liSiguiente.innerHTML = `
            <a class="page-link" href="#" data-pagina="${this.paginaActual + 1}">
                <i class="bi bi-chevron-right"></i>
            </a>
        `;
        paginacionLista.appendChild(liSiguiente);
    }

    generarNumerosPagina(totalPaginas) {
        const paginas = [];
        const paginasALaVista = 5; // Número máximo de páginas a mostrar
        
        if (totalPaginas <= paginasALaVista) {
            // Mostrar todas las páginas
            for (let i = 1; i <= totalPaginas; i++) {
                paginas.push(i);
            }
        } else {
            // Lógica para mostrar páginas con elipsis
            if (this.paginaActual <= 3) {
                paginas.push(1, 2, 3, 4, '...', totalPaginas);
            } else if (this.paginaActual >= totalPaginas - 2) {
                paginas.push(1, '...', totalPaginas - 3, totalPaginas - 2, totalPaginas - 1, totalPaginas);
            } else {
                paginas.push(1, '...', this.paginaActual - 1, this.paginaActual, this.paginaActual + 1, '...', totalPaginas);
            }
        }
        
        return paginas;
    }

    cambiarPagina(nuevaPagina) {
        this.paginaActual = nuevaPagina;
        this.mostrarBitacora();
        
        // Scroll to top
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    configurarEventos() {
        // Filtros
        document.getElementById('filtroUsuario').addEventListener('change', (e) => {
            this.filtros.usuario = e.target.value;
            this.paginaActual = 1;
            this.mostrarBitacora();
        });

        document.getElementById('filtroAccion').addEventListener('change', (e) => {
            this.filtros.accion = e.target.value;
            this.paginaActual = 1;
            this.mostrarBitacora();
        });

        document.getElementById('filtroFechaInicio').addEventListener('change', (e) => {
            this.filtros.fechaInicio = e.target.value;
            this.paginaActual = 1;
            this.mostrarBitacora();
        });

        document.getElementById('filtroFechaFin').addEventListener('change', (e) => {
            this.filtros.fechaFin = e.target.value;
            this.paginaActual = 1;
            this.mostrarBitacora();
        });

        // Buscador
        document.getElementById('btnBuscar').addEventListener('click', () => {
            this.filtros.busqueda = document.getElementById('buscadorGlobal').value;
            this.paginaActual = 1;
            this.mostrarBitacora();
        });

        document.getElementById('buscadorGlobal').addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                this.filtros.busqueda = e.target.value;
                this.paginaActual = 1;
                this.mostrarBitacora();
            }
        });

        // Registros por página
        document.getElementById('registrosPorPagina').addEventListener('change', (e) => {
            this.registrosPorPagina = parseInt(e.target.value);
            this.paginaActual = 1;
            this.mostrarBitacora();
        });

        // Paginación (event delegation)
        document.getElementById('paginacionLista').addEventListener('click', (e) => {
            e.preventDefault();
            if (e.target.closest('.page-link') && e.target.closest('.page-link').dataset.pagina) {
                const nuevaPagina = parseInt(e.target.closest('.page-link').dataset.pagina);
                if (!isNaN(nuevaPagina)) {
                    this.cambiarPagina(nuevaPagina);
                }
            }
        });

        // Limpiar filtros
        document.getElementById('btnLimpiarFiltros').addEventListener('click', () => {
            this.limpiarFiltros();
        });

        // Exportar
        document.getElementById('btnExportar').addEventListener('click', () => {
            this.exportarBitacora();
        });
    }

    limpiarFiltros() {
        document.getElementById('filtroUsuario').value = '';
        document.getElementById('filtroAccion').value = '';
        document.getElementById('filtroFechaInicio').value = '';
        document.getElementById('filtroFechaFin').value = '';
        document.getElementById('buscadorGlobal').value = '';
        
        this.filtros = {
            usuario: '',
            accion: '',
            fechaInicio: '',
            fechaFin: '',
            busqueda: ''
        };
        
        this.paginaActual = 1;
        this.mostrarBitacora();
    }

    exportarBitacora() {
        // Implementar exportación a Excel/CSV
        alert('Funcionalidad de exportación en desarrollo');
    }

    mostrarError(mensaje) {
        const loadingMessage = document.getElementById('loadingMessage');
        const errorMessage = document.getElementById('errorMessage');
        
        loadingMessage.style.display = 'none';
        errorMessage.textContent = `Error: ${mensaje}`;
        errorMessage.style.display = 'block';
    }
}

// Instancia global
const bitacoraSistema = new BitacoraSistema();
</script>

<style>
/* Estilos para la tabla de bitácora */
#tablaBitacora {
    font-size: 0.85rem !important;
    width: 100% !important;
}

#tablaBitacora th {
    background-color: #f8f9fa;
    font-weight: 600;
    white-space: nowrap;
    font-size: 0.9rem;
    padding: 10px 8px;
}

#tablaBitacora td {
    vertical-align: middle;
    word-wrap: break-word;
    word-break: break-word;
    padding: 8px 6px;
    line-height: 1.2;
}

.badge {
    font-size: 0.7em;
    padding: 4px 6px;
}

/* Columnas específicas */
#tablaBitacora td:nth-child(1) { /* ID */
    min-width: 60px;
    max-width: 80px;
    text-align: center;
}

#tablaBitacora td:nth-child(2) { /* Fecha */
    min-width: 140px;
    max-width: 160px;
    white-space: nowrap;
}

#tablaBitacora td:nth-child(3) { /* Usuario */
    min-width: 120px;
    max-width: 150px;
}

#tablaBitacora td:nth-child(4) { /* Acción */
    min-width: 100px;
    max-width: 120px;
}

#tablaBitacora td:nth-child(5) { /* Descripción */
    min-width: 200px;
    max-width: 300px;
}

#tablaBitacora td:nth-child(6) { /* Objeto */
    min-width: 120px;
    max-width: 150px;
}

#tablaBitacora td:nth-child(7) { /* Creado Por */
    min-width: 120px;
    max-width: 150px;
}

/* Paginación */
.page-item.active .page-link {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.page-link {
    color: #0d6efd;
}

/* Filtros */
.card-header {
    background-color: #e9ecef;
    border-bottom: 1px solid #dee2e6;
}

/* Responsive */
@media (max-width: 768px) {
    #tablaBitacora {
        font-size: 0.8rem !important;
    }
    
    #tablaBitacora th,
    #tablaBitacora td {
        padding: 6px 4px;
    }
    
    .pagination {
        flex-wrap: wrap;
    }
}
</style>