<?php
if (ob_get_length()) ob_clean();
use App\config\errorlogs;
use App\config\responseHTTP;
use App\config\Security;

require dirname(__DIR__) . '/vendor/autoload.php';
errorlogs::activa_error_logs();

if (isset($_GET['route'])) {
    $url = explode('/', $_GET['route']);
    $lista = ['auth', 'user', 'login', 'dashboard', 'recuperar-password', 'gestion-usuarios', 'crear-usuario', 'configurar-preguntas', 'inicio', 'cambiar-password' ];
    $caso = filter_input(INPUT_GET, "caso");

       // Ruta para cambiar-password (VISTA)
    if ($url[0] === 'crear-usuario' && empty($caso)) {
        $file = dirname(__DIR__) . '/src/Views/crear-usuario.php';
        if (file_exists($file) && is_readable($file)) {
            require $file;
            exit;
        } else {
            echo json_encode(responseHTTP::status400('Archivo de vista no encontrado o no legible'));
            exit;
        }
    }

    // Ruta para cambiar-password (VISTA)
    if ($url[0] === 'cambiar-password' && empty($caso)) {
        $file = dirname(__DIR__) . '/src/Views/cambiar-password.php';
        if (file_exists($file) && is_readable($file)) {
            require $file;
            exit;
        } else {
            echo json_encode(responseHTTP::status400('Archivo de vista no encontrado o no legible'));
            exit;
        }
    }

    // Ruta para login (VISTA)
    if ($url[0] === 'login' && empty($caso)) {
        $file = dirname(__DIR__) . '/src/Views/login.php';
        if (file_exists($file) && is_readable($file)) {
            require $file;
            exit;
        } else {
            echo json_encode(responseHTTP::status400('Archivo de vista no encontrado o no legible'));
            exit;
        }
    }

    // Ruta para inicio (VISTA)
    if ($url[0] === 'inicio' && empty($caso)) {
        $file = dirname(__DIR__) . '/src/Views/inicio.php';
        if (file_exists($file) && is_readable($file)) {
            require $file;
            exit;
        } else {
            echo json_encode(responseHTTP::status400('Archivo de vista no encontrado o no legible'));
            exit;
        }
    }

    // Ruta para configurar-preguntas (VISTA)
    if ($url[0] === 'configurar-preguntas' && empty($caso)) {
        $file = dirname(__DIR__) . '/src/Views/configurar-preguntas.php';
        if (file_exists($file) && is_readable($file)) {
            require $file;
            exit;
        } else {
            echo json_encode(responseHTTP::status400('Archivo de vista no encontrado o no legible'));
            exit;
        }
    }

    // Ruta para dashboard (VISTA)
    if ($url[0] === 'dashboard' && empty($caso)) {
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
    if ($url[0] === 'recuperar-password' && empty($caso)) {
        $file = dirname(__DIR__) . '/src/Views/recuperar-password.php';
        if (file_exists($file) && is_readable($file)) {
            require $file;
            exit;
        } else {
            echo json_encode(responseHTTP::status400('Archivo de vista no encontrado o no legible'));
            exit;
        }
    }

    // Ruta para gestion-usuarios (VISTA)
    if ($url[0] === 'gestion-usuarios' && empty($caso)) {
        $file = dirname(__DIR__) . '/src/Views/gestion-usuarios.php';
        if (file_exists($file) && is_readable($file)) {
            require $file;
            exit;
        } else {
            echo json_encode(responseHTTP::status400('Archivo de vista no encontrado o no legible'));
            exit;
        }
    }

    // ELIMINAR ESTA SECCIÓN DUPLICADA (LÍNEAS 71-80)
    // NO DEBE ESTAR AQUÍ OTRA VEZ LA CONDICIÓN DE LOGIN

    // Verificar si la ruta está permitida
    if (!in_array($url[0], $lista)) {
        echo json_encode(responseHTTP::status400('Ruta no permitida'));
        exit;
    }

    // Cargar archivo de ruta API
    $file = dirname(__DIR__) . '/src/routes/' . $url[0] . '.php';
    
    if (!file_exists($file) || !is_readable($file)) {
        echo json_encode(responseHTTP::status400('Archivo de ruta no encontrado o no legible'));
        exit;
    }

    require $file;
    exit;
} else {
    header('Location: index.php?route=login');
    exit;
}