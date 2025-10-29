<?php
// src/routes/compras.php

use App\controllers\comprasController;
use App\config\responseHTTP;

// Obtener método HTTP
$method = $_SERVER['REQUEST_METHOD'];

// Obtener datos según el método
if ($method == 'GET') {
    $data = $_GET;
} else if ($method == 'POST') {
    // Para peticiones JSON
    $input = file_get_contents('php://input');
    if (!empty($input)) {
        $data = json_decode($input, true) ?? [];
    } else {
        $data = $_POST;
    }
} else {
    $data = [];
}

// Obtener el caso de la URL
$caso = $data['caso'] ?? $_GET['caso'] ?? '';

// DEBUG: Ver qué está llegando
error_log("DEBUG compras.php - Method: $method, Caso: $caso, Data: " . json_encode($data));

try {
    require_once __DIR__ . '/../controllers/comprasController.php';
    $controller = new comprasController($method, $data);
    
    switch($caso) {
        case 'registrar':
            $controller->registrarCompra();
            break;
            
        case 'listar': // ✅ CASO FALTANTE - AGREGAR ESTE
        case 'obtenerCompras':
            $controller->obtenerCompras();
            break;
            
        case 'obtenerMateriaPrimaProveedor':
            error_log("DEBUG: Llamando a obtenerMateriaPrimaProveedor con id_proveedor: " . ($data['id_proveedor'] ?? 'NO DEFINIDO'));
            $controller->obtenerMateriaPrimaProveedor();
            break;
            
        case 'obtenerDetalleCompra':
            $controller->obtenerDetalleCompra();
            break;
            
        default:
            // Si no hay caso específico, cargar vista
            if ($method == 'GET') {
                // Verificar si es la vista de consultar compras
                if (strpos($_SERVER['REQUEST_URI'], 'consultar-compras') !== false) {
                    require_once __DIR__ . '/../views/compras/consultar-compras.php';
                } else {
                    require_once dirname(__DIR__) . '/views/compras/consultar-compras.php';
                }
            } else {
                echo json_encode(responseHTTP::status404('Endpoint no encontrado'));
            }
            break;

            case 'registrarProveedor':
        $controller->registrarProveedor();
        break;
        
    case 'validarProveedor':
        $controller->validarProveedor();
        break;

        case 'listarProveedores':
    $controller->listarProveedores();
    break;

    case 'obtenerProveedorPorId':
    $controller->obtenerProveedorPorId();
    break;
    
case 'editarProveedor':
    $controller->editarProveedor();
    break;
    
case 'cambiarEstadoProveedor':
    $controller->cambiarEstadoProveedor();
    break;

    case 'exportarProveedoresPDF':
    $controller->exportarProveedoresPDF();
    break;

    case 'registrarMateriaPrima':
    $controller->registrarMateriaPrima();
    break;

case 'obtenerProveedoresActivos':
    $controller->obtenerProveedoresActivos();
    break;

case 'obtenerUnidadesMedida':
    $controller->obtenerUnidadesMedida();
    break;

    case 'listarMateriaPrima':
    $controller->listarMateriaPrima();
    break;
    }

    
 
    
} catch (Exception $e) {
    error_log("Error en ruta compras: " . $e->getMessage());
    echo json_encode([
        'status' => 500,
        'message' => 'Error interno del servidor'
    ]);
}
?>