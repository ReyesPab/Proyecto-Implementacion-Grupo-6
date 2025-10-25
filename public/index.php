<?php
if (ob_get_length()) ob_clean();
use App\config\errorlogs;
use App\config\responseHTTP;
use App\config\Security;

require dirname(__DIR__) . '/vendor/autoload.php';
errorlogs::activa_error_logs();

if (isset($_GET['route'])) {
    $url = explode('/', $_GET['route']);
$lista = ['auth', 'user', 'login', 'dashboard', 'recuperar-password', 'gestion-usuarios', 'crear-usuario', 'configurar-preguntas', 'inicio', 'cambiar-password', 'bitacora', 'editar-usuario'];    $caso = filter_input(INPUT_GET, "caso");

    // CAMBIO IMPORTANTE: Usar $url[0] directamente en lugar de explode
    $rutaActual = $_GET['route']; // Esto ya contiene la ruta completa

      // Ruta para login (VISTA)
    if ($rutaActual === 'login' && empty($caso)) {
        $file = dirname(__DIR__) . '/src/Views/login.php';
        if (file_exists($file) && is_readable($file)) {
            require $file;
            exit;
        } else {
            echo json_encode(responseHTTP::status400('Archivo de vista no encontrado o no legible'));
            exit;
        }
    }
if ($rutaActual === 'editar-usuario' && empty($caso)) {
    $file = dirname(__DIR__) . '/src/Views/editar-usuario.php';
    if (file_exists($file) && is_readable($file)) {
        require $file;
        exit;
    } else {
        echo json_encode(responseHTTP::status400('Archivo de vista no encontrado o no legible'));
        exit;
    }
}
    // Ruta para inicio (VISTA)
    if ($rutaActual === 'inicio' && empty($caso)) {
        $file = dirname(__DIR__) . '/src/Views/inicio.php';
        if (file_exists($file) && is_readable($file)) {
            require $file;
            exit;
        } else {
            echo json_encode(responseHTTP::status400('Archivo de vista no encontrado o no legible'));
            exit;
        }
    }

    // Ruta para cambiar-password (VISTA)
    if ($rutaActual === 'cambiar-password' && empty($caso)) {
        $file = dirname(__DIR__) . '/src/Views/cambiar-password.php';
        if (file_exists($file) && is_readable($file)) {
            require $file;
            exit;
        } else {
            echo json_encode(responseHTTP::status400('Archivo de vista no encontrado o no legible'));
            exit;
        }
    }

    // Ruta para gestion-usuarios (VISTA)
    if ($rutaActual === 'gestion-usuarios' && empty($caso)) {
        $file = dirname(__DIR__) . '/src/Views/gestion-usuarios.php';
        if (file_exists($file) && is_readable($file)) {
            require $file;
            exit;
        } else {
            echo json_encode(responseHTTP::status400('Archivo de vista no encontrado o no legible'));
            exit;
        }
    }

    // Ruta para crear-usuario (VISTA)
    if ($rutaActual === 'crear-usuario' && empty($caso)) {
        $file = dirname(__DIR__) . '/src/Views/crear-usuario.php';
        if (file_exists($file) && is_readable($file)) {
            require $file;
            exit;
        } else {
            echo json_encode(responseHTTP::status400('Archivo de vista no encontrado o no legible'));
            exit;
        }
    }

    // Ruta para dashboard (VISTA)
    if ($rutaActual === 'dashboard' && empty($caso)) {
        $file = dirname(__DIR__) . '/src/Views/dashboard.php';
        if (file_exists($file) && is_readable($file)) {
            require $file;
            exit;
        } else {
            echo json_encode(responseHTTP::status400('Archivo de vista no encontrado o no legible'));
            exit;
        }
    }

    // Ruta para recuperar-password (VISTA)
    if ($rutaActual === 'recuperar-password' && empty($caso)) {
        $file = dirname(__DIR__) . '/src/Views/recuperar-password.php';
        if (file_exists($file) && is_readable($file)) {
            require $file;
            exit;
        } else {
            echo json_encode(responseHTTP::status400('Archivo de vista no encontrado o no legible'));
            exit;
        }
    }

    // Ruta para configurar-preguntas (VISTA)
    if ($rutaActual === 'configurar-preguntas' && empty($caso)) {
        $file = dirname(__DIR__) . '/src/Views/configurar-preguntas.php';
        if (file_exists($file) && is_readable($file)) {
            require $file;
            exit;
        } else {
            echo json_encode(responseHTTP::status400('Archivo de vista no encontrado o no legible'));
            exit;
        }
    }

    // Ruta para bitacora (VISTA)
    if ($rutaActual === 'bitacora' && empty($caso)) {
        $file = dirname(__DIR__) . '/src/Views/bitacora.php';
        if (file_exists($file) && is_readable($file)) {
            require $file;
            exit;
        } else {
            echo json_encode(responseHTTP::status400('Archivo de vista no encontrado o no legible'));
            exit;
        }
    }

    

    // Verificar si la ruta está permitida (para APIs)
    if (!in_array($rutaActual, $lista)) {
        echo json_encode(responseHTTP::status400('Ruta no permitida'));
        exit;
    }

    // Cargar archivo de ruta API (solo para rutas de API como 'auth', 'user')
    if (in_array($rutaActual, ['auth', 'user'])) {
        $file = dirname(__DIR__) . '/src/routes/' . $rutaActual . '.php';
        
        if (!file_exists($file) || !is_readable($file)) {
            echo json_encode(responseHTTP::status400('Archivo de ruta no encontrado o no legible'));
            exit;
        }

        require $file;
        exit;
    }
    

} else {
    // Redirección por defecto
    header('Location: /sistema/public/login');
    exit;
}