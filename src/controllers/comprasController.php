<?php

namespace App\controllers;

use App\config\responseHTTP;
use App\config\Security;
use App\db\connectionDB;
use App\models\comprasModel;
use PDO;

class comprasController {
    
    private $method;
    private $data;
    
    public function __construct($method, $data) {
        $this->method = $method;
        $this->data = Security::sanitizeInput($data);
    }
    
    // Registrar nueva compra
    // En comprasController.php - método registrarCompra
// En comprasController.php - método registrarCompra
public function registrarCompra() {
    // Establecer headers JSON PRIMERO
    header('Content-Type: application/json');
    
    // Validar método
    if ($this->method != 'POST') {
        error_log("DEBUG: Método incorrecto. Esperado: POST, Recibido: " . $this->method);
        http_response_code(405);
        echo json_encode(responseHTTP::status405());
        return;
    }
    
    error_log("DEBUG registrarCompra - Datos recibidos: " . json_encode($this->data));
    
    try {
        // Validar datos requeridos
        $camposRequeridos = ['id_proveedor', 'id_usuario', 'detalles'];
        foreach ($camposRequeridos as $campo) {
            if (empty($this->data[$campo])) {
                error_log("DEBUG: Campo requerido faltante: " . $campo);
                http_response_code(400);
                echo json_encode(responseHTTP::status400("El campo $campo es obligatorio"));
                return;
            }
        }
        
        // Validar proveedor
        if (!is_numeric($this->data['id_proveedor']) || $this->data['id_proveedor'] <= 0) {
            error_log("DEBUG: ID de proveedor inválido: " . $this->data['id_proveedor']);
            http_response_code(400);
            echo json_encode(responseHTTP::status400('ID de proveedor inválido'));
            return;
        }
        
        // Validar usuario
        if (!is_numeric($this->data['id_usuario']) || $this->data['id_usuario'] <= 0) {
            error_log("DEBUG: ID de usuario inválido: " . $this->data['id_usuario']);
            http_response_code(400);
            echo json_encode(responseHTTP::status400('ID de usuario inválido'));
            return;
        }
        
        // Validar detalles
        if (!is_array($this->data['detalles']) || empty($this->data['detalles'])) {
            error_log("DEBUG: Detalles inválidos o vacíos");
            http_response_code(400);
            echo json_encode(responseHTTP::status400('Debe incluir al menos un detalle de compra'));
            return;
        }
        
        // Validar cada detalle
        foreach ($this->data['detalles'] as $index => $detalle) {
            if (empty($detalle['id_materia_prima']) || !is_numeric($detalle['id_materia_prima'])) {
                error_log("DEBUG: Detalle $index - ID materia prima inválido: " . ($detalle['id_materia_prima'] ?? 'NULL'));
                http_response_code(400);
                echo json_encode(responseHTTP::status400("Detalle $index: ID de materia prima inválido"));
                return;
            }
            
            if (empty($detalle['cantidad']) || !is_numeric($detalle['cantidad']) || $detalle['cantidad'] <= 0) {
                error_log("DEBUG: Detalle $index - Cantidad inválida: " . ($detalle['cantidad'] ?? 'NULL'));
                http_response_code(400);
                echo json_encode(responseHTTP::status400("Detalle $index: Cantidad debe ser mayor a 0"));
                return;
            }
            
            if (empty($detalle['precio_unitario']) || !is_numeric($detalle['precio_unitario']) || $detalle['precio_unitario'] <= 0) {
                error_log("DEBUG: Detalle $index - Precio unitario inválido: " . ($detalle['precio_unitario'] ?? 'NULL'));
                http_response_code(400);
                echo json_encode(responseHTTP::status400("Detalle $index: Precio unitario debe ser mayor a 0"));
                return;
            }
        }
        
        error_log("DEBUG: Todas las validaciones pasadas. Llamando al modelo...");
        
        // Registrar compra
        $result = comprasModel::registrarCompra($this->data);
        
        if ($result['success']) {
            error_log("DEBUG: Compra registrada exitosamente. ID: " . $result['id_compra']);
            http_response_code(201);
            echo json_encode([
                'status' => 201,
                'message' => $result['message'],
                'data' => [
                    'id_compra' => $result['id_compra'],
                    'total_compra' => $result['total_compra']
                ]
            ]);
        } else {
            error_log("DEBUG: Error en modelo: " . $result['message']);
            http_response_code(400);
            echo json_encode([
                'status' => 400,
                'message' => $result['message']
            ]);
        }
        
    } catch (\Exception $e) {
        error_log("ERROR en comprasController::registrarCompra: " . $e->getMessage());
        error_log("ERROR Trace: " . $e->getTraceAsString());
        http_response_code(500);
        echo json_encode([
            'status' => 500,
            'message' => 'Error interno del servidor: ' . $e->getMessage()
        ]);
    }
}
    
    // Obtener lista de compras
   // En comprasController.php - método obtenerCompras
public function obtenerCompras() {
    // Establecer headers JSON
    header('Content-Type: application/json');
    
    // ✅ CAMBIAR 'get' por 'GET'
    if ($this->method != 'GET') {
        echo json_encode(responseHTTP::status405());
        return;
    }
    
    $filtros = [
        'fecha_inicio' => $this->data['fecha_inicio'] ?? null,
        'fecha_fin' => $this->data['fecha_fin'] ?? null,
        'id_proveedor' => $this->data['id_proveedor'] ?? null,
        'estado_compra' => $this->data['estado_compra'] ?? null
    ];
    
    $result = comprasModel::obtenerCompras($filtros);
    
    if ($result['success']) {
        echo json_encode([
            'status' => 200,
            'data' => $result['data']
        ]);
    } else {
        echo json_encode([
            'status' => 400,
            'message' => $result['message']
        ]);
    }
}
    
    // Obtener detalles de una compra específica
    public function obtenerDetalleCompra() {
        // Establecer headers JSON
        header('Content-Type: application/json');
        
        if ($this->method != 'get') {
            echo json_encode(responseHTTP::status405());
            return;
        }
        
        if (empty($this->data['id_compra']) || !is_numeric($this->data['id_compra'])) {
            echo json_encode(responseHTTP::status400('ID de compra inválido'));
            return;
        }
        
        $result = comprasModel::obtenerDetalleCompra($this->data['id_compra']);
        
        if ($result['success']) {
            echo json_encode([
                'status' => 200,
                'data' => $result['data']
            ]);
        } else {
            echo json_encode([
                'status' => 400,
                'message' => $result['message']
            ]);
        }
    }

    // En comprasController.php - agregar este método
// En comprasController.php - método obtenerMateriaPrimaProveedor
public function obtenerMateriaPrimaProveedor() {
    // Establecer headers JSON primero
    header('Content-Type: application/json');
    
    error_log("DEBUG obtenerMateriaPrimaProveedor - Method: " . $this->method . ", Data: " . json_encode($this->data));
    
    if ($this->method != 'GET') {
        http_response_code(405);
        echo json_encode(responseHTTP::status405());
        return;
    }
    
    // Validar ID de proveedor
    if (empty($this->data['id_proveedor']) || !is_numeric($this->data['id_proveedor'])) {
        error_log("DEBUG: ID de proveedor inválido o vacío");
        http_response_code(400);
        echo json_encode(responseHTTP::status400('ID de proveedor inválido'));
        return;
    }
    
    try {
        $id_proveedor = (int)$this->data['id_proveedor'];
        error_log("DEBUG: Llamando a modelo con id_proveedor: " . $id_proveedor);
        
        $materiaPrima = comprasModel::obtenerMateriaPrimaPorProveedor($id_proveedor);
        
        error_log("DEBUG: Materia prima obtenida: " . json_encode($materiaPrima));
        
        echo json_encode([
            'success' => true,
            'data' => $materiaPrima
        ]);
        
    } catch (\Exception $e) {
        error_log("Error en obtenerMateriaPrimaProveedor: " . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Error al obtener la materia prima del proveedor: ' . $e->getMessage()
        ]);
    }
}

// Registrar nuevo proveedor
public function registrarProveedor() {
    // Establecer headers JSON PRIMERO
    header('Content-Type: application/json');
    
    // Validar método
    if ($this->method != 'POST') {
        error_log("DEBUG: Método incorrecto. Esperado: POST, Recibido: " . $this->method);
        http_response_code(405);
        echo json_encode(responseHTTP::status405());
        return;
    }
    
    error_log("DEBUG registrarProveedor - Datos recibidos: " . json_encode($this->data));
    
    try {
        // Validar datos requeridos
        $camposRequeridos = ['nombre'];
        foreach ($camposRequeridos as $campo) {
            if (empty($this->data[$campo])) {
                error_log("DEBUG: Campo requerido faltante: " . $campo);
                http_response_code(400);
                echo json_encode(responseHTTP::status400("El campo $campo es obligatorio"));
                return;
            }
        }
        
        // Validar nombre (mínimo 3 caracteres)
        if (strlen(trim($this->data['nombre'])) < 3) {
            http_response_code(400);
            echo json_encode(responseHTTP::status400('El nombre debe tener al menos 3 caracteres'));
            return;
        }
        
        // Validar correo si se proporciona
        if (!empty($this->data['correo']) && !filter_var($this->data['correo'], FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode(responseHTTP::status400('El formato del correo electrónico no es válido'));
            return;
        }
        
        // Validar teléfono si se proporciona (solo números y algunos caracteres especiales)
        if (!empty($this->data['telefono']) && !preg_match('/^[0-9\-\+\s\(\)]{8,20}$/', $this->data['telefono'])) {
            http_response_code(400);
            echo json_encode(responseHTTP::status400('El formato del teléfono no es válido'));
            return;
        }
        
        // Validar que no exista un proveedor con el mismo nombre o correo
        $proveedorExistente = comprasModel::validarProveedorExistente(
            $this->data['nombre'], 
            $this->data['correo'] ?? ''
        );
        
        if ($proveedorExistente) {
            http_response_code(400);
            $mensaje = "Ya existe un proveedor con el nombre '{$proveedorExistente['NOMBRE']}'";
            if (!empty($proveedorExistente['CORREO']) && $proveedorExistente['CORREO'] === $this->data['correo']) {
                $mensaje .= " y correo '{$proveedorExistente['CORREO']}'";
            }
            echo json_encode(responseHTTP::status400($mensaje));
            return;
        }
        
        error_log("DEBUG: Todas las validaciones pasadas. Llamando al modelo...");
        
        // Agregar usuario que crea (puedes obtenerlo de la sesión)
        $this->data['creado_por'] = $_SESSION['usuario']['NOMBRE'] ?? 'SISTEMA';
        
        // Registrar proveedor
        $result = comprasModel::registrarProveedor($this->data);
        
        if ($result['success']) {
            error_log("DEBUG: Proveedor registrado exitosamente. ID: " . $result['data']['ID_PROVEEDOR']);
            http_response_code(201);
            echo json_encode([
                'status' => 201,
                'message' => $result['message'],
                'data' => $result['data']
            ]);
        } else {
            error_log("DEBUG: Error en modelo: " . $result['message']);
            http_response_code(400);
            echo json_encode([
                'status' => 400,
                'message' => $result['message']
            ]);
        }
        
    } catch (\Exception $e) {
        error_log("ERROR en comprasController::registrarProveedor: " . $e->getMessage());
        error_log("ERROR Trace: " . $e->getTraceAsString());
        http_response_code(500);
        echo json_encode([
            'status' => 500,
            'message' => 'Error interno del servidor: ' . $e->getMessage()
        ]);
    }
}

// Validar campo único de proveedor
public function validarProveedor() {
    // Establecer headers JSON
    header('Content-Type: application/json');
    
    if ($this->method != 'GET') {
        http_response_code(405);
        echo json_encode(responseHTTP::status405());
        return;
    }
    
    $campo = $this->data['campo'] ?? '';
    $valor = $this->data['valor'] ?? '';
    
    // Validar parámetros
    if (empty($campo) || empty($valor)) {
        http_response_code(400);
        echo json_encode(['disponible' => false, 'message' => 'Parámetros incompletos']);
        return;
    }
    
    // Campos permitidos
    $camposPermitidos = ['nombre', 'contacto', 'correo'];
    if (!in_array($campo, $camposPermitidos)) {
        http_response_code(400);
        echo json_encode(['disponible' => false, 'message' => 'Campo no válido']);
        return;
    }
    
    try {
        $disponible = comprasModel::validarCampoProveedor($campo, $valor);
        
        echo json_encode([
            'disponible' => $disponible,
            'message' => $disponible ? 'Campo disponible' : 'Campo ya existe'
        ]);
        
    } catch (\Exception $e) {
        error_log("Error en validarProveedor: " . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            'disponible' => false,
            'message' => 'Error al validar el campo'
        ]);
    }
}

// Listar proveedores
public function listarProveedores() {
    // Establecer headers JSON
    header('Content-Type: application/json');
    
    if ($this->method != 'GET') {
        http_response_code(405);
        echo json_encode(responseHTTP::status405());
        return;
    }
    
    $filtros = [
        'filtro_nombre' => $this->data['filtro_nombre'] ?? '',
        'filtro_estado' => $this->data['filtro_estado'] ?? ''
    ];
    
    $result = comprasModel::listarProveedores($filtros);
    
    if ($result['success']) {
        echo json_encode([
            'status' => 200,
            'data' => $result['data']
        ]);
    } else {
        echo json_encode([
            'status' => 400,
            'message' => $result['message']
        ]);
    }
}

// Cambiar estado de proveedor
public function cambiarEstadoProveedor() {
    // Establecer headers JSON
    header('Content-Type: application/json');
    
    if ($this->method != 'POST') {
        http_response_code(405);
        echo json_encode(responseHTTP::status405());
        return;
    }
    
    // Validar datos requeridos
    if (empty($this->data['id_proveedor']) || !is_numeric($this->data['id_proveedor'])) {
        http_response_code(400);
        echo json_encode(responseHTTP::status400('ID de proveedor inválido'));
        return;
    }
    
    if (empty($this->data['estado']) || !in_array($this->data['estado'], ['ACTIVO', 'INACTIVO'])) {
        http_response_code(400);
        echo json_encode(responseHTTP::status400('Estado inválido'));
        return;
    }
    
    try {
        $modificado_por = $_SESSION['usuario']['NOMBRE'] ?? 'SISTEMA';
        
        $result = comprasModel::cambiarEstadoProveedor(
            $this->data['id_proveedor'],
            $this->data['estado'],
            $modificado_por
        );
        
        if ($result['success']) {
            echo json_encode([
                'status' => 200,
                'message' => $result['message']
            ]);
        } else {
            echo json_encode([
                'status' => 400,
                'message' => $result['message']
            ]);
        }
        
    } catch (\Exception $e) {
        error_log("Error en cambiarEstadoProveedor: " . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            'status' => 500,
            'message' => 'Error interno del servidor'
        ]);
    }
}

// Obtener proveedor por ID
public function obtenerProveedorPorId() {
    // Establecer headers JSON
    header('Content-Type: application/json');
    
    if ($this->method != 'GET') {
        http_response_code(405);
        echo json_encode(responseHTTP::status405());
        return;
    }
    
    if (empty($this->data['id_proveedor']) || !is_numeric($this->data['id_proveedor'])) {
        http_response_code(400);
        echo json_encode(responseHTTP::status400('ID de proveedor inválido'));
        return;
    }
    
    $result = comprasModel::obtenerProveedorPorId($this->data['id_proveedor']);
    
    if ($result['success']) {
        echo json_encode([
            'status' => 200,
            'data' => $result['data']
        ]);
    } else {
        echo json_encode([
            'status' => 404,
            'message' => $result['message']
        ]);
    }
}

// Editar proveedor
public function editarProveedor() {
    // Establecer headers JSON
    header('Content-Type: application/json');
    
    if ($this->method != 'POST') {
        http_response_code(405);
        echo json_encode(responseHTTP::status405());
        return;
    }
    
    // Validar datos requeridos
    $camposRequeridos = ['id_proveedor', 'nombre', 'estado'];
    foreach ($camposRequeridos as $campo) {
        if (empty($this->data[$campo])) {
            http_response_code(400);
            echo json_encode(responseHTTP::status400("El campo $campo es obligatorio"));
            return;
        }
    }
    
    // Validar ID de proveedor
    if (!is_numeric($this->data['id_proveedor']) || $this->data['id_proveedor'] <= 0) {
        http_response_code(400);
        echo json_encode(responseHTTP::status400('ID de proveedor inválido'));
        return;
    }
    
    // Validar nombre (mínimo 5 caracteres)
    if (strlen(trim($this->data['nombre'])) < 5) {
        http_response_code(400);
        echo json_encode(responseHTTP::status400('El nombre debe tener al menos 5 caracteres'));
        return;
    }
    
    // Validar estado
    if (!in_array($this->data['estado'], ['ACTIVO', 'INACTIVO'])) {
        http_response_code(400);
        echo json_encode(responseHTTP::status400('Estado inválido'));
        return;
    }
    
    // Validar correo si se proporciona
    if (!empty($this->data['correo']) && !filter_var($this->data['correo'], FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(responseHTTP::status400('El formato del correo electrónico no es válido'));
        return;
    }
    
    // Validar teléfono si se proporciona
    if (!empty($this->data['telefono']) && !preg_match('/^504\s[0-9]{4}-[0-9]{4}$/', $this->data['telefono'])) {
        http_response_code(400);
        echo json_encode(responseHTTP::status400('Formato de teléfono inválido. Use: 504 XXXX-XXXX'));
        return;
    }
    
    try {
        // Agregar usuario que modifica
        $this->data['modificado_por'] = $_SESSION['usuario']['NOMBRE'] ?? 'SISTEMA';
        
        // Editar proveedor
        $result = comprasModel::editarProveedor($this->data);
        
        if ($result['success']) {
            http_response_code(200);
            echo json_encode([
                'status' => 200,
                'message' => $result['message'],
                'data' => $result['data']
            ]);
        } else {
            http_response_code(400);
            echo json_encode([
                'status' => 400,
                'message' => $result['message']
            ]);
        }
        
    } catch (\Exception $e) {
        error_log("Error en comprasController::editarProveedor: " . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            'status' => 500,
            'message' => 'Error interno del servidor'
        ]);
    }
}

// Exportar proveedores a PDF
public function exportarProveedoresPDF() {
    // Establecer headers para PDF
    header('Content-Type: application/json');
    
    if ($this->method != 'GET') {
        http_response_code(405);
        echo json_encode(responseHTTP::status405());
        return;
    }
    
    $filtros = [
        'filtro_nombre' => $this->data['filtro_nombre'] ?? '',
        'filtro_estado' => $this->data['filtro_estado'] ?? ''
    ];
    
    try {
        $result = comprasModel::exportarProveedoresPDF($filtros);
        
        if ($result['success']) {
            echo json_encode([
                'status' => 200,
                'message' => 'Datos listos para exportar',
                'data' => $result['data'],
                'total_registros' => count($result['data'])
            ]);
        } else {
            echo json_encode([
                'status' => 400,
                'message' => $result['message']
            ]);
        }
        
    } catch (\Exception $e) {
        error_log("Error en exportarProveedoresPDF: " . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            'status' => 500,
            'message' => 'Error al generar el reporte PDF'
        ]);
    }
}

// Registrar nueva materia prima
public function registrarMateriaPrima() {
    // Establecer headers JSON PRIMERO
    header('Content-Type: application/json');
    
    // Validar método
    if ($this->method != 'POST') {
        error_log("DEBUG: Método incorrecto. Esperado: POST, Recibido: " . $this->method);
        http_response_code(405);
        echo json_encode(responseHTTP::status405());
        return;
    }
    
    error_log("DEBUG registrarMateriaPrima - Datos recibidos: " . json_encode($this->data));
    
    try {
        // Validar datos requeridos
        $camposRequeridos = ['nombre', 'id_unidad_medida', 'id_proveedor', 'minimo', 'maximo', 'precio_promedio'];
        foreach ($camposRequeridos as $campo) {
            if (empty($this->data[$campo])) {
                error_log("DEBUG: Campo requerido faltante: " . $campo);
                http_response_code(400);
                echo json_encode(responseHTTP::status400("El campo $campo es obligatorio"));
                return;
            }
        }
        
        // Validar nombre (mínimo 3 caracteres)
        if (strlen(trim($this->data['nombre'])) < 3) {
            http_response_code(400);
            echo json_encode(responseHTTP::status400('El nombre debe tener al menos 3 caracteres'));
            return;
        }
        
        // Validar números positivos
        if (!is_numeric($this->data['minimo']) || $this->data['minimo'] < 0) {
            http_response_code(400);
            echo json_encode(responseHTTP::status400('El mínimo debe ser un número positivo'));
            return;
        }
        
        if (!is_numeric($this->data['maximo']) || $this->data['maximo'] < 0) {
            http_response_code(400);
            echo json_encode(responseHTTP::status400('El máximo debe ser un número positivo'));
            return;
        }
        
        if (!is_numeric($this->data['precio_promedio']) || $this->data['precio_promedio'] <= 0) {
            http_response_code(400);
            echo json_encode(responseHTTP::status400('El precio promedio debe ser mayor a 0'));
            return;
        }
        
        // Validar que máximo sea mayor que mínimo
        if ($this->data['maximo'] <= $this->data['minimo']) {
            http_response_code(400);
            echo json_encode(responseHTTP::status400('El máximo debe ser mayor que el mínimo'));
            return;
        }
        
        // Agregar usuario que crea
        $this->data['creado_por'] = $_SESSION['usuario']['NOMBRE'] ?? 'SISTEMA';
        $this->data['id_usuario'] = $_SESSION['usuario']['ID_USUARIO'] ?? 1;
        
        error_log("DEBUG: Todas las validaciones pasadas. Llamando al modelo...");
        
        // Registrar materia prima
        $result = comprasModel::registrarMateriaPrima($this->data);
        
        if ($result['success']) {
            error_log("DEBUG: Materia prima registrada exitosamente. ID: " . $result['data']['ID_MATERIA_PRIMA']);
            http_response_code(201);
            echo json_encode([
                'status' => 201,
                'message' => $result['message'],
                'data' => $result['data']
            ]);
        } else {
            error_log("DEBUG: Error en modelo: " . $result['message']);
            http_response_code(400);
            echo json_encode([
                'status' => 400,
                'message' => $result['message']
            ]);
        }
        
    } catch (\Exception $e) {
        error_log("ERROR en comprasController::registrarMateriaPrima: " . $e->getMessage());
        error_log("ERROR Trace: " . $e->getTraceAsString());
        http_response_code(500);
        echo json_encode([
            'status' => 500,
            'message' => 'Error interno del servidor: ' . $e->getMessage()
        ]);
    }
}

// Obtener proveedores activos
public function obtenerProveedoresActivos() {
    header('Content-Type: application/json');
    
    if ($this->method != 'GET') {
        http_response_code(405);
        echo json_encode(responseHTTP::status405());
        return;
    }
    
    try {
        $proveedores = comprasModel::obtenerProveedoresActivos();
        
        echo json_encode([
            'status' => 200,
            'data' => $proveedores
        ]);
        
    } catch (\Exception $e) {
        error_log("Error en obtenerProveedoresActivos: " . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            'status' => 500,
            'message' => 'Error al obtener proveedores'
        ]);
    }
}

// Obtener unidades de medida
public function obtenerUnidadesMedida() {
    header('Content-Type: application/json');
    
    if ($this->method != 'GET') {
        http_response_code(405);
        echo json_encode(responseHTTP::status405());
        return;
    }
    
    try {
        $unidades = comprasModel::obtenerUnidadesMedida();
        
        echo json_encode([
            'status' => 200,
            'data' => $unidades
        ]);
        
    } catch (\Exception $e) {
        error_log("Error en obtenerUnidadesMedida: " . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            'status' => 500,
            'message' => 'Error al obtener unidades de medida'
        ]);
    }
}

// Listar materia prima
public function listarMateriaPrima() {
    // Establecer headers JSON
    header('Content-Type: application/json');
    
    if ($this->method != 'GET') {
        http_response_code(405);
        echo json_encode(responseHTTP::status405());
        return;
    }
    
    $filtros = [
        'filtro_nombre' => $this->data['filtro_nombre'] ?? '',
        'filtro_proveedor' => $this->data['filtro_proveedor'] ?? ''
    ];
    
    $result = comprasModel::listarMateriaPrima($filtros);
    
    if ($result['success']) {
        echo json_encode([
            'status' => 200,
            'data' => $result['data']
        ]);
    } else {
        echo json_encode([
            'status' => 400,
            'message' => $result['message']
        ]);
    }
}
}
?>