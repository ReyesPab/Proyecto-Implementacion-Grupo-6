<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=2.0" name="viewport">
  <title><?= $title ?? 'Tesoro de MIMI' ?></title>
  
  <!-- Favicons -->
  <link href="/sistema\src\Views\assets\img/Tesorodemimi.jpg" rel="icon">
  
  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
  
  <!-- Vendor CSS Files -->
  <link href="/sistema/src/Views/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="/sistema/src/Views/assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  
  <!-- Template Main CSS File -->
  <link href="/sistema/src/Views/assets/css/style.css" rel="stylesheet">
  <!-- Glass theme (header + sidebar) -->
  <link href="/sistema/src/Views/assets/css/glass-theme.css" rel="stylesheet">
  <!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
 <!-- Vendor JS Files -->
  
 <!-- <script src="/sistema/src/Views/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script> -->
  
  <!-- Template Main JS File -->

  <!-- <script src="/sistema/src/Views/assets/js/main.js"></script> -->


</head>

<body>
  <!-- ======= Header ======= -->
  <header id="header" class="header fixed-top d-flex align-items-center">
  <div class="d-flex align-items-center justify-content-between">
        <a href="/sistema/public/dashboard" class="logo d-flex align-items-center">
          <i class="bi bi-list"></i>
        </button>
          <img src="/sistema/src/Views/assets/img/Tesorodemimi.jpg" alt="" class="brand-logo">
        
          <span class="d-none d-lg-block">Tesoro D' MIMI</span>
        </a>
        <!-- Header toggle to collapse/expand sidebar -->
         <button id="headerSidebarToggle" class="btn btn-sm btn-outline-light ms-3 d-none d-md-inline" title="Toggle sidebar">
      </div>
    

    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center">

      <li class="nav-item dropdown">
  <a class="nav-link nav-icon" href="#" data-bs-toggle="dropdown">
    <i class="bi bi-bell"></i>
    <span id="contadorNotificaciones" class="badge bg-danger badge-number">0</span>
  </a>
  <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow notifications">
    <li class="dropdown-header">
      Tienes <span class="badge bg-primary rounded-pill">0</span> notificaciones nuevas
    </li>
    <li>
      <hr class="dropdown-divider">
    </li>
    <div id="listaNotificaciones">
      <!-- Aquí se cargarán las notificaciones dinámicamente -->
    </div>
    <li class="dropdown-footer">
      <a href="/sistema/public/notificaciones">Ver todas las notificaciones</a>
    </li>
  </ul>
</li>

        <li class="nav-item dropdown pe-3">
          <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
          <span class="d-none d-md-block dropdown-toggle ps-2"><?= htmlspecialchars($_SESSION['usuario_nombre'] ?? $_SESSION['user_name'] ?? $_SESSION['usuario_usuario'] ?? 'Invitado') ?></span>

          </a>
          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
          
       

            <li>
  <a class="dropdown-item d-flex align-items-center" href="#" id="btnAbrirPerfil">
    <i class="bi bi-person"></i>
    <span>Mi Perfil</span>
  </a>
</li>

            <li>
              <hr class="dropdown-divider">
            </li>
            <li>
              <a class="dropdown-item d-flex align-items-center"  href='configurar-2fa'>
                <i class="bi bi-box-arrow-right"></i>
                <span>Configurar Autenticación 2 Pasos</span>
              </a>
            </li>

            <li>
              <hr class="dropdown-divider">
            </li>
            <li>
              <a class="dropdown-item d-flex align-items-center"  href='/sistema/public/index.php?route=login'>
                <i class="bi bi-box-arrow-right"></i>
                <span>Cerrar Sesión</span>
                
              </a>
            </li>
          </ul>
        </li>
      </ul>
    </nav>
  </header>
  <script>
  document.addEventListener('DOMContentLoaded', function(){
    const headerToggle = document.getElementById('headerSidebarToggle');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    const main = document.getElementById('main');

    if (!headerToggle) return;

    headerToggle.addEventListener('click', function(){
      // If the floating sidebar toggle exists, use it so behavior is centralized
      if (sidebarToggle) {
        sidebarToggle.click();
        return;
      }

      if (!sidebar) return;
      const collapsedNow = sidebar.classList.toggle('collapsed');
      sidebar.classList.toggle('narrow', collapsedNow);
      if (main) main.classList.toggle('expanded', collapsedNow);
      localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));

      // Sync icon on header button if no central toggle
      const icon = headerToggle.querySelector('i');
      if (icon) {
        if (sidebar.classList.contains('collapsed')) {
          icon.classList.remove('bi-list');
          icon.classList.add('bi-grid-1x2');
        } else {
          icon.classList.remove('bi-grid-1x2');
          icon.classList.add('bi-list');
        }
      }
    });
  });
  </script>
