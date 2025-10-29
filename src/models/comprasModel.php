<?php

namespace App\models;

use App\config\responseHTTP;
use App\db\connectionDB;
use App\config\Security;
use PDO;

class comprasModel {
    
    // Registrar nueva compra usando procedimiento almacenado
    // En comprasModel.php - mejorar el método registrarCompra
public static function registrarCompra($datos) {
    $con = null;
    try {
        $con = connectionDB::getConnection();
        
        if (!$con) {
            throw new \PDOException("No se pudo establecer conexión con la base de datos");
        }
        
        // Convertir detalles a JSON
        $detalles_json = json_encode($datos['detalles']);
        if ($detalles_json === false) {
            throw new \Exception("Error al codificar detalles JSON: " . json_last_error_msg());
        }
        
        $sql = "CALL SP_REGISTRAR_COMPRA(:id_proveedor, :id_usuario, :detalles, :observaciones, :creado_por)";
        
        $query = $con->prepare($sql);
        
        // Ejecutar con parámetros
        $params = [
            'id_proveedor' => (int)$datos['id_proveedor'],
            'id_usuario' => (int)$datos['id_usuario'],
            'detalles' => $detalles_json,
            'observaciones' => $datos['observaciones'] ?? '',
            'creado_por' => $datos['creado_por'] ?? 'SISTEMA'
        ];
        
        $success = $query->execute($params);
        
        if (!$success) {
            $errorInfo = $query->errorInfo();
            throw new \PDOException("Error en ejecución: " . ($errorInfo[2] ?? 'Desconocido'));
        }
        
        // Obtener el resultado
        $result = $query->fetch(PDO::FETCH_ASSOC);
        
        if ($result && isset($result['ID_COMPRA'])) {
            return [
                'success' => true, 
                'message' => 'Compra registrada exitosamente',
                'id_compra' => (int)$result['ID_COMPRA'],
                'total_compra' => (float)$result['TOTAL_COMPRA']
            ];
        } else {
            throw new \Exception("No se pudo obtener el ID de la compra registrada");
        }
        
    } catch (\PDOException $e) {
        error_log("comprasModel::registrarCompra PDOException: " . $e->getMessage());
        return [
            'success' => false, 
            'message' => 'Error de base de datos: ' . $e->getMessage()
        ];
    } catch (\Exception $e) {
        error_log("comprasModel::registrarCompra Exception: " . $e->getMessage());
        return [
            'success' => false, 
            'message' => 'Error al registrar la compra: ' . $e->getMessage()
        ];
    }
}
    
    // Obtener lista de compras
    // En comprasModel.php - actualizar el método obtenerCompras
public static function obtenerCompras($filtros = []) {
    try {
        $con = connectionDB::getConnection();
        
        // Usar el nuevo procedimiento que incluye los detalles
        $sql = "CALL SP_CONSULTAR_COMPRAS_DETALLADAS(:fecha_inicio, :fecha_fin, :id_proveedor, :estado_compra)";
        
        $query = $con->prepare($sql);
        $query->execute([
            'fecha_inicio' => $filtros['fecha_inicio'],
            'fecha_fin' => $filtros['fecha_fin'],
            'id_proveedor' => $filtros['id_proveedor'],
            'estado_compra' => $filtros['estado_compra']
        ]);
        
        $compras = $query->fetchAll(PDO::FETCH_ASSOC);
        
        return [
            'success' => true,
            'data' => $compras
        ];
        
    } catch (\PDOException $e) {
        error_log("comprasModel::obtenerCompras -> " . $e->getMessage());
        return ['success' => false, 'message' => 'Error al obtener las compras: ' . $e->getMessage()];
    }
}
    
    // Obtener detalles de una compra específica
    public static function obtenerDetalleCompra($id_compra) {
        try {
            $con = connectionDB::getConnection();
            
            $sql = "CALL SP_OBTENER_DETALLE_COMPRA(:id_compra)";
            
            $query = $con->prepare($sql);
            $query->execute(['id_compra' => $id_compra]);
            
            // Obtener información de la compra (primer resultado)
            $compra = $query->fetch(PDO::FETCH_ASSOC);
            
            // Obtener detalles de la compra (segundo resultado)
            $query->nextRowset();
            $detalles = $query->fetchAll(PDO::FETCH_ASSOC);
            
            return [
                'success' => true,
                'data' => [
                    'compra' => $compra,
                    'detalles' => $detalles
                ]
            ];
            
        } catch (\PDOException $e) {
            error_log("comprasModel::obtenerDetalleCompra -> " . $e->getMessage());
            return ['success' => false, 'message' => 'Error al obtener el detalle de la compra: ' . $e->getMessage()];
        }
    }
    
    // Obtener lista de proveedores activos
    public static function obtenerProveedores() {
        try {
            $con = connectionDB::getConnection();
            
            $sql = "SELECT ID_PROVEEDOR, NOMBRE, CONTACTO, TELEFONO 
                    FROM TBL_PROVEEDOR 
                    WHERE ESTADO = 'ACTIVO' 
                    ORDER BY NOMBRE";
            
            $query = $con->prepare($sql);
            $query->execute();
            
            return $query->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (\PDOException $e) {
            error_log("comprasModel::obtenerProveedores -> " . $e->getMessage());
            return [];
        }
    }

    // En comprasModel.php - Agrega este método junto con los otros
public static function obtenerMateriaPrima() {
    try {
        $con = connectionDB::getConnection();
        
        $sql = "SELECT mp.ID_MATERIA_PRIMA, mp.NOMBRE, mp.DESCRIPCION, um.UNIDAD, 
                       mp.PRECIO_PROMEDIO, mp.ID_PROVEEDOR
                FROM TBL_MATERIA_PRIMA mp
                INNER JOIN TBL_UNIDAD_MEDIDA um ON mp.ID_UNIDAD_MEDIDA = um.ID_UNIDAD_MEDIDA
                WHERE mp.ESTADO = 'ACTIVO'
                ORDER BY mp.NOMBRE";
        
        $query = $con->prepare($sql);
        $query->execute();
        
        return $query->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (\PDOException $e) {
        error_log("comprasModel::obtenerMateriaPrima -> " . $e->getMessage());
        return [];
    }
}
    
    // Obtener lista de materia prima activa
    // En comprasModel.php - método obtenerMateriaPrimaPorProveedor
public static function obtenerMateriaPrimaPorProveedor($id_proveedor) {
    try {
        error_log("DEBUG obtenerMateriaPrimaPorProveedor - ID: " . $id_proveedor);
        
        $con = connectionDB::getConnection();
        
        if (!$con) {
            throw new \PDOException("No se pudo establecer conexión con la base de datos");
        }
        
        $sql = "CALL SP_OBTENER_MATERIA_PRIMA_POR_PROVEEDOR(:id_proveedor)";
        
        error_log("DEBUG: Ejecutando SP: " . $sql . " con ID: " . $id_proveedor);
        
        $query = $con->prepare($sql);
        $query->execute(['id_proveedor' => $id_proveedor]);
        
        $resultado = $query->fetchAll(PDO::FETCH_ASSOC);
        
        error_log("DEBUG: Resultados obtenidos: " . count($resultado) . " registros");
        error_log("DEBUG: Datos: " . json_encode($resultado));
        
        return $resultado;
        
    } catch (\PDOException $e) {
        error_log("ERROR comprasModel::obtenerMateriaPrimaPorProveedor -> " . $e->getMessage());
        error_log("ERROR Info: " . json_encode($e->getTrace()));
        return [];
    }
}

public function obtenerComprasParaReporte($fecha_inicio = '', $fecha_fin = '', $id_proveedor = '', $estado_compra = '') {
    try {
        $sql = "SELECT DISTINCT
                    c.ID_COMPRA,
                    c.FECHA_COMPRA,
                    c.ESTADO_COMPRA,
                    c.DESCUENTO,
                    p.NOMBRE AS PROVEEDOR,
                    u.NOMBRE AS USUARIO,
                    (SELECT SUM(dc.CANTIDAD * dc.PRECIO_UNITARIO) 
                     FROM detalle_compras dc 
                     WHERE dc.ID_COMPRA = c.ID_COMPRA) AS SUBTOTAL
                FROM compras c
                INNER JOIN proveedores p ON c.ID_PROVEEDOR = p.ID_PROVEEDOR
                INNER JOIN usuarios u ON c.ID_USUARIO = u.ID_USUARIO
                WHERE 1=1";
        
        $params = [];
        
        // Aplicar filtros
        if (!empty($fecha_inicio)) {
            $sql .= " AND DATE(c.FECHA_COMPRA) >= ?";
            $params[] = $fecha_inicio;
        }
        
        if (!empty($fecha_fin)) {
            $sql .= " AND DATE(c.FECHA_COMPRA) <= ?";
            $params[] = $fecha_fin;
        }
        
        if (!empty($id_proveedor)) {
            $sql .= " AND c.ID_PROVEEDOR = ?";
            $params[] = $id_proveedor;
        }
        
        if (!empty($estado_compra)) {
            $sql .= " AND c.ESTADO_COMPRA = ?";
            $params[] = $estado_compra;
        }
        
        $sql .= " ORDER BY c.FECHA_COMPRA DESC, c.ID_COMPRA DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $compras = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return [
            'success' => true,
            'data' => $compras
        ];
        
    } catch (PDOException $e) {
        error_log("Error en obtenerComprasParaReporte: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Error al obtener compras para reporte'
        ];
    }
}

// Registrar nuevo proveedor
public static function registrarProveedor($datos) {
    $con = null;
    try {
        $con = connectionDB::getConnection();
        
        if (!$con) {
            throw new \PDOException("No se pudo establecer conexión con la base de datos");
        }
        
        $sql = "CALL SP_REGISTRAR_PROVEEDOR(:nombre, :contacto, :telefono, :correo, :direccion, :creado_por)";
        
        $query = $con->prepare($sql);
        
        // Ejecutar con parámetros
        $params = [
            'nombre' => $datos['nombre'],
            'contacto' => $datos['contacto'] ?? '',
            'telefono' => $datos['telefono'] ?? '',
            'correo' => $datos['correo'] ?? '',
            'direccion' => $datos['direccion'] ?? '',
            'creado_por' => $datos['creado_por'] ?? 'SISTEMA'
        ];
        
        $success = $query->execute($params);
        
        if (!$success) {
            $errorInfo = $query->errorInfo();
            throw new \PDOException("Error en ejecución: " . ($errorInfo[2] ?? 'Desconocido'));
        }
        
        // Obtener el resultado
        $result = $query->fetch(PDO::FETCH_ASSOC);
        
        if ($result && isset($result['ID_PROVEEDOR'])) {
            return [
                'success' => true, 
                'message' => 'Proveedor registrado exitosamente',
                'data' => $result
            ];
        } else {
            throw new \Exception("No se pudo obtener el ID del proveedor registrado");
        }
        
    } catch (\PDOException $e) {
        error_log("comprasModel::registrarProveedor PDOException: " . $e->getMessage());
        return [
            'success' => false, 
            'message' => 'Error de base de datos: ' . $e->getMessage()
        ];
    } catch (\Exception $e) {
        error_log("comprasModel::registrarProveedor Exception: " . $e->getMessage());
        return [
            'success' => false, 
            'message' => 'Error al registrar el proveedor: ' . $e->getMessage()
        ];
    }
}

// Método para validar si el proveedor ya existe
public static function validarProveedorExistente($nombre, $correo = '') {
    try {
        $con = connectionDB::getConnection();
        
        $sql = "SELECT ID_PROVEEDOR, NOMBRE, CORREO 
                FROM TBL_PROVEEDOR 
                WHERE NOMBRE = :nombre 
                   OR (CORREO = :correo AND CORREO != '')";
        
        $query = $con->prepare($sql);
        $query->execute([
            'nombre' => $nombre,
            'correo' => $correo
        ]);
        
        $resultado = $query->fetch(PDO::FETCH_ASSOC);
        
        return $resultado ?: null;
        
    } catch (\PDOException $e) {
        error_log("comprasModel::validarProveedorExistente -> " . $e->getMessage());
        return null;
    }
}

// Listar proveedores con filtros
public static function listarProveedores($filtros = []) {
    try {
        $con = connectionDB::getConnection();
        
        if (!$con) {
            throw new \PDOException("No se pudo establecer conexión con la base de datos");
        }
        
        $sql = "CALL SP_LISTAR_PROVEEDORES(:filtro_nombre, :filtro_estado)";
        
        $query = $con->prepare($sql);
        
        $params = [
            'filtro_nombre' => $filtros['filtro_nombre'] ?? '',
            'filtro_estado' => $filtros['filtro_estado'] ?? ''
        ];
        
        $success = $query->execute($params);
        
        if (!$success) {
            $errorInfo = $query->errorInfo();
            throw new \PDOException("Error en ejecución: " . ($errorInfo[2] ?? 'Desconocido'));
        }
        
        $proveedores = $query->fetchAll(PDO::FETCH_ASSOC);
        
        return [
            'success' => true,
            'data' => $proveedores
        ];
        
    } catch (\PDOException $e) {
        error_log("comprasModel::listarProveedores PDOException: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Error de base de datos: ' . $e->getMessage()
        ];
    } catch (\Exception $e) {
        error_log("comprasModel::listarProveedores Exception: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Error al listar proveedores: ' . $e->getMessage()
        ];
    }
}

// Método para cambiar estado de proveedor
public static function cambiarEstadoProveedor($id_proveedor, $estado, $modificado_por) {
    try {
        $con = connectionDB::getConnection();
        
        $sql = "UPDATE TBL_PROVEEDOR 
                SET ESTADO = :estado, 
                    MODIFICADO_POR = :modificado_por,
                    FECHA_MODIFICACION = NOW()
                WHERE ID_PROVEEDOR = :id_proveedor";
        
        $query = $con->prepare($sql);
        $success = $query->execute([
            'estado' => $estado,
            'modificado_por' => $modificado_por,
            'id_proveedor' => $id_proveedor
        ]);
        
        if ($success && $query->rowCount() > 0) {
            return [
                'success' => true,
                'message' => 'Estado del proveedor actualizado correctamente'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'No se pudo actualizar el estado del proveedor'
            ];
        }
        
    } catch (\PDOException $e) {
        error_log("comprasModel::cambiarEstadoProveedor -> " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Error de base de datos: ' . $e->getMessage()
        ];
    }
}

// Obtener proveedor por ID
public static function obtenerProveedorPorId($id_proveedor) {
    try {
        $con = connectionDB::getConnection();
        
        if (!$con) {
            throw new \PDOException("No se pudo establecer conexión con la base de datos");
        }
        
        $sql = "CALL SP_OBTENER_PROVEEDOR_POR_ID(:id_proveedor)";
        
        $query = $con->prepare($sql);
        $success = $query->execute(['id_proveedor' => $id_proveedor]);
        
        if (!$success) {
            $errorInfo = $query->errorInfo();
            throw new \PDOException("Error en ejecución: " . ($errorInfo[2] ?? 'Desconocido'));
        }
        
        $proveedor = $query->fetch(PDO::FETCH_ASSOC);
        
        if ($proveedor) {
            return [
                'success' => true,
                'data' => $proveedor
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Proveedor no encontrado'
            ];
        }
        
    } catch (\PDOException $e) {
        error_log("comprasModel::obtenerProveedorPorId PDOException: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Error de base de datos: ' . $e->getMessage()
        ];
    } catch (\Exception $e) {
        error_log("comprasModel::obtenerProveedorPorId Exception: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Error al obtener el proveedor: ' . $e->getMessage()
        ];
    }
}

// Editar proveedor
public static function editarProveedor($datos) {
    $con = null;
    try {
        $con = connectionDB::getConnection();
        
        if (!$con) {
            throw new \PDOException("No se pudo establecer conexión con la base de datos");
        }
        
        $sql = "CALL SP_EDITAR_PROVEEDOR(:id_proveedor, :nombre, :contacto, :telefono, :correo, :direccion, :estado, :modificado_por)";
        
        $query = $con->prepare($sql);
        
        // Ejecutar con parámetros
        $params = [
            'id_proveedor' => (int)$datos['id_proveedor'],
            'nombre' => $datos['nombre'],
            'contacto' => $datos['contacto'] ?? '',
            'telefono' => $datos['telefono'] ?? '',
            'correo' => $datos['correo'] ?? '',
            'direccion' => $datos['direccion'] ?? '',
            'estado' => $datos['estado'],
            'modificado_por' => $datos['modificado_por'] ?? 'SISTEMA'
        ];
        
        $success = $query->execute($params);
        
        if (!$success) {
            $errorInfo = $query->errorInfo();
            throw new \PDOException("Error en ejecución: " . ($errorInfo[2] ?? 'Desconocido'));
        }
        
        // Obtener el resultado
        $result = $query->fetch(PDO::FETCH_ASSOC);
        
        if ($result && isset($result['ID_PROVEEDOR'])) {
            return [
                'success' => true, 
                'message' => 'Proveedor actualizado exitosamente',
                'data' => $result
            ];
        } else {
            throw new \Exception("No se pudo obtener el proveedor actualizado");
        }
        
    } catch (\PDOException $e) {
        error_log("comprasModel::editarProveedor PDOException: " . $e->getMessage());
        return [
            'success' => false, 
            'message' => 'Error de base de datos: ' . $e->getMessage()
        ];
    } catch (\Exception $e) {
        error_log("comprasModel::editarProveedor Exception: " . $e->getMessage());
        return [
            'success' => false, 
            'message' => 'Error al actualizar el proveedor: ' . $e->getMessage()
        ];
    }
}

// Exportar proveedores para PDF
public static function exportarProveedoresPDF($filtros = []) {
    try {
        $con = connectionDB::getConnection();
        
        if (!$con) {
            throw new \PDOException("No se pudo establecer conexión con la base de datos");
        }
        
        $sql = "CALL SP_EXPORTAR_PROVEEDORES_PDF(:filtro_nombre, :filtro_estado)";
        
        $query = $con->prepare($sql);
        
        $params = [
            'filtro_nombre' => $filtros['filtro_nombre'] ?? '',
            'filtro_estado' => $filtros['filtro_estado'] ?? ''
        ];
        
        $success = $query->execute($params);
        
        if (!$success) {
            $errorInfo = $query->errorInfo();
            throw new \PDOException("Error en ejecución: " . ($errorInfo[2] ?? 'Desconocido'));
        }
        
        $proveedores = $query->fetchAll(PDO::FETCH_ASSOC);
        
        return [
            'success' => true,
            'data' => $proveedores,
            'total' => count($proveedores)
        ];
        
    } catch (\PDOException $e) {
        error_log("comprasModel::exportarProveedoresPDF PDOException: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Error de base de datos: ' . $e->getMessage()
        ];
    } catch (\Exception $e) {
        error_log("comprasModel::exportarProveedoresPDF Exception: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Error al exportar proveedores: ' . $e->getMessage()
        ];
    }
}

// Registrar nueva materia prima
public static function registrarMateriaPrima($datos) {
    $con = null;
    try {
        $con = connectionDB::getConnection();
        
        if (!$con) {
            throw new \PDOException("No se pudo establecer conexión con la base de datos");
        }
        
        $sql = "CALL SP_REGISTRAR_MATERIA_PRIMA(:nombre, :descripcion, :id_unidad_medida, :id_proveedor, :minimo, :maximo, :precio_promedio, :creado_por, :id_usuario, @id_materia_prima, @mensaje)";
        
        $query = $con->prepare($sql);
        
        // Ejecutar con parámetros
        $params = [
            'nombre' => $datos['nombre'],
            'descripcion' => $datos['descripcion'] ?? '',
            'id_unidad_medida' => (int)$datos['id_unidad_medida'],
            'id_proveedor' => (int)$datos['id_proveedor'],
            'minimo' => (float)$datos['minimo'],
            'maximo' => (float)$datos['maximo'],
            'precio_promedio' => (float)$datos['precio_promedio'],
            'creado_por' => $datos['creado_por'] ?? 'SISTEMA',
            'id_usuario' => (int)$datos['id_usuario']
        ];
        
        $success = $query->execute($params);
        
        if (!$success) {
            $errorInfo = $query->errorInfo();
            throw new \PDOException("Error en ejecución: " . ($errorInfo[2] ?? 'Desconocido'));
        }
        
        // Obtener los parámetros de salida
        $query = $con->query("SELECT @id_materia_prima as ID_MATERIA_PRIMA, @mensaje as MENSAJE");
        $result = $query->fetch(PDO::FETCH_ASSOC);
        
        if ($result && $result['ID_MATERIA_PRIMA'] > 0) {
            return [
                'success' => true, 
                'message' => $result['MENSAJE'],
                'data' => [
                    'ID_MATERIA_PRIMA' => (int)$result['ID_MATERIA_PRIMA'],
                    'NOMBRE' => $datos['nombre']
                ]
            ];
        } else {
            return [
                'success' => false, 
                'message' => $result['MENSAJE'] ?? 'Error al registrar la materia prima'
            ];
        }
        
    } catch (\PDOException $e) {
        error_log("comprasModel::registrarMateriaPrima PDOException: " . $e->getMessage());
        return [
            'success' => false, 
            'message' => 'Error de base de datos: ' . $e->getMessage()
        ];
    } catch (\Exception $e) {
        error_log("comprasModel::registrarMateriaPrima Exception: " . $e->getMessage());
        return [
            'success' => false, 
            'message' => 'Error al registrar la materia prima: ' . $e->getMessage()
        ];
    }
}

// Obtener proveedores activos
public static function obtenerProveedoresActivos() {
    try {
        $con = connectionDB::getConnection();
        
        $sql = "CALL SP_OBTENER_PROVEEDORES_ACTIVOS()";
        
        $query = $con->prepare($sql);
        $query->execute();
        
        return $query->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (\PDOException $e) {
        error_log("comprasModel::obtenerProveedoresActivos -> " . $e->getMessage());
        return [];
    }
}

// Obtener unidades de medida
public static function obtenerUnidadesMedida() {
    try {
        $con = connectionDB::getConnection();
        
        $sql = "CALL SP_OBTENER_UNIDADES_MEDIDA()";
        
        $query = $con->prepare($sql);
        $query->execute();
        
        return $query->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (\PDOException $e) {
        error_log("comprasModel::obtenerUnidadesMedida -> " . $e->getMessage());
        return [];
    }
}

// Listar materia prima con filtros
public static function listarMateriaPrima($filtros = []) {
    try {
        $con = connectionDB::getConnection();
        
        if (!$con) {
            throw new \PDOException("No se pudo establecer conexión con la base de datos");
        }
        
        $sql = "CALL SP_LISTAR_MATERIA_PRIMA(:filtro_nombre, :filtro_proveedor)";
        
        $query = $con->prepare($sql);
        
        $params = [
            'filtro_nombre' => $filtros['filtro_nombre'] ?? '',
            'filtro_proveedor' => $filtros['filtro_proveedor'] ?? ''
        ];
        
        $success = $query->execute($params);
        
        if (!$success) {
            $errorInfo = $query->errorInfo();
            throw new \PDOException("Error en ejecución: " . ($errorInfo[2] ?? 'Desconocido'));
        }
        
        $materiaPrima = $query->fetchAll(PDO::FETCH_ASSOC);
        
        return [
            'success' => true,
            'data' => $materiaPrima
        ];
        
    } catch (\PDOException $e) {
        error_log("comprasModel::listarMateriaPrima PDOException: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Error de base de datos: ' . $e->getMessage()
        ];
    } catch (\Exception $e) {
        error_log("comprasModel::listarMateriaPrima Exception: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Error al listar materia prima: ' . $e->getMessage()
        ];
    }
}
}
?>