<?php

namespace App\controllers;

use App\config\responseHTTP;
use App\config\Security;
use App\models\inventarioModel;
use PDO;

class inventarioController {
    
    private $method;
    private $data;
    
    public function __construct($method, $data) {
        $this->method = $method;
        $this->data = Security::sanitizeInput($data);
    }
    
    // Listar inventario completo
    public function listarInventario() {
        try {
            $inventario = inventarioModel::obtenerInventarioCompleto();
            
            if (empty($inventario)) {
                echo json_encode([
                    'status' => 200,
                    'data' => ['inventario' => []],
                    'message' => 'No hay registros en el inventario'
                ]);
                return;
            }
            
            echo json_encode([
                'status' => 200,
                'data' => ['inventario' => $inventario],
                'message' => 'Inventario obtenido correctamente'
            ]);
            
        } catch (\Exception $e) {
            error_log("inventarioController::listarInventario -> " . $e->getMessage());
            echo json_encode(responseHTTP::status500('Error al obtener el inventario'));
        }
    }
    
    // Obtener item específico del inventario
    public function obtenerItemInventario() {
        if ($this->method != 'get') {
            echo json_encode(responseHTTP::status405());
            return;
        }
        
        if (empty($this->data['id_inventario'])) {
            echo json_encode(responseHTTP::status400('ID de inventario requerido'));
            return;
        }
        
        $item = inventarioModel::obtenerItemInventario($this->data['id_inventario']);
        
        if ($item) {
            echo json_encode(responseHTTP::status200('Item de inventario obtenido', ['item' => $item]));
        } else {
            echo json_encode(responseHTTP::status404('Item de inventario no encontrado'));
        }
    }
    
    // Actualizar inventario
public function actualizarInventario() {
    error_log("🔍 actualizarInventario llamado - Method: " . $this->method);
    
    if ($this->method != 'post') {
        error_log("❌ Método no permitido: " . $this->method);
        echo json_encode(responseHTTP::status405());
        return;
    }
    
    // Log de datos recibidos
    error_log("📥 Datos recibidos: " . print_r($this->data, true));
    
    // Validar datos requeridos
    $camposRequeridos = ['id_materia_prima', 'cantidad', 'tipo_movimiento', 'descripcion'];
    $camposFaltantes = [];
    
    foreach ($camposRequeridos as $campo) {
        if (empty($this->data[$campo])) {
            $camposFaltantes[] = $campo;
        }
    }
    
    if (!empty($camposFaltantes)) {
        error_log("❌ Campos faltantes: " . implode(', ', $camposFaltantes));
        echo json_encode(responseHTTP::status400("Campos obligatorios faltantes: " . implode(', ', $camposFaltantes)));
        return;
    }
    
    // Validar tipo de movimiento
    $tiposPermitidos = ['ENTRADA', 'SALIDA', 'AJUSTE'];
    $tipoMovimiento = strtoupper($this->data['tipo_movimiento']);
    
    if (!in_array($tipoMovimiento, $tiposPermitidos)) {
        error_log("❌ Tipo movimiento inválido: " . $tipoMovimiento);
        echo json_encode(responseHTTP::status400('Tipo de movimiento no válido. Use: ENTRADA, SALIDA o AJUSTE'));
        return;
    }
    
    // Validar cantidad
    if (!is_numeric($this->data['cantidad']) || $this->data['cantidad'] <= 0) {
        error_log("❌ Cantidad inválida: " . $this->data['cantidad']);
        echo json_encode(responseHTTP::status400('La cantidad debe ser un número positivo mayor a 0'));
        return;
    }
    
    // Asegurar que los datos tengan el formato correcto
    $datosLimpios = [
        'id_materia_prima' => (int)$this->data['id_materia_prima'],
        'cantidad' => (float)$this->data['cantidad'],
        'tipo_movimiento' => $tipoMovimiento,
        'descripcion' => trim($this->data['descripcion']),
        'id_usuario' => $this->data['id_usuario'] ?? 1,
        'actualizado_por' => $this->data['actualizado_por'] ?? 'SISTEMA'
    ];
    
    error_log("🧹 Datos limpios para modelo: " . print_r($datosLimpios, true));
    
    // Actualizar inventario
    $result = inventarioModel::actualizarInventario($datosLimpios);
    
    error_log("📤 Resultado del modelo: " . print_r($result, true));
    
    if ($result['success']) {
        echo json_encode(responseHTTP::status200($result['message'], [
            'nuevo_stock' => $result['nuevo_stock'] ?? null
        ]));
    } else {
        echo json_encode(responseHTTP::status400($result['message']));
    }
}
    
    // Obtener historial de movimientos
    public function obtenerHistorial() {
        if ($this->method != 'get') {
            echo json_encode(responseHTTP::status405());
            return;
        }
        
        $filtros = [
            'id_materia_prima' => $this->data['id_materia_prima'] ?? null,
            'fecha_inicio' => $this->data['fecha_inicio'] ?? null,
            'fecha_fin' => $this->data['fecha_fin'] ?? null
        ];
        
        try {
            $historial = inventarioModel::obtenerHistorialInventario($filtros);
            
            echo json_encode([
                'status' => 200,
                'data' => ['historial' => $historial],
                'message' => 'Historial obtenido correctamente'
            ]);
            
        } catch (\Exception $e) {
            error_log("inventarioController::obtenerHistorial -> " . $e->getMessage());
            echo json_encode(responseHTTP::status500('Error al obtener el historial'));
        }
    }
    
    // Exportar inventario a PDF
    public function exportarInventarioPDF() {
        if ($this->method != 'get') {
            echo json_encode(responseHTTP::status405());
            return;
        }
        
        try {
            $inventario = inventarioModel::exportarInventarioPDF();
            
            if (empty($inventario)) {
                echo json_encode(responseHTTP::status404('No hay datos para exportar'));
                return;
            }
            
            echo json_encode([
                'status' => 200,
                'message' => 'Datos de inventario obtenidos para exportación',
                'data' => ['inventario' => $inventario]
            ]);
            
        } catch (\Exception $e) {
            error_log("Error en exportarInventarioPDF: " . $e->getMessage());
            echo json_encode(responseHTTP::status500('Error al exportar inventario'));
        }
    }
    
    // Obtener alertas de inventario
    public function obtenerAlertas() {
        if ($this->method != 'get') {
            echo json_encode(responseHTTP::status405());
            return;
        }
        
        try {
            $alertas = inventarioModel::obtenerAlertasInventario();
            
            echo json_encode([
                'status' => 200,
                'data' => ['alertas' => $alertas],
                'message' => 'Alertas obtenidas correctamente'
            ]);
            
        } catch (\Exception $e) {
            error_log("inventarioController::obtenerAlertas -> " . $e->getMessage());
            echo json_encode(responseHTTP::status500('Error al obtener las alertas'));
        }
    }
}
?>