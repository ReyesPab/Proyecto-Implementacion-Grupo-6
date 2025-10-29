<?php
if (ob_get_length()) ob_clean();
use App\config\errorlogs;
use App\config\responseHTTP;
use App\config\Security;

require dirname(__DIR__) . '/vendor/autoload.php';
errorlogs::activa_error_logs();

if (isset($_GET['route'])) {
    $url = explode('/', $_GET['route']);
    $lista = ['auth', 'user', 'login', 'dashboard', 'recuperar-password', 'gestion-usuarios', 'crear-usuario', 'registro', 'inicio', 'cambiar-password', 'bitacora', 'editar-usuario', 'resetear-contrasena', 'configurar-2fa', 'registrar-compras', 'consultar-compras', 'detalle-compra', 'compras', 'generar_pdf', 'reporte_compras_pdf', 'registrar-proveedor', 'gestion-proveedores', 'editar-proveedor', 'registrar-materia-prima', 'gestion-materia-prima', 'gestion-inventario' ];
    $caso = filter_input(INPUT_GET, "caso");

    // CAMBIO IMPORTANTE: Usar $url[0] directamente en lugar de explode
    $rutaActual = $_GET['route']; // Esto ya contiene la ruta completa



 // En la sección de APIs, AGREGAR 'dashboard':
if (in_array($rutaActual, ['auth', 'user', 'compras', 'dashboard', 'inventario'])) {
    $file = dirname(__DIR__) . '/src/routes/' . $rutaActual . '.php';
    
    if (!file_exists($file) || !is_readable($file)) {
        echo json_encode(responseHTTP::status400('Archivo de ruta no encontrado o no legible'));
        exit;
    }

    require $file;
    exit;
}

// Ruta para consultar-compras (VISTA)
if ($rutaActual === 'gestion-inventario' && empty($caso)) {
    $file = dirname(__DIR__) . '/src/Views/Inventario/gestion-inventario.php';
    if (file_exists($file) && is_readable($file)) {
        require $file;
        exit;
    } else {
        echo json_encode(responseHTTP::status400('Archivo de vista no encontrado o no legible'));
        exit;
    }
}

   // Módulo Compras-- Ruta para registrar-compras
if ($rutaActual === 'registrar-compras' && empty($caso)) {
    $file = dirname(__DIR__) . '/src/Views/compras/registrar-compras.php';
    
    
    if (file_exists($file) && is_readable($file)) {
        require $file;
        exit;
    } else {
       
        echo json_encode(responseHTTP::status400('Archivo de vista no encontrado: ' . $file));
        exit;
    }            
}

// Ruta para consultar-compras (VISTA)
if ($rutaActual === 'consultar-compras' && empty($caso)) {
    $file = dirname(__DIR__) . '/src/Views/compras/consultar-compras.php';
    if (file_exists($file) && is_readable($file)) {
        require $file;
        exit;
    } else {
        echo json_encode(responseHTTP::status400('Archivo de vista no encontrado o no legible'));
        exit;
    }
}

// Ruta para detalle-compra (VISTA)
if ($rutaActual === 'detalle-compra' && empty($caso)) {
    $file = dirname(__DIR__) . '/src/Views/compras/detalle-compra.php';
    if (file_exists($file) && is_readable($file)) {
        require $file;
        exit;
    } else {
        echo json_encode(responseHTTP::status400('Archivo de vista no encontrado o no legible'));
        exit;
    }
}


// Ruta para detalle-compra (VISTA)
// Ruta para generar_pdf (VISTA)
if ($rutaActual === 'generar-pdf' && empty($caso)) {
    $file = dirname(__DIR__) . '/src/Views/compras/generar_pdf.php';
    if (file_exists($file) && is_readable($file)) {
        require $file;
        exit;
    } else {
        echo json_encode(responseHTTP::status400('Archivo de vista no encontrado o no legible'));
        exit;
    }
}

// Ruta para generar_pdf (VISTA)
if ($rutaActual === 'reporte_compras_pdf' && empty($caso)) {
    $file = dirname(__DIR__) . '/src/Views/compras/reporte_compras_pdf.php';
    if (file_exists($file) && is_readable($file)) {
        require $file;
        exit;
    } else {
        echo json_encode(responseHTTP::status400('Archivo de vista no encontrado o no legible'));
        exit;
    }
}

// Ruta para registrar-proveedor (VISTA)
if ($rutaActual === 'registrar-proveedor' && empty($caso)) {
    $file = dirname(__DIR__) . '/src/Views/compras/registrar-proveedor.php';
    if (file_exists($file) && is_readable($file)) {
        require $file;
        exit;
    } else {
        echo json_encode(responseHTTP::status400('Archivo de vista no encontrado o no legible'));
        exit;
    }
}

// Ruta para gestion-proveedores (VISTA)
if ($rutaActual === 'gestion-proveedores' && empty($caso)) {
    $file = dirname(__DIR__) . '/src/Views/compras/gestion-proveedores.php';
    if (file_exists($file) && is_readable($file)) {
        require $file;
        exit;
    } else {
        echo json_encode(responseHTTP::status400('Archivo de vista no encontrado o no legible'));
        exit;
    }
}

// Ruta para ditar-proveedor (VISTA)
if ($rutaActual === 'editar-proveedor' && empty($caso)) {
    $file = dirname(__DIR__) . '/src/Views/compras/editar-proveedor.php';
    if (file_exists($file) && is_readable($file)) {
        require $file;
        exit;
    } else {
        echo json_encode(responseHTTP::status400('Archivo de vista no encontrado o no legible'));
        exit;
    }
}

// Ruta para registrar-materia-prima (VISTA)
if ($rutaActual === 'registrar-materia-prima' && empty($caso)) {
    $file = dirname(__DIR__) . '/src/Views/compras/registrar-materia-prima.php';
    if (file_exists($file) && is_readable($file)) {
        require $file;
        exit;
    } else {
        echo json_encode(responseHTTP::status400('Archivo de vista no encontrado o no legible'));
        exit;
    }
} 

// Ruta para registrar-materia-prima (VISTA)
if ($rutaActual === 'gestion-materia-prima' && empty($caso)) {
    $file = dirname(__DIR__) . '/src/Views/compras/gestion-materia-prima.php';
    if (file_exists($file) && is_readable($file)) {
        require $file;
        exit;
    } else {
        echo json_encode(responseHTTP::status400('Archivo de vista no encontrado o no legible'));
        exit;
    }
} 


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

    // Ruta para crear-usuario  (VISTA)
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

    

    if ($rutaActual === 'resetear-contrasena' && empty($caso)) {
        $file = dirname(__DIR__) . '/src/Views/resetear-contrasena.php';
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

    // Ruta para registro (VISTA)
    if ($rutaActual === 'registro' && empty($caso)) {
        $file = dirname(__DIR__) . '/src/Views/registro.php';
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

    // Ruta para configurar-2fa (VISTA)
    if ($rutaActual === 'configurar-2fa' && empty($caso)) {
        $file = dirname(__DIR__) . '/src/Views/configurar-2fa.php';
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