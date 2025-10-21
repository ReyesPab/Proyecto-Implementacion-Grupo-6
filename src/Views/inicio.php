<?php 
require_once 'partials/header.php';
require_once 'partials/sidebar.php';
 require_once 'partials/footer.php'; 
?>


<!-- Favicons -->
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="/sistema/src/Views/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="/sistema/src/Views/assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="/sistema/src/Views/assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="/sistema/src/Views/assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
  <link href="/sistema/src/Views/assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="/sistema/src/Views/assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

  <!-- Main CSS File -->
  <link href="/sistema/src/Views/assets/css/main.css" rel="stylesheet">

  <style>
    .info-card {
        transition: transform 0.3s ease;
        border: none;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
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

    .breadcrumb {
        background: transparent;
        padding: 0;
    }

    .breadcrumb-item a {
        color: #4361ee;
        text-decoration: none;
    }

    .breadcrumb-item.active {
        color: #6c757d;
    }

    .chart-container {
        position: relative;
        height: 300px;
        width: 100%;
    }
  </style>



  

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
        <div class="col-xxl-3 col-md-6">
          <div class="card info-card">
            <div class="card-body">
              <h5 class="card-title">Usuarios</h5>
              <div class="d-flex align-items-center">
                <div class="card-icon rounded-circle bg-primary">
                  <i class="bi bi-people"></i>
                </div>
                <div class="ps-3">
                  <h6 id="total-usuarios">0</h6>
                  <span class="text-muted small pt-2 ps-1">Registrados</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Tarjeta de Sesiones Activas -->
        <div class="col-xxl-3 col-md-6">
          <div class="card info-card">
            <div class="card-body">
              <h5 class="card-title">Sesiones Activas</h5>
              <div class="d-flex align-items-center">
                <div class="card-icon rounded-circle bg-success">
                  <i class="bi bi-person-check"></i>
                </div>
                <div class="ps-3">
                  <h6 id="total-sesiones">0</h6>
                  <span class="text-muted small pt-2 ps-1">Conectados</span>
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
                  <h6 id="total-actividades">0</h6>
                  <span class="text-muted small pt-2 ps-1">Hoy</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Tarjeta de Alertas -->
        <div class="col-xxl-3 col-md-6">
          <div class="card info-card">
            <div class="card-body">
              <h5 class="card-title">Alertas</h5>
              <div class="d-flex align-items-center">
                <div class="card-icon rounded-circle bg-warning">
                  <i class="bi bi-exclamation-triangle"></i>
                </div>
                <div class="ps-3">
                  <h6 id="total-alertas">0</h6>
                  <span class="text-muted small pt-2 ps-1">Del Sistema</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Tarjeta de Compras -->
        <div class="col-xxl-3 col-md-6">
          <div class="card info-card">
            <div class="card-body">
              <h5 class="card-title">Compras</h5>
              <div class="d-flex align-items-center">
                <div class="card-icon rounded-circle bg-secondary">
                  <i class="bi bi-cart-check"></i>
                </div>
                <div class="ps-3">
                  <h6 id="total-compras">0</h6>
                  <span class="text-muted small pt-2 ps-1">Procesadas</span>
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
                  <h6 id="total-inventarios">0</h6>
                  <span class="text-muted small pt-2 ps-1">Productos</span>
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
                  <h6 id="total-produccion">0</h6>
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
                  <h6 id="total-ventas">0</h6>
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
                  <h6 id="total-bitacora">0</h6>
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
              <h5 class="card-title">Actividad del Sistema</h5>
              <div class="chart-container">
                <canvas id="lineChart"></canvas>
              </div>
            </div>
          </div>
        </div>

        <!-- Gráfica de barras - Módulos del Sistema -->
        <div class="col-lg-6">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Uso de Módulos</h5>
              <div class="chart-container">
                <canvas id="barChart"></canvas>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>



  <!-- Vendor JS Files -->
  <script src="/sistema/src/Views/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="/sistema/src/Views/assets/vendor/php-email-form/validate.js"></script>
  <script src="/sistema/src/Views/assets/vendor/aos/aos.js"></script>
  <script src="/sistema/src/Views/assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="/sistema/src/Views/assets/vendor/purecounter/purecounter_vanilla.js"></script>
  <script src="/sistema/src/Views/assets/vendor/swiper/swiper-bundle.min.js"></script>
  


  <!-- Main JS File -->
  <script src="/sistema/src/Views/assets/js/main.js"></script>


  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Función para animar el conteo
      function animateValue(id, start, end, duration) {
        const obj = document.getElementById(id);
        let startTimestamp = null;
        const step = (timestamp) => {
          if (!startTimestamp) startTimestamp = timestamp;
          const progress = Math.min((timestamp - startTimestamp) / duration, 1);
          const value = Math.floor(progress * (end - start) + start);
          obj.textContent = value.toLocaleString();
          if (progress < 1) {
            window.requestAnimationFrame(step);
          }
        };
        window.requestAnimationFrame(step);
      }

      // Cargar información del usuario
      const userName = sessionStorage.getItem('user_name') || 'Usuario';
      const userRole = sessionStorage.getItem('user_role') || 'Rol no asignado';
      
      // Simular datos del sistema (reemplazar con llamadas a tu API)
      const systemStats = {
        total_usuarios: 156,
        total_sesiones: 23,
        total_actividades: 189,
        total_alertas: 3,
        total_compras: 45,
        total_inventarios: 1247,
        total_produccion: 12,
        total_ventas: 89,
        total_bitacora: 2341
      };

      // Animar cada tarjeta
      const animationDuration = 1500;
      
      animateValue('total-usuarios', 0, systemStats.total_usuarios, animationDuration);
      animateValue('total-sesiones', 0, systemStats.total_sesiones, animationDuration);
      animateValue('total-actividades', 0, systemStats.total_actividades, animationDuration);
      animateValue('total-alertas', 0, systemStats.total_alertas, animationDuration);
      animateValue('total-compras', 0, systemStats.total_compras, animationDuration);
      animateValue('total-inventarios', 0, systemStats.total_inventarios, animationDuration);
      animateValue('total-produccion', 0, systemStats.total_produccion, animationDuration);
      animateValue('total-ventas', 0, systemStats.total_ventas, animationDuration);
      animateValue('total-bitacora', 0, systemStats.total_bitacora, animationDuration);

      // Inicializar gráficas después de un pequeño delay para que las animaciones terminen
      setTimeout(initializeCharts, animationDuration + 500);

      function initializeCharts() {
        // Gráfica de líneas - Actividad del Sistema
        const lineCtx = document.getElementById('lineChart').getContext('2d');
        new Chart(lineCtx, {
          type: 'line',
          data: {
            labels: ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'],
            datasets: [
              {
                label: 'Usuarios Activos',
                data: [45, 52, 38, 61, 55, 48, 35],
                borderColor: '#4361ee',
                backgroundColor: 'rgba(67, 97, 238, 0.1)',
                tension: 0.4,
                fill: true,
                borderWidth: 3
              },
              {
                label: 'Actividades',
                data: [120, 145, 98, 167, 134, 112, 89],
                borderColor: '#38b000',
                backgroundColor: 'rgba(56, 176, 0, 0.1)',
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
                position: 'top',
                labels: {
                  font: {
                    size: 12,
                    family: "'Poppins', sans-serif"
                  }
                }
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

        // Gráfica de barras - Uso de Módulos
        const barCtx = document.getElementById('barChart').getContext('2d');
        new Chart(barCtx, {
          type: 'bar',
          data: {
            labels: ['Usuarios', 'Compras', 'Inventarios MP', 'Inventarios Productos', 'Producción', 'Ventas', 'Bitácora'],
            datasets: [
              {
                label: 'Accesos Diarios',
                data: [156, 45, 89, 23, 67, 34, 222],
                backgroundColor: [
                  'rgba(67, 97, 238, 0.8)',
                  'rgba(108, 117, 125, 0.8)',
                  'rgba(220, 53, 69, 0.8)',
                  'rgba(49, 255, 8, 0.93)',
                  'rgba(111, 66, 193, 0.8)',
                  'rgba(214, 51, 132, 0.8)',
                  'rgba(32, 201, 151, 0.8)'
                ],
                borderColor: [
                  '#4361ee',
                  '#6c757d',
                  '#dc3545',
                  '#297e1eff',
                  '#6f42c1',
                  '#d63384',
                  '#20c997'
                ],
                borderWidth: 2,
                borderRadius: 8
              }
            ]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
              legend: { 
                display: false
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

      // Inicializar animaciones
      AOS.init();
    });

    function navigateTo(route) {
      window.location.href = `/sistema/public/index.php?route=${route}`;
    }
  </script>
