<?php
use App\controllers\userController;
use App\config\responseHTTP;

$method = strtolower($_SERVER['REQUEST_METHOD']);
$route = $_GET['route'] ?? '';
$params = explode('/', $route);
$body = json_decode(file_get_contents("php://input"), true) ?? [];
// Merge GET params into data so controllers can access query string values (p.ej. id_usuario)
$data = array_merge($_GET, $body);
$headers = getallheaders();
$caso = $_GET['caso'] ?? '';

$user = new userController($method, $data);

// Rutas para gestión de usuarios
switch ($caso) {
    case 'crear':
        if ($method == 'post') {
            $user->crearUsuario();
        } else {
            echo json_encode(responseHTTP::status405());
        }
        break;
        
    case 'listar':
        if ($method == 'get') {
            $user->listarUsuarios();
        } else {
            echo json_encode(responseHTTP::status405());
        }
        break;
        
    case 'obtener':
        if ($method == 'get') {
            $user->obtenerUsuario();
        } else {
            echo json_encode(responseHTTP::status405());
        }
        break;
        
case 'actualizar':
    if ($method == 'put' || $method == 'post') {
        $user->actualizarUsuario();
    } else {
        echo json_encode(responseHTTP::status405());
    }
    break;
        
    case 'resetear-password':
        if ($method == 'post') {
            $user->resetearPassword();
        } else {
            echo json_encode(responseHTTP::status405());
        }
        break;
        
    case 'obtener-roles':
        if ($method == 'get') {
            $user->obtenerRoles();
        } else {
            echo json_encode(responseHTTP::status405());
        }
        break;
        
    case 'generar-password':
        if ($method == 'get') {
            $user->generarPassword();
        } else {
            echo json_encode(responseHTTP::status405());
        }
        break;
        
    case 'obtener-parametros':
        if ($method == 'get') {
            $user->obtenerParametros();
        } else {
            echo json_encode(responseHTTP::status405());
        }
        break;
        
    case 'obtener-bitacora':
        if ($method == 'get') {
            $user->obtenerBitacora();
        } else {
            echo json_encode(responseHTTP::status405());
        }
        break;
        
    case 'verificar-usuario':
        if ($method == 'post') {
            $user->verificarUsuario();
        } else {
            echo json_encode(responseHTTP::status405());
        }
        break;

    case 'verificar-identidad':
        if ($method == 'post') {
            $user->verificarIdentidad();
        } else {
            echo json_encode(responseHTTP::status405());
        }
        break;
        
    case 'cambiar-estado':
        if ($method == 'post') {
            $user->cambiarEstado();
        } else {
            echo json_encode(responseHTTP::status405());
        }
        break;
        
case 'obtener-usuario-completo':
    if ($method == 'get') {
        $user->obtenerUsuarioCompleto();
    } else {
        echo json_encode(responseHTTP::status405());
    }
    break;
        // Agregar en el switch case después de 'verificar-identidad'
case 'verificar-correo':
    if ($method == 'post') {
        $user->verificarCorreo();
    } else {
        echo json_encode(responseHTTP::status405());
    }
    break;

        
    default:
        echo json_encode(responseHTTP::status404('Endpoint de usuario no encontrado'));
        break;
}