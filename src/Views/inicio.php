<?php
require_once dirname(__DIR__) . '/config/session.php';

// Obtener datos iniciales - con manejo de errores y namespaces
try {
    require_once dirname(__DIR__) . '/models/dashboardModel.php';
    
    // Crear instancia usando namespace completo
    $dashboardModel = new App\models\DashboardModel();
    $estadisticas = $dashboardModel->obtenerEstadisticasGenerales();
    $detalleUsuarios = $dashboardModel->obtenerDetalleUsuarios();
    $datosFinancieros = $dashboardModel->obtenerDatosFinancieros();
    $tendenciaMensual = $dashboardModel->obtenerTendenciaMensual();
    
    // OBTENER EL TOTAL DE ALERTAS DEL SISTEMA - NUEVO
    $totalAlertasSistema = $dashboardModel->obtenerTotalAlertasSistema();
     $totalSesionesActivas = $dashboardModel->obtenerTotalSesionesActivas24h();
    
} catch (Exception $e) {
    // Si hay error, usar valores por defecto
    error_log("Error al cargar datos del dashboard: " . $e->getMessage());
    $estadisticas = [];
    $detalleUsuarios = [];
    $datosFinancieros = [
        'total_usuarios' => 0,
        'total_compras_monto' => 0,
        'total_ventas_monto' => 0,
        'total_produccion_cantidad' => 0,
        'total_bitacora' => 0,
        'utilidad' => 0,
        'porcentaje_utilidad' => 0
    ];
    $tendenciaMensual = [];
    $totalAlertasSistema = 0;
    
     // Valor por defecto
}

// Valores por defecto para evitar errores
$estadisticas = array_merge([
    'total_usuarios' => 0,
    'sesiones_activas' => 0,
    'usuarios_bloqueados' => 0,
    'usuarios_nuevos' => 0,
    'actividades_hoy' => 0,
    'total_bitacora' => 0,
    'alertas_stock_bajo' => 0,
    'alertas_stock_excesivo' => 0,
    'total_compras' => 0,
    'total_productos' => 0,
    'ordenes_produccion_activas' => 0,
    'ventas_mes_actual' => 0,
    'actividad_reciente' => [],
    'uso_modulos' => []
], $estadisticas);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Panel de Control - Sistema de Gestión</title>
  <meta name="description" content="Sistema integral de gestión empresarial">

  <style>
    .info-card {
        transition: transform 0.3s ease;
        border: none;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        cursor: pointer;
    }

    .info-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.15);
    }

    .card-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 50px;
        height: 50px;
        font-size: 1.5rem;
        color: white;
        border-radius: 10px;
    }

    .bg-primary { background: linear-gradient(135deg, #4361ee, #3a56d4); }
    .bg-success { background: linear-gradient(135deg, #38b000, #32a100); }
    .bg-info { background: linear-gradient(135deg, #00b4d8, #00a0c4); }
    .bg-warning { background: linear-gradient(135deg, #ff9e00, #e68a00); }
    .bg-secondary { background: linear-gradient(135deg, #6c757d, #5a6268); }
    .bg-danger { background: linear-gradient(135deg, #dc3545, #c82333); }
    .bg-purple { background: linear-gradient(135deg, #6f42c1, #5e36b1); }
    .bg-pink { background: linear-gradient(135deg, #d63384, #c2185b); }
    .bg-teal { background: linear-gradient(135deg, #20c997, #1ba87e); }

    .pagetitle {
        margin-bottom: 2rem;
    }

    .card-title {
        color: #2c3e50;
        font-weight: 600;
        margin-bottom: 1rem;
    }

    .ps-3 h6 {
        font-size: 1.5rem;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 0.25rem;
    }

    .text-muted.small {
        font-size: 0.875rem;
        color: #6c757d !important;
    }

    .chart-container {
        position: relative;
        height: 300px;
        width: 100%;
    }

    .modal-detail {
        max-width: 500px;
    }

    .status-badge {
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .badge-active { background-color: #d4edda; color: #155724; }
    .badge-blocked { background-color: #f8d7da; color: #721c24; }
    .badge-new { background-color: #d1ecf1; color: #0c5460; }
    .badge-critico { background-color: #dc3545; color: white; }
    .badge-bajo { background-color: #fd7e14; color: white; }
    .badge-excesivo { background-color: #ffc107; color: black; }
  </style>
</head>

<body>
  <?php require_once 'partials/header.php'; ?>
  <?php require_once 'partials/sidebar.php'; ?>
  
  <main id="main" class="main">
    <div class="pagetitle">
      <h1>Panel de Control</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="/sistema/public/index.php?route=dashboard">Inicio</a></li>
          <li class="breadcrumb-item active">Dashboard</li>
        </ol>
      </nav>
    </div>

    <section class="section dashboard">
      <div class="row">
        <!-- Tarjeta de Usuarios -->
        <div class="col-xxl-3 col-md-6" onclick="mostrarDetalleUsuarios()">
            <div class="card info-card">
                <div class="card-body">
                    <h5 class="card-title">Usuarios</h5>
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle bg-primary">
                            <i class="bi bi-people"></i>
                        </div>
                        <div class="ps-3">
                            <h6 id="total-usuarios"><?php echo $estadisticas['total_usuarios']; ?></h6>
                            <span class="text-muted small pt-2 ps-1">Registrados</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tarjeta de Sesiones Activas -->
        <div class="col-xxl-3 col-md-6" onclick="mostrarSesionesActivas()">
            <div class="card info-card">
                <div class="card-body">
                    <h5 class="card-title">Sesiones Activas</h5>
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle bg-success">
                            <i class="bi bi-person-check"></i>
                        </div>
                        <div class="ps-3">
                            <h6 id="total-sesiones"><?php echo $estadisticas['sesiones_activas']; ?></h6>
                            <span class="text-muted small pt-2 ps-1">Conectados (24h)</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tarjeta de Actividades Hoy -->
        <div class="col-xxl-3 col-md-6">
          <div class="card info-card">
            <div class="card-body">
              <h5 class="card-title">Actividades</h5>
              <div class="d-flex align-items-center">
                <div class="card-icon rounded-circle bg-info">
                  <i class="bi bi-activity"></i>
                </div>
                <div class="ps-3">
                  <h6 id="total-actividades"><?php echo $estadisticas['actividades_hoy']; ?></h6>
                  <span class="text-muted small pt-2 ps-1">Hoy</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Tarjeta de Alertas -->
        <<!-- Tarjeta de Alertas - ACTUALIZADA -->
<div class="col-xxl-3 col-md-6" onclick="mostrarAlertasSistema()">
    <div class="card info-card">
        <div class="card-body">
            <h5 class="card-title">Alertas</h5>
            <div class="d-flex align-items-center">
                <div class="card-icon rounded-circle bg-warning">
                    <i class="bi bi-exclamation-triangle"></i>
                </div>
                <div class="ps-3">
                    <h6 id="total-alertas"><?php echo $estadisticas['alertas_stock_bajo'] + $estadisticas['alertas_stock_excesivo']; ?></h6>
                    <span class="text-muted small pt-2 ps-1">Del Sistema</span>
                </div>
            </div>
        </div>
    </div>
</div>

        <!-- Tarjeta de Compras -->
        <div class="col-xxl-3 col-md-6" onclick="mostrarReporteFinanciero()">
            <div class="card info-card">
                <div class="card-body">
                    <h5 class="card-title">Compras</h5>
                    <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle bg-secondary">
                            <i class="bi bi-cart-check"></i>
                        </div>
                        <div class="ps-3">
                            <h6 id="total-compras"><?php echo $estadisticas['total_compras']; ?></h6>
                            <span class="text-muted small pt-2 ps-1">Último Mes</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tarjeta de Inventarios -->
        <div class="col-xxl-3 col-md-6">
          <div class="card info-card">
            <div class="card-body">
              <h5 class="card-title">Inventarios</h5>
              <div class="d-flex align-items-center">
                <div class="card-icon rounded-circle bg-danger">
                  <i class="bi bi-box-seam"></i>
                </div>
                <div class="ps-3">
                  <h6 id="total-inventarios"><?php echo $estadisticas['total_productos']; ?></h6>
                  <span class="text-muted small pt-2 ps-1">Productos Activos</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Tarjeta de Producción -->
        <div class="col-xxl-3 col-md-6">
          <div class="card info-card">
            <div class="card-body">
              <h5 class="card-title">Producción</h5>
              <div class="d-flex align-items-center">
                <div class="card-icon rounded-circle bg-purple">
                  <i class="bi bi-gear"></i>
                </div>
                <div class="ps-3">
                  <h6 id="total-produccion"><?php echo $estadisticas['ordenes_produccion_activas']; ?></h6>
                  <span class="text-muted small pt-2 ps-1">Órdenes Activas</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Tarjeta de Ventas -->
        <div class="col-xxl-3 col-md-6">
          <div class="card info-card">
            <div class="card-body">
              <h5 class="card-title">Ventas</h5>
              <div class="d-flex align-items-center">
                <div class="card-icon rounded-circle bg-pink">
                  <i class="bi bi-graph-up-arrow"></i>
                </div>
                <div class="ps-3">
                  <h6 id="total-ventas"><?php echo $estadisticas['ventas_mes_actual']; ?></h6>
                  <span class="text-muted small pt-2 ps-1">Este Mes</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Tarjeta de Bitácora -->
        <div class="col-xxl-3 col-md-6">
          <div class="card info-card">
            <div class="card-body">
              <h5 class="card-title">Registros</h5>
              <div class="d-flex align-items-center">
                <div class="card-icon rounded-circle bg-teal">
                  <i class="bi bi-journal-text"></i>
                </div>
                <div class="ps-3">
                  <h6 id="total-bitacora"><?php echo $estadisticas['total_bitacora']; ?></h6>
                  <span class="text-muted small pt-2 ps-1">En Bitácora</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Gráficas -->
      <div class="row mt-4">
        <!-- Gráfica de líneas - Actividad del Sistema -->
        <div class="col-lg-6">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Actividad del Sistema (Últimos 7 días)</h5>
              <div class="chart-container">
                <canvas id="lineChart"></canvas>
              </div>
            </div>
          </div>
        </div>

        <!-- Sección de Reporte Financiero -->
        <!-- Sección de Reporte Financiero Sencillo -->
<div class="col-lg-6">
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title mb-0">
                    <i class="bi bi-cart-check me-2"></i>Reporte de Compras
                </h5>
                <div class="btn-group" role="group" id="periodo-compras-buttons">
                    <button type="button" class="btn btn-sm btn-primary active" onclick="cambiarPeriodoCompras('hoy')">
                        Al Día
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="cambiarPeriodoCompras('semana')">
                        Última Semana
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="cambiarPeriodoCompras('quincena')">
                        15 Días
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="cambiarPeriodoCompras('mes')">
                        Último Mes
                    </button>
                </div>
            </div>
            
            <!-- Resumen de Compras -->
            <div class="row text-center" id="resumen-compras">
                <div class="col-md-6 mb-3">
                    <div class="card border-primary">
                        <div class="card-body">
                            <h6 class="card-title text-primary">
                                <i class="bi bi-cart me-2"></i>Total Compras
                            </h6>
                            <h3 class="text-primary mb-1" id="total-compras-cantidad">0</h3>
                            <small class="text-muted" id="periodo-compras-texto">Hoy</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="card border-success">
                        <div class="card-body">
                            <h6 class="card-title text-success">
                                <i class="bi bi-currency-dollar me-2"></i>Monto Total
                            </h6>
                            <h3 class="text-success mb-1" id="total-compras-monto">L 0.00</h3>
                            <small class="text-muted" id="periodo-monto-texto">Hoy</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Información Adicional -->
            <div class="row mt-3">
                <div class="col-12">
                    <div class="alert alert-info">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <i class="bi bi-info-circle me-2"></i>
                                <span id="info-compras">Cargando información de compras...</span>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-info" onclick="verComprasCompletas()">
                                <i class="bi bi-list-ul me-1"></i>Ver Todas
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
      </div>
    </section>
  </main>

  <!-- Modal Detalle Usuarios -->
  <div class="modal fade" id="modalDetalleUsuarios" tabindex="-1">
      <div class="modal-dialog modal-sm">
          <div class="modal-content">
              <div class="modal-header bg-primary text-white">
                  <h5 class="modal-title">
                      <i class="bi bi-people me-2"></i>Detalle de Usuarios
                  </h5>
                  <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body p-0">
                  <div id="contenido-usuarios-detalle">
                      <div class="text-center py-4">
                          <div class="spinner-border text-primary" role="status">
                              <span class="visually-hidden">Cargando...</span>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
      </div>
  </div>

  <!-- Modal Sesiones Activas -->
  <div class="modal fade" id="modalSesionesActivas" tabindex="-1">
      <div class="modal-dialog modal-md">
          <div class="modal-content">
              <div class="modal-header bg-success text-white">
                  <h5 class="modal-title">
                      <i class="bi bi-person-check me-2"></i>Sesiones Activas (24h)
                  </h5>
                  <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                  <div id="contenido-sesiones-activas">
                      <div class="text-center py-4">
                          <div class="spinner-border text-success" role="status">
                              <span class="visually-hidden">Cargando...</span>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
      </div>
  </div>

  <!-- Modal Alertas Materia Prima -->
  <div class="modal fade" id="modalAlertasMateriaPrima" tabindex="-1">
      <div class="modal-dialog modal-lg">
          <div class="modal-content">
              <div class="modal-header bg-warning text-dark">
                  <h5 class="modal-title">
                      <i class="bi bi-exclamation-triangle me-2"></i>Alertas de Materia Prima
                  </h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                  <div id="contenido-alertas-materia-prima">
                      <div class="text-center py-4">
                          <div class="spinner-border text-warning" role="status">
                              <span class="visually-hidden">Cargando...</span>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
      </div>
  </div>

  <?php require_once 'partials/footer.php'; ?>

  <script>
    // Variables globales para el reporte financiero
   let periodoComprasActual = 'hoy';
let reporteCompras = null;


    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar gráficas
        initializeCharts();
        
        // Cargar reporte financiero inicial
        cargarReporteFinanciero();

        // Configurar tooltips de Bootstrap
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });

    // Función para cambiar período
    function cambiarPeriodo(periodo) {
        periodoActual = periodo;
        
        // Actualizar botones
        document.querySelectorAll('.btn-group .btn').forEach(btn => {
            btn.classList.remove('active');
        });
        event.target.classList.add('active');
        
        // Cargar datos del nuevo período
        cargarReporteFinanciero();
    }

    // Función para cargar el reporte financiero
    function cargarReporteCompras() {
    fetch(`/sistema/public/index.php?route=dashboard&action=reporte-compras-sencillo`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                reporteCompras = data.data;
                actualizarVistaCompras();
            } else {
                console.error('Error al cargar reporte de compras:', data.message);
                mostrarErrorCompras();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarErrorCompras();
        });
}

function actualizarVistaCompras() {
    if (!reporteCompras) return;
    
    let monto, cantidad, textoPeriodo, infoTexto;
    
    switch(periodoComprasActual) {
        case 'hoy':
            monto = reporteCompras.compras_hoy;
            cantidad = reporteCompras.cantidad_hoy;
            textoPeriodo = 'Hoy';
            infoTexto = `Se realizaron ${cantidad} compras hoy`;
            break;
        case 'semana':
            monto = reporteCompras.compras_semana;
            cantidad = reporteCompras.cantidad_semana;
            textoPeriodo = 'Última Semana';
            infoTexto = `${cantidad} compras en la última semana`;
            break;
        case 'quincena':
            monto = reporteCompras.compras_quincena;
            cantidad = reporteCompras.cantidad_quincena;
            textoPeriodo = 'Últimos 15 Días';
            infoTexto = `${cantidad} compras en los últimos 15 días`;
            break;
        case 'mes':
            monto = reporteCompras.compras_mes;
            cantidad = reporteCompras.cantidad_mes;
            textoPeriodo = 'Último Mes';
            infoTexto = `${cantidad} compras en el último mes`;
            break;
    }
    
    // Actualizar valores
    document.getElementById('total-compras-cantidad').textContent = cantidad;
    document.getElementById('total-compras-monto').textContent = `L ${monto.toLocaleString('es-HN', {minimumFractionDigits: 2})}`;
    document.getElementById('periodo-compras-texto').textContent = textoPeriodo;
    document.getElementById('periodo-monto-texto').textContent = textoPeriodo;
    document.getElementById('info-compras').textContent = infoTexto;
}

// Función para mostrar error
function mostrarErrorCompras() {
    document.getElementById('total-compras-cantidad').textContent = '0';
    document.getElementById('total-compras-monto').textContent = 'L 0.00';
    document.getElementById('info-compras').textContent = 'Error al cargar los datos de compras';
}

// Función para ir al módulo de compras
function verComprasCompletas() {
    window.location.href = '/sistema/public/index.php?route=compras&caso=listar';
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    // Cargar reporte de compras al iniciar
    cargarReporteCompras();
    
    // Actualizar cada 2 minutos
    setInterval(cargarReporteCompras, 120000);
});

    // Función para mostrar modal de reporte financiero
    // Reemplaza la función mostrarModalFinanciero con esta:

function mostrarModalFinanciero(data) {
    if (data.success) {
        const reporte = data.data;
        const modal = `
            <div class="modal fade" id="modalFinanciero" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-success text-white">
                            <h5 class="modal-title">
                                <i class="bi bi-graph-up me-2"></i>Reporte Financiero Completo
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <!-- Pestañas para Ventas y Compras -->
                            <ul class="nav nav-tabs mb-4" id="financieroTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="ventas-tab" data-bs-toggle="tab" data-bs-target="#ventas" type="button" role="tab">
                                        <i class="bi bi-arrow-up-circle me-1"></i>Ventas
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="compras-tab" data-bs-toggle="tab" data-bs-target="#compras" type="button" role="tab">
                                        <i class="bi bi-arrow-down-circle me-1"></i>Compras
                                    </button>
                                </li>
                            </ul>
                            
                            <div class="tab-content" id="financieroTabsContent">
                                <!-- Pestaña de Ventas -->
                                <div class="tab-pane fade show active" id="ventas" role="tabpanel">
                                    <div class="row text-center">
                                        <div class="col-md-3 mb-3">
                                            <div class="card border-primary">
                                                <div class="card-body">
                                                    <h6 class="card-title text-primary">Ventas Hoy</h6>
                                                    <h4 class="text-primary">L ${reporte.ventas_hoy.toLocaleString('es-HN', {minimumFractionDigits: 2})}</h4>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <div class="card border-info">
                                                <div class="card-body">
                                                    <h6 class="card-title text-info">Ventas Semana</h6>
                                                    <h4 class="text-info">L ${reporte.ventas_semana.toLocaleString('es-HN', {minimumFractionDigits: 2})}</h4>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <div class="card border-warning">
                                                <div class="card-body">
                                                    <h6 class="card-title text-warning">Ventas 15 Días</h6>
                                                    <h4 class="text-warning">L ${reporte.ventas_quincena.toLocaleString('es-HN', {minimumFractionDigits: 2})}</h4>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <div class="card border-success">
                                                <div class="card-body">
                                                    <h6 class="card-title text-success">Ventas Mes</h6>
                                                    <h4 class="text-success">L ${reporte.ventas_mes.toLocaleString('es-HN', {minimumFractionDigits: 2})}</h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Pestaña de Compras -->
                                <div class="tab-pane fade" id="compras" role="tabpanel">
                                    <div class="row text-center">
                                        <div class="col-md-3 mb-3">
                                            <div class="card border-primary">
                                                <div class="card-body">
                                                    <h6 class="card-title text-primary">Compras Hoy</h6>
                                                    <h4 class="text-primary">L ${reporte.compras_hoy.toLocaleString('es-HN', {minimumFractionDigits: 2})}</h4>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <div class="card border-info">
                                                <div class="card-body">
                                                    <h6 class="card-title text-info">Compras Semana</h6>
                                                    <h4 class="text-info">L ${reporte.compras_semana.toLocaleString('es-HN', {minimumFractionDigits: 2})}</h4>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <div class="card border-warning">
                                                <div class="card-body">
                                                    <h6 class="card-title text-warning">Compras 15 Días</h6>
                                                    <h4 class="text-warning">L ${reporte.compras_quincena.toLocaleString('es-HN', {minimumFractionDigits: 2})}</h4>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <div class="card border-success">
                                                <div class="card-body">
                                                    <h6 class="card-title text-success">Compras Mes</h6>
                                                    <h4 class="text-success">L ${reporte.compras_mes.toLocaleString('es-HN', {minimumFractionDigits: 2})}</h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Resumen de Utilidad -->
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <h6 class="card-title">Resumen del Mes</h6>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <h5 class="text-success">L ${reporte.ventas_mes.toLocaleString('es-HN', {minimumFractionDigits: 2})}</h5>
                                                    <small class="text-muted">Total Ventas</small>
                                                </div>
                                                <div class="col-md-4">
                                                    <h5 class="text-danger">L ${reporte.compras_mes.toLocaleString('es-HN', {minimumFractionDigits: 2})}</h5>
                                                    <small class="text-muted">Total Compras</small>
                                                </div>
                                                <div class="col-md-4">
                                                    <h5 class="text-primary">L ${(reporte.ventas_mes - reporte.compras_mes).toLocaleString('es-HN', {minimumFractionDigits: 2})}</h5>
                                                    <small class="text-muted">Utilidad Neta</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Agregar modal al DOM y mostrarlo
        document.body.insertAdjacentHTML('beforeend', modal);
        new bootstrap.Modal(document.getElementById('modalFinanciero')).show();
        
        // Remover modal del DOM después de cerrar
        document.getElementById('modalFinanciero').addEventListener('hidden.bs.modal', function() {
            this.remove();
        });
    }
}
    // Función para mostrar detalle de usuarios
    // Función para mostrar detalle de usuarios
function mostrarDetalleUsuarios() {
    const contenido = document.getElementById('contenido-usuarios-detalle');
    contenido.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Cargando...</span></div></div>';
    
    fetch('/sistema/public/index.php?route=dashboard&action=detalle-usuarios-modal')
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor');
            }
            return response.json();
        })
        .then(data => {
            console.log('Datos recibidos:', data); // Para debug
            if (data.status === 'success') {
                let html = '<div class="table-responsive">';
                html += '<table class="table table-sm table-hover mb-0">';
                html += '<thead class="table-light"><tr><th>Estado</th><th>Cantidad</th><th>Porcentaje</th></tr></thead>';
                html += '<tbody>';
                
                const totalUsuarios = data.data.reduce((sum, user) => sum + parseInt(user.total), 0);
                
                if (data.data && data.data.length > 0) {
                    data.data.forEach(usuario => {
                        let badgeClass = '';
                        let icono = '';
                        let estadoDisplay = usuario.estado;
                        
                        // Normalizar el estado para mostrar
                        switch(usuario.estado.toUpperCase()) {
                            case 'ACTIVO': 
                                badgeClass = 'badge-active'; 
                                icono = '<i class="bi bi-person-check text-success me-1"></i>';
                                estadoDisplay = 'ACTIVO';
                                break;
                            case 'BLOQUEADO': 
                                badgeClass = 'badge-blocked'; 
                                icono = '<i class="bi bi-person-x text-danger me-1"></i>';
                                estadoDisplay = 'BLOQUEADO';
                                break;
                            case 'NUEVO': 
                                badgeClass = 'badge-new'; 
                                icono = '<i class="bi bi-person-plus text-info me-1"></i>';
                                estadoDisplay = 'NUEVO';
                                break;
                            case 'INACTIVO': 
                                badgeClass = 'badge-secondary'; 
                                icono = '<i class="bi bi-person-dash text-secondary me-1"></i>';
                                estadoDisplay = 'INACTIVO';
                                break;
                            default: 
                                badgeClass = 'badge-secondary';
                                icono = '<i class="bi bi-person me-1"></i>';
                        }
                        
                        const porcentaje = totalUsuarios > 0 ? ((usuario.total / totalUsuarios) * 100).toFixed(1) : 0;
                        
                        html += `<tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    ${icono}
                                    <span class="status-badge ${badgeClass}">${estadoDisplay}</span>
                                </div>
                            </td>
                            <td><strong>${usuario.total}</strong></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                        <div class="progress-bar ${badgeClass.replace('badge-', 'bg-')}" 
                                             style="width: ${porcentaje}%"></div>
                                    </div>
                                    <small class="text-muted">${porcentaje}%</small>
                                </div>
                            </td>
                        </tr>`;
                    });
                    
                    // Total
                    html += `<tr class="table-primary">
                        <td><strong><i class="bi bi-people-fill me-1"></i>TOTAL</strong></td>
                        <td><strong>${totalUsuarios}</strong></td>
                        <td><strong>100%</strong></td>
                    </tr>`;
                } else {
                    html += '<tr><td colspan="3" class="text-center text-muted py-3">No hay datos disponibles</td></tr>';
                }
                
                html += '</tbody></table></div>';
                contenido.innerHTML = html;
            } else {
                contenido.innerHTML = '<div class="alert alert-danger text-center">Error: ' + (data.message || 'No se pudieron cargar los datos') + '</div>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            contenido.innerHTML = '<div class="alert alert-danger text-center">Error al cargar los datos: ' + error.message + '</div>';
        });
    
    new bootstrap.Modal(document.getElementById('modalDetalleUsuarios')).show();
}

    // Función para mostrar sesiones activas
    // Función para mostrar sesiones activas - CORREGIDA
function mostrarSesionesActivas() {
    const contenido = document.getElementById('contenido-sesiones-activas');
    contenido.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-success" role="status"><span class="visually-hidden">Cargando...</span></div></div>';
    
    fetch('/sistema/public/index.php?route=dashboard&action=sesiones-activas-modal')
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            console.log('Datos recibidos sesiones:', data);
            
            if (data.status === 'success') {
                let html = '<div class="table-responsive">';
                html += '<table class="table table-sm table-hover">';
                html += '<thead class="table-light"><tr><th>Usuario</th><th>Nombre</th><th>Última Conexión</th><th>Tiempo Transcurrido</th></tr></thead>';
                html += '<tbody>';
                
                const sesionesData = data.data || [];
                
                if (sesionesData.length > 0) {
                    sesionesData.forEach(sesion => {
                        const fechaConexion = new Date(sesion.FECHA_ULTIMA_CONEXION);
                        const ahora = new Date();
                        const diferenciaMs = ahora - fechaConexion;
                        const diferenciaMinutos = Math.floor(diferenciaMs / (1000 * 60));
                        const diferenciaHoras = Math.floor(diferenciaMs / (1000 * 60 * 60));
                        
                        let badgeTiempo = '';
                        let textoFecha = '';
                        
                        if (diferenciaMinutos < 1) {
                            badgeTiempo = '<span class="badge bg-success">En línea</span>';
                            textoFecha = 'Hace unos segundos';
                        } else if (diferenciaMinutos < 60) {
                            badgeTiempo = `<span class="badge bg-success">Hace ${diferenciaMinutos} min</span>`;
                            textoFecha = `Hace ${diferenciaMinutos} minutos`;
                        } else if (diferenciaHoras < 24) {
                            badgeTiempo = `<span class="badge bg-info">Hace ${diferenciaHoras} h</span>`;
                            textoFecha = `Hace ${diferenciaHoras} horas`;
                        } else {
                            const diferenciaDias = Math.floor(diferenciaHoras / 24);
                            badgeTiempo = `<span class="badge bg-warning">Hace ${diferenciaDias} d</span>`;
                            textoFecha = `Hace ${diferenciaDias} días`;
                        }
                        
                        html += `<tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-person-circle me-2 text-primary"></i>
                                    <div>
                                        <strong>${sesion.USUARIO}</strong>
                                        <div class="mt-1">${badgeTiempo}</div>
                                    </div>
                                </div>
                            </td>
                            <td>${sesion.NOMBRE_USUARIO || 'No disponible'}</td>
                            <td>
                                <div class="small">${fechaConexion.toLocaleString('es-HN')}</div>
                                <div class="text-muted smaller">${textoFecha}</div>
                            </td>
                            <td>
                                <div class="text-center">
                                    ${badgeTiempo}
                                </div>
                            </td>
                        </tr>`;
                    });
                } else {
                    html += `<tr>
                        <td colspan="4" class="text-center py-4">
                            <div class="text-muted">
                                <i class="bi bi-clock-history display-4"></i>
                                <h6 class="mt-2">No hay sesiones activas</h6>
                                <small>No se han registrado conexiones en las últimas 24 horas</small>
                            </div>
                        </td>
                    </tr>`;
                }
                
                html += '</tbody></table>';
                
                // Información adicional
                html += `<div class="mt-3 p-3 bg-light rounded">
                    <div class="row text-center">
                        <div class="col-4">
                            <small class="text-muted"><span class="badge bg-success">●</span> En línea: Últimos 5 min</small>
                        </div>
                        <div class="col-4">
                            <small class="text-muted"><span class="badge bg-info">●</span> Reciente: Últimas 24h</small>
                        </div>
                        <div class="col-4">
                            <small class="text-muted"><span class="badge bg-warning">●</span> Antiguo: Más de 24h</small>
                        </div>
                    </div>
                </div>`;
                
                html += '</div>';
                contenido.innerHTML = html;
            } else {
                contenido.innerHTML = '<div class="alert alert-danger text-center">Error: ' + (data.message || 'No se pudieron cargar las sesiones activas') + '</div>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            contenido.innerHTML = '<div class="alert alert-danger text-center">Error al cargar las sesiones activas: ' + error.message + '</div>';
        });
    
    new bootstrap.Modal(document.getElementById('modalSesionesActivas')).show();
}

    // Función para actualizar el contador de alertas
function actualizarContadorAlertas() {
    fetch('/sistema/public/index.php?route=dashboard&action=estadisticas-alertas')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                const totalAlertas = data.data.reduce((total, tipo) => total + parseInt(tipo.total), 0);
                document.getElementById('total-alertas').textContent = totalAlertas;
            }
        })
        .catch(error => {
            console.error('Error al actualizar contador de alertas:', error);
        });
}

// Llamar a la función cuando se cargue la página
document.addEventListener('DOMContentLoaded', function() {
    // ... código existente ...
    
    // Actualizar contador de alertas cada 30 segundos
    setInterval(actualizarContadorAlertas, 30000);
});

// También actualizar el contador después de marcar una alerta como leída
function marcarAlertaLeida(idAlerta) {
    const formData = new FormData();
    formData.append('action', 'marcar-alerta-leida');
    formData.append('id_alerta', idAlerta);
    
    fetch('/sistema/public/index.php?route=dashboard', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            // Recargar las alertas y actualizar contador
            mostrarAlertasSistema();
            actualizarContadorAlertas();
        } else {
            alert('Error al marcar la alerta como leída: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al marcar la alerta como leída');
    });
}

    // Función para mostrar alertas de materia prima
    // Función para mostrar alertas del sistema - COMPLETAMENTE NUEVA
function mostrarAlertasSistema() {
    const contenido = document.getElementById('contenido-alertas-materia-prima');
    contenido.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-warning" role="status"><span class="visually-hidden">Cargando...</span></div></div>';
    
    fetch('/sistema/public/index.php?route=dashboard&action=alertas-sistema')
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            console.log('Datos recibidos alertas:', data);
            
            if (data.status === 'success') {
                const alertas = data.data || [];
                let html = '<div class="alertas-completas">';
                
                if (alertas.length > 0) {
                    // Agrupar alertas por tipo
                    const alertasPorTipo = {};
                    alertas.forEach(alerta => {
                        if (!alertasPorTipo[alerta.TIPO_ALERTA]) {
                            alertasPorTipo[alerta.TIPO_ALERTA] = [];
                        }
                        alertasPorTipo[alerta.TIPO_ALERTA].push(alerta);
                    });
                    
                    // Mostrar cada tipo de alerta
                    Object.keys(alertasPorTipo).forEach(tipo => {
                        const alertasTipo = alertasPorTipo[tipo];
                        let titulo = '';
                        let icono = '';
                        let colorClase = '';
                        
                        // En la función mostrarAlertasSistema, actualiza el switch:
switch(tipo) {
    case 'INVENTARIO_MP_BAJO':
        titulo = 'Stock Bajo Materia Prima';
        icono = 'bi-exclamation-triangle-fill';
        colorClase = 'danger';
        break;
    case 'INVENTARIO_MP_EXCESIVO':
        titulo = 'Stock Excesivo Materia Prima';
        icono = 'bi-info-circle-fill';
        colorClase = 'warning';
        break;
    case 'INVENTARIO_PROD_BAJO':
        titulo = 'Stock Bajo Productos';
        icono = 'bi-exclamation-triangle-fill';
        colorClase = 'danger';
        break;
    case 'INVENTARIO_PROD_EXCESIVO':
        titulo = 'Stock Excesivo Productos';
        icono = 'bi-info-circle-fill';
        colorClase = 'warning';
        break;
    case 'NUEVO_USUARIO':
        titulo = 'Nuevos Usuarios';
        icono = 'bi-person-plus-fill';
        colorClase = 'info';
        break;
    case 'NUEVO_CLIENTE':
        titulo = 'Nuevos Clientes';
        icono = 'bi-people-fill';
        colorClase = 'primary';
        break;
    case 'NUEVO_PRODUCTO':
        titulo = 'Nuevos Productos';
        icono = 'bi-box-seam';
        colorClase = 'success';
        break;
    default:
        titulo = tipo;
        icono = 'bi-bell-fill';
        colorClase = 'secondary';
}
                        
                        html += `<div class="mb-4">
                            <h6 class="text-${colorClase} mb-3">
                                <i class="bi ${icono} me-2"></i>
                                ${titulo} (${alertasTipo.length})
                            </h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Descripción</th>
                                            <th>Nivel</th>
                                            <th>Fecha</th>
                                            <th>Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>`;
                        
                        alertasTipo.forEach(alerta => {
                            let badgeNivel = '';
                            switch(alerta.NIVEL_URGENCIA) {
                                case 'CRITICA':
                                    badgeNivel = '<span class="badge bg-danger">CRÍTICA</span>';
                                    break;
                                case 'ALTA':
                                    badgeNivel = '<span class="badge bg-warning">ALTA</span>';
                                    break;
                                case 'MEDIA':
                                    badgeNivel = '<span class="badge bg-info">MEDIA</span>';
                                    break;
                                case 'BAJA':
                                    badgeNivel = '<span class="badge bg-secondary">BAJA</span>';
                                    break;
                                default:
                                    badgeNivel = '<span class="badge bg-secondary">' + alerta.NIVEL_URGENCIA + '</span>';
                            }
                            
                            const fecha = new Date(alerta.FECHA_CREACION);
                            const horasTranscurridas = Math.floor((new Date() - fecha) / (1000 * 60 * 60));
                            
                            let badgeTiempo = '';
                            if (horasTranscurridas < 1) {
                                badgeTiempo = '<span class="badge bg-success">Ahora</span>';
                            } else if (horasTranscurridas < 24) {
                                badgeTiempo = `<span class="badge bg-info">Hace ${horasTranscurridas}h</span>`;
                            } else {
                                const dias = Math.floor(horasTranscurridas / 24);
                                badgeTiempo = `<span class="badge bg-warning">Hace ${dias}d</span>`;
                            }
                            
                            html += `<tr>
                                <td>
                                    <strong>${alerta.TITULO}</strong>
                                    <br><small class="text-muted">${alerta.DESCRIPCION}</small>
                                </td>
                                <td>${badgeNivel}</td>
                                <td>
                                    <div>${fecha.toLocaleString('es-HN')}</div>
                                    <small>${badgeTiempo}</small>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" onclick="marcarAlertaLeida(${alerta.ID_ALERTA})">
                                        <i class="bi bi-check-lg"></i> Marcar leída
                                    </button>
                                </td>
                            </tr>`;
                        });
                        
                        html += `</tbody></table></div></div>`;
                    });
                } else {
                    html += `<div class="alert alert-success text-center">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        No hay alertas activas en este momento
                    </div>`;
                }
                
                html += '</div>';
                contenido.innerHTML = html;
            } else {
                contenido.innerHTML = '<div class="alert alert-danger text-center">Error: ' + (data.message || 'No se pudieron cargar las alertas') + '</div>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            contenido.innerHTML = '<div class="alert alert-danger text-center">Error al cargar las alertas: ' + error.message + '</div>';
        });
    
    new bootstrap.Modal(document.getElementById('modalAlertasMateriaPrima')).show();
}

// Función para marcar alerta como leída
function marcarAlertaLeida(idAlerta) {
    const formData = new FormData();
    formData.append('action', 'marcar-alerta-leida');
    formData.append('id_alerta', idAlerta);
    
    fetch('/sistema/public/index.php?route=dashboard', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            // Recargar las alertas
            mostrarAlertasSistema();
        } else {
            alert('Error al marcar la alerta como leída: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al marcar la alerta como leída');
    });
}

    function initializeCharts() {
        // Gráfica de líneas - Actividad del Sistema
        const lineCtx = document.getElementById('lineChart');
        if (lineCtx) {
            new Chart(lineCtx.getContext('2d'), {
                type: 'line',
                data: {
                    labels: ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'],
                    datasets: [
                        {
                            label: 'Actividades Diarias',
                            data: [45, 52, 38, 61, 55, 48, 35],
                            borderColor: '#4361ee',
                            backgroundColor: 'rgba(67, 97, 238, 0.1)',
                            tension: 0.4,
                            fill: true,
                            borderWidth: 3
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { 
                            position: 'top'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0,0,0,0.05)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        }
    }
  </script>
</body>
</html>