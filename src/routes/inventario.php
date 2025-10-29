<?php
use App\controllers\inventarioController;
use App\config\responseHTTP;

$method = strtolower($_SERVER['REQUEST_METHOD']);
$route = $_GET['route'] ?? '';
$params = explode('/', $route);
$body = json_decode(file_get_contents("php://input"), true) ?? [];
$data = array_merge($_GET, $body);
$headers = getallheaders();
$caso = $_GET['caso'] ?? '';

$inventario = new inventarioController($method, $data);

// Rutas para gestión de inventario
switch ($caso) {
    case 'listar':
        if ($method == 'get') {
            $inventario->listarInventario();
        } else {
            echo json_encode(responseHTTP::status405());
        }
        break;
        
    case 'obtener':
        if ($method == 'get') {
            $inventario->obtenerItemInventario();
        } else {
            echo json_encode(responseHTTP::status405());
        }
        break;
        
    case 'actualizar':
        if ($method == 'post') {
            $inventario->actualizarInventario();
        } else {
            echo json_encode(responseHTTP::status405());
        }
        break;
        
    case 'historial':
        if ($method == 'get') {
            $inventario->obtenerHistorial();
        } else {
            echo json_encode(responseHTTP::status405());
        }
        break;
        
    case 'exportar-pdf':
        if ($method == 'get') {
            $inventario->exportarInventarioPDF();
        } else {
            echo json_encode(responseHTTP::status405());
        }
        break;
        
    case 'alertas':
        if ($method == 'get') {
            $inventario->obtenerAlertas();
        } else {
            echo json_encode(responseHTTP::status405());
        }
        break;

    default:
        echo json_encode(responseHTTP::status404('Endpoint de inventario no encontrado: ' . $caso));
        break;
}
?>