<?php
 
?>

<!-- (hide/show toggle removed) -->

<!-- submenu visual styles -->
<style>
  /* make submenu links white and add orange transparent hover */
  .sidebar .nav-content a {
    color: #ffffff !important;
    font-size: 15px;
    padding: 8px 16px;
    padding-left: 28px;
    display: flex;
    align-items: center;
    gap: 10px;
    transition: background-color 0.15s ease, color 0.15s ease;
  }

  /* base icon colors for submenu and top-level */
  .sidebar .nav-content a .bi-circle,
  .sidebar .nav-link i {
    color: rgba(255,255,255,0.85);
    transition: color 0.15s ease;
  }

  .sidebar .nav-content a:hover,
  .sidebar .nav-content a:focus {
    background-color: rgba(255,165,0,0.12); /* soft orange transparent */
    color: #ffffff !important;
    text-decoration: none;
  }

  /* subtle left accent on hover */
  .sidebar .nav-content a:hover { border-left: 3px solid rgba(255,165,0,0.25); }

  /* turn the icon orange when hovering the whole link (top-level and submenu) */
  .sidebar .nav-link:hover i,
  .sidebar .nav-content a:hover i {
    color: rgba(255,165,0,1) !important;
  }
</style>


<!-- ======= Sidebar ======= -->
<aside id="sidebar" class="sidebar">
  <div class="sidebar-brand">
    <img src="/sistema/src/Views/assets/img/Tesorodemimi.jpg" alt="logo" class="brand-logo-small">
  </div>
  <div class="sidebar-profile p-3 d-flex flex-column align-items-center gap-2 text-center">
    <?php
      $nombre = $_SESSION['usuario_nombre'] ?? 'Usuario';
      $rol = $_SESSION['usuario_rol'] ?? '';

      // Preferencia 1: ruta proporcionada en session (puede ser URL o path relativo)
      $avatarCandidate = $_SESSION['usuario_avatar'] ?? '';

      // Preferencia 2: archivo por id en uploads (jpg/png)
      $userId = $_SESSION['usuario_id'] ?? null;
      $uploadAvatar = '';
      if ($userId) {
        $try1 = __DIR__ . '/../../../../public/uploads/avatars/' . $userId . '.jpg';
        $try2 = __DIR__ . '/../../../../public/uploads/avatars/' . $userId . '.png';
        if (file_exists($try1)) {
          $uploadAvatar = '/sistema/public/uploads/avatars/' . $userId . '.jpg';
        } elseif (file_exists($try2)) {
          $uploadAvatar = '/sistema/public/uploads/avatars/' . $userId . '.png';
        }
      }

      // Default avatar
      $defaultAvatar = '/sistema/public/Views/assets/img/perfil.jpg';

      // Resolve final avatar URL
      if (!empty($avatarCandidate)) {
        // If it's an absolute URL, use it; if it's a server path, check existence
        if (preg_match('#^https?://#i', $avatarCandidate)) {
          $avatar = $avatarCandidate;
        } else {
          // assume relative to public root
          $serverPath = $_SERVER['DOCUMENT_ROOT'] . '/' . ltrim($avatarCandidate, '/');
          if (file_exists($serverPath)) {
            $avatar = '/' . ltrim($avatarCandidate, '/');
          } else {
            $avatar = $uploadAvatar ?: $defaultAvatar;
          }
        }
      } else {
        $avatar = $uploadAvatar ?: $defaultAvatar;
      }
    ?>
    <img src="<?= htmlspecialchars($avatar, ENT_QUOTES, 'UTF-8') ?>" alt="avatar" class="avatar mb-1" style="width:54px;height:54px;border-radius:50%;object-fit:cover;" onerror="this.onerror=null;this.src='<?= $defaultAvatar ?>'">
    <div class="profile-meta w-100">
      <div class="fw-bold"><?= htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') ?></div>
      <?php if (!empty($rol)): ?>
        <div class="text-muted small"><?= htmlspecialchars($rol, ENT_QUOTES, 'UTF-8') ?></div>
      <?php endif; ?>
    </div>
  </div>

  <ul class="sidebar-nav" id="sidebar-nav">

 
  <li class="nav-item">
    <a class="nav-link" href='/sistema/public/inicio'>
        <i class="bi bi-grid"></i>
    <span>Inicio</span>
    </a>
</li>
  

   
    <li class="nav-item">
    <a class="nav-link" href='/sistema/public/dashboard'>
        <i class="bi bi-grid"></i>
        <span>Dashboard</span>
    </a>
</li>
     

  
    <li class="nav-item">
      <a class="nav-link collapsed" data-bs-target="#usuarios-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-people"></i><span>Usuarios</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="usuarios-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">

      <li>
    <a href='/sistema/public/gestion-usuarios'>
        <i class="bi bi-circle"></i><span>Registrados</span>
    </a>
</li>

      <li>
    <a href='/sistema/public/cambiar-password'>
        <i class="bi bi-circle"></i><span>Cambiar Contraseña</span>
    </a>
</li>
       
        <li>
          <a href='/sistema/public/usuarios_asignar'>
            <i class="bi bi-circle"></i><span>Permisos</span>
          </a>
        </li>
      </ul>
    </li>
    


    <li class="nav-item">
      <a class="nav-link collapsed" data-bs-target="#servicios-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-collection"></i><span>Compras</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="servicios-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">

      <li>
          <a href='/sistema/public/gestion-proveedores'>
            <i class="bi bi-circle"></i><span>Proveedores</span>
          </a>
        </li> 

        <li>
          <a href='/sistema/public/consultar-compras'>
            <i class="bi bi-circle"></i><span> Compras</span>
          </a>
        </li> 

         <li>
          <a href='/sistema/public/gestion-materia-prima'>
            <i class="bi bi-circle"></i><span> Materia prima</span>
          </a>
        </li> 

        
        
      </ul>
    </li>


    <li class="nav-item">
      <a class="nav-link collapsed" data-bs-target="#clinicas-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-building"></i><span>Ventas</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="clinicas-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">

      
      <li>
          <a href='/sistema/public/registrar-materia-prima'>
            <i class="bi bi-circle"></i><span>Reportes de Ventas</span>
          </a>
        </li>
    

        
        <li>
          <a href='/sistema/public/gestion-materia-prima'>
            <i class="bi bi-circle"></i><span>Historial de Ventas</span>
          </a>
        </li>
       
      </ul>
    </li>

    <li class="nav-item">
      <a class="nav-link collapsed" data-bs-target="#citas-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-calendar-check"></i><span>Inventarios</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="citas-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
        <li>
          <a href='/sistema/public/gestion-inventario'>
            <i class="bi bi-circle"></i><span>Inventario de Materia Prima</span>
          </a>
        </li>
         
 
        <li>
          <a href='/sistema/public/crear-usuario'>
            <i class="bi bi-circle"></i><span>Inventario de Productos</span>
          </a>
        </li>

        
      </ul>
    </li>

   
    <!-- Nuevo item para Historiales Médicos -->
    <li class="nav-item">
      <a class="nav-link collapsed" data-bs-target="#historiales-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-file-earmark-medical"></i><span>Eventos</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="historiales-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">

        <li>
          <a href='/sistema/public/crear-usuario'>
            <i class="bi bi-circle"></i><span>Listar Historiales</span>
          </a>
        </li>
        <li>
          <a href='/sistema/public/crear-usuario'>
            <i class="bi bi-circle"></i><span>Crear Historial</span>
          </a>
        </li>
      </ul>
    </li>
     

 
    <!-- Nuevo item para Recetas -->
    <li class="nav-item">
      <a class="nav-link collapsed" data-bs-target="#recetas-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-file-earmark-text"></i><span>Recetas</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="recetas-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
        <li>
          <a href='/sistema/public/crear-usuario'>
            <i class="bi bi-circle"></i><span>Listar Recetas</span>
          </a>
        </li>
        <li>
          <a href='/sistema/public/crear-usuario'>
            <i class="bi bi-circle"></i><span>Crear Receta</span>
          </a>
        </li>
      </ul>
    </li>
 


    <!-- Nuevo Item para Informes Médicos -->
    <li class="nav-item">
      <a class="nav-link collapsed" data-bs-target="#informes-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-file-earmark-medical"></i><span>Reportes</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="informes-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
     
      <li>
          <a href="/gestion/public/lista_informes_medicos">
            <i class="bi bi-circle"></i><span>Listar Reportes</span>
          </a>
        </li>
        

       
        <li>
          <a href="/rosquilla/public/crear_informe_medico">
            <i class="bi bi-circle"></i><span>Generar Informe</span>
          </a>
        </li>
        
      </ul>
    </li>


     <!-- Nuevo Item para Nosotros -->
     <li class="nav-item">
      <a class="nav-link" href="/rosquilla/public/nosotros">
        <i class="bi bi-info-circle"></i>
        <span>Nosotros</span>
      </a>
    </li>

    <!-- Nuevo Item para Contacto -->
    <li class="nav-item">
      <a class="nav-link" href="/rosquilla/public/contacto">
        <i class="bi bi-envelope"></i>
        <span>Contacto</span>
      </a>
    </li>

    <li class="nav-item">
  <a class="nav-link" href="/rosquilla/public/crear_notificacion">
    <i class="bi bi-bell"></i>
    <span>Crear Notificaciones</span>
    <span id="contadorNotificaciones" class="badge bg-danger rounded-pill ms-auto"></span>
  </a>
</li>

 
<li class="nav-item">
  <a class="nav-link" href="/sistema/public/bitacora">
    <i class="bi bi-list-check"></i>
    <span>Bitácora del Sistema</span>
  </a>
</li>
    

  </ul>
</aside>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('main');
    
    // Verificar preferencia guardada
    const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
    
    // Aplicar estado inicial según preferencia
    if (isCollapsed) {
      sidebar.classList.add('collapsed');
      sidebar.classList.add('narrow');
      if (mainContent) mainContent.classList.add('expanded');
    }
    
  // Manejar cambios de tamaño de pantalla
    window.addEventListener('resize', function() {
        if (window.innerWidth < 992) {
            sidebar.classList.add('collapsed');
            mainContent.classList.add('expanded');
        } else {
            // Restaurar según preferencia del usuario
            if (localStorage.getItem('sidebarCollapsed') !== 'true') {
                sidebar.classList.remove('collapsed');
                mainContent.classList.remove('expanded');
            }
        }
    });
});
</script>
<!-- En sidebar.php, modifica los enlaces para que registren navegación -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Registrar navegación cuando se hace clic en enlaces del sidebar
    document.querySelectorAll('.sidebar-nav a[href]').forEach(link => {
        link.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            const pagina = obtenerPaginaDesdeURL(href);
            
            if (pagina) {
                // Llamar al endpoint para registrar navegación
                fetch('index.php?route=auth&caso=registrar-navegacion', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        pagina: pagina,
                        accion: 'NAVEGACION'
                    })
                }).catch(error => {
                    console.error('Error registrando navegación:', error);
                });
            }
        });
    });
    
    function obtenerPaginaDesdeURL(url) {
        const mapeoURLs = {
            '/sistema/public/inicio': 'inicio',
            '/sistema/public/dashboard': 'dashboard',
            '/sistema/public/gestion-usuarios': 'gestion-usuarios',
            '/sistema/public/cambiar-password': 'cambiar-password',
            '/sistema/public/usuarios_asignar': 'usuarios_asignar',
            '/sistema/public/bitacora': 'bitacora',
            '/sistema/public/datos': 'datos',
            '/sistema/public/configurar-2fa': 'configurar-2fa'
        };
        
        // Buscar coincidencia exacta o parcial
        for (const [urlPattern, pagina] of Object.entries(mapeoURLs)) {
            if (url.includes(urlPattern) || urlPattern.includes(url)) {
                return pagina;
            }
        }
        
        return null;
    }
});
</script>