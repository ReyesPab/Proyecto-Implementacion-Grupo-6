<?php

namespace App\models;

use App\config\responseHTTP;
use App\db\connectionDB;
use PDO;

class inventarioModel {
    
    // Obtener inventario completo
    public static function obtenerInventarioCompleto() {
        try {
            $con = connectionDB::getConnection();
            
            $sql = "CALL SP_OBTENER_INVENTARIO_ACTUAL()";
            $query = $con->prepare($sql);
            $query->execute();
            
            $inventario = $query->fetchAll(PDO::FETCH_ASSOC);
            
            // DEBUG: Log para verificar datos
            error_log("DEBUG - Inventario obtenido: " . count($inventario) . " registros");
            if (!empty($inventario)) {
                error_log("DEBUG - Primer registro: " . print_r($inventario[0], true));
            }
            
            return $inventario;
            
        } catch (\PDOException $e) {
            error_log("inventarioModel::obtenerInventarioCompleto -> " . $e->getMessage());
            return [];
        }
    }
    
    // Obtener item específico del inventario
    public static function obtenerItemInventario($idInventario) {
        try {
            $con = connectionDB::getConnection();
            
            $sql = "SELECT * FROM TBL_INVENTARIO_MATERIA_PRIMA WHERE ID_INVENTARIO_MP = :id_inventario";
            $query = $con->prepare($sql);
            $query->execute(['id_inventario' => $idInventario]);
            
            if ($query->rowCount() > 0) {
                return $query->fetch(PDO::FETCH_ASSOC);
            }
            
            return null;
            
        } catch (\PDOException $e) {
            error_log("inventarioModel::obtenerItemInventario -> " . $e->getMessage());
            return null;
        }
    }
    
    // Actualizar inventario
public static function actualizarInventario($datos) {
    try {
        error_log("🎯 inventarioModel::actualizarInventario - Datos: " . print_r($datos, true));
        
        $con = connectionDB::getConnection();
        
        $sql = "CALL SP_ACTUALIZAR_INVENTARIO(:id_materia_prima, :cantidad, :tipo_movimiento, :id_usuario, :descripcion, :actualizado_por)";
        
        error_log("📝 Ejecutando SP: " . $sql);
        
        $query = $con->prepare($sql);
        $success = $query->execute([
            'id_materia_prima' => $datos['id_materia_prima'],
            'cantidad' => $datos['cantidad'],
            'tipo_movimiento' => $datos['tipo_movimiento'],
            'id_usuario' => $datos['id_usuario'],
            'descripcion' => $datos['descripcion'],
            'actualizado_por' => $datos['actualizado_por']
        ]);
        
        error_log("✅ Ejecución SQL exitosa: " . ($success ? 'Sí' : 'No'));
        
        $result = $query->fetch(PDO::FETCH_ASSOC);
        error_log("📊 Resultado del SP: " . print_r($result, true));
        
        if ($result && $result['STATUS'] === 'success') {
            return [
                'success' => true, 
                'message' => $result['MESSAGE'],
                'nuevo_stock' => $result['NUEVO_STOCK'] ?? null
            ];
        } else {
            $errorMessage = $result['MESSAGE'] ?? 'Error desconocido al actualizar inventario';
            error_log("❌ Error del SP: " . $errorMessage);
            return ['success' => false, 'message' => $errorMessage];
        }
        
    } catch (\PDOException $e) {
        error_log("💥 PDOException en actualizarInventario: " . $e->getMessage());
        error_log("💥 Código de error: " . $e->getCode());
        
        return ['success' => false, 'message' => 'Error de base de datos: ' . $e->getMessage()];
    }
}
    
    // Obtener historial de inventario
    public static function obtenerHistorialInventario($filtros = []) {
        try {
            $con = connectionDB::getConnection();
            
            $sql = "CALL SP_OBTENER_HISTORIAL_INVENTARIO(:id_materia_prima, :fecha_inicio, :fecha_fin)";
            $query = $con->prepare($sql);
            $query->execute([
                'id_materia_prima' => $filtros['id_materia_prima'] ?? null,
                'fecha_inicio' => $filtros['fecha_inicio'] ?? null,
                'fecha_fin' => $filtros['fecha_fin'] ?? null
            ]);
            
            return $query->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (\PDOException $e) {
            error_log("inventarioModel::obtenerHistorialInventario -> " . $e->getMessage());
            return [];
        }
    }
    
    // Exportar inventario para PDF
    public static function exportarInventarioPDF() {
        try {
            $con = connectionDB::getConnection();
            
            $sql = "CALL SP_EXPORTAR_INVENTARIO_PDF()";
            $query = $con->prepare($sql);
            $query->execute();
            
            return $query->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (\PDOException $e) {
            error_log("inventarioModel::exportarInventarioPDF -> " . $e->getMessage());
            return [];
        }
    }
    
    // Obtener alertas de inventario
    public static function obtenerAlertasInventario() {
        try {
            $con = connectionDB::getConnection();
            
            $sql = "CALL SP_OBTENER_ALERTAS_INVENTARIO()";
            $query = $con->prepare($sql);
            $query->execute();
            
            return $query->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (\PDOException $e) {
            error_log("inventarioModel::obtenerAlertasInventario -> " . $e->getMessage());
            return [];
        }
    }
    
    // Registrar en bitácora
    public static function registrarBitacora($idUsuario, $accion, $descripcion, $creadoPor = 'SISTEMA') {
        try {
            $con = connectionDB::getConnection();
            $sql = "INSERT INTO TBL_MS_BITACORA (ID_USUARIO, ACCION, DESCRIPCION, CREADO_POR) 
                    VALUES (:id_usuario, :accion, :descripcion, :creado_por)";
            $query = $con->prepare($sql);
            $query->execute([
                'id_usuario' => $idUsuario,
                'accion' => $accion,
                'descripcion' => $descripcion,
                'creado_por' => $creadoPor
            ]);
        } catch (\PDOException $e) {
            error_log("inventarioModel::registrarBitacora -> " . $e->getMessage());
        }
    }
}
?>