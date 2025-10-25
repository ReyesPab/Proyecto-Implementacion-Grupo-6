<?php

namespace App\models;

use App\config\responseHTTP;
use App\db\connectionDB;
use App\config\Security;
use PDO;

class userModel {
    
    // Crear nuevo usuario usando procedimiento almacenado
    public static function crearUsuario($datos) {
        try {
            $con = connectionDB::getConnection();
            
            // Usar procedimiento almacenado
            $sql = "CALL SP_CREAR_USUARIO(:numero_identidad, :usuario, :nombre_usuario, :password, :id_rol, :correo_electronico, :creado_por)";
            
            $query = $con->prepare($sql);
            $query->execute([
                'numero_identidad' => $datos['numero_identidad'] ?? null,
                'usuario' => $datos['usuario'],
                'nombre_usuario' => $datos['nombre_usuario'],
                'password' => $datos['contraseña'],
                'id_rol' => $datos['id_rol'],
                'correo_electronico' => $datos['correo_electronico'] ?? null,
                'creado_por' => $datos['creado_por'] ?? 'SISTEMA'
            ]);
            
            $result = $query->fetch(PDO::FETCH_ASSOC);
            
            if ($result && $result['STATUS'] === 'success') {
                // Obtener el ID del usuario recién creado
                $idUsuario = self::obtenerIdUsuario($datos['usuario']);
                
                return [
                    'success' => true, 
                    'message' => $result['MESSAGE'],
                    'id_usuario' => $idUsuario
                ];
            } else {
                $errorMessage = $result['MESSAGE'] ?? 'Error desconocido al crear usuario';
                return ['success' => false, 'message' => $errorMessage];
            }
            
        } catch (\PDOException $e) {
            error_log("userModel::crearUsuario -> " . $e->getMessage());
            return ['success' => false, 'message' => 'Error al crear el usuario: ' . $e->getMessage()];
        }
    }
    
    // Obtener ID de usuario por nombre de usuario
    private static function obtenerIdUsuario($usuario) {
        try {
            $con = connectionDB::getConnection();
            $sql = "SELECT ID_USUARIO FROM TBL_MS_USUARIOS WHERE USUARIO = :usuario";
            $query = $con->prepare($sql);
            $query->execute(['usuario' => strtoupper($usuario)]);
            $result = $query->fetch(PDO::FETCH_ASSOC);
            return $result['ID_USUARIO'] ?? null;
        } catch (\PDOException $e) {
            error_log("userModel::obtenerIdUsuario -> " . $e->getMessage());
            return null;
        }
    }
    
    // Listar todos los usuarios usando procedimiento almacenado
    // Listar todos los usuarios usando procedimiento almacenado
public static function listarUsuarios() {
    try {
        $con = connectionDB::getConnection();
        
        $sql = "CALL SP_OBTENER_USUARIOS()";
        $query = $con->prepare($sql);
        $query->execute();
        
        $usuarios = $query->fetchAll(PDO::FETCH_ASSOC);
        
        // DEBUG: Ver qué campos están llegando
        error_log("DEBUG - Campos recibidos del SP_OBTENER_USUARIOS:");
        if (!empty($usuarios)) {
            error_log("Primer usuario: " . print_r($usuarios[0], true));
            error_log("Todos los campos disponibles: " . implode(', ', array_keys($usuarios[0])));
        } else {
            error_log("No se obtuvieron usuarios");
        }
        
        // Si el SP no devuelve ID_USUARIO, obtenerlo manualmente
        foreach ($usuarios as &$usuario) {
            // Si no tiene ID_USUARIO, obtenerlo por el nombre de usuario
            if (!isset($usuario['ID_USUARIO']) && isset($usuario['USUARIO'])) {
                $idUsuario = self::obtenerIdUsuario($usuario['USUARIO']);
                if ($idUsuario) {
                    $usuario['ID_USUARIO'] = $idUsuario;
                }
            }
            
            // Enmascarar contraseñas
            if (isset($usuario['CONTRASENA'])) {
                $usuario['CONTRASENA_MOSTRAR'] = Security::enmascararPassword($usuario['CONTRASENA']);
            } else {
                $usuario['CONTRASENA_MOSTRAR'] = '***';
            }
        }
        
        return $usuarios;
        
    } catch (\PDOException $e) {
        error_log("userModel::listarUsuarios -> " . $e->getMessage());
        return [];
    }
}
    
    // Obtener usuario por ID
    public static function obtenerUsuario($idUsuario) {
        try {
            $con = connectionDB::getConnection();
            $sql = "SELECT U.*, R.ROL 
                    FROM TBL_MS_USUARIOS U 
                    INNER JOIN TBL_MS_ROLES R ON U.ID_ROL = R.ID_ROL 
                    WHERE U.ID_USUARIO = :id_usuario";
            
            $query = $con->prepare($sql);
            $query->execute(['id_usuario' => $idUsuario]);
            
            if ($query->rowCount() > 0) {
                $usuario = $query->fetch(PDO::FETCH_ASSOC);
                $usuario['CONTRASENA_MOSTRAR'] = Security::enmascararPassword($usuario['CONTRASENA']);
                return $usuario;
            }
            
            return null;
            
        } catch (\PDOException $e) {
            error_log("userModel::obtenerUsuario -> " . $e->getMessage());
            return null;
        }
    }
    
// Actualizar usuario usando procedimiento almacenado CORREGIDO
public static function actualizarUsuario($idUsuario, $datos) {
    try {
        $con = connectionDB::getConnection();
        
        // Construir la consulta dinámicamente
        $updates = [];
        $params = ['id_usuario' => $idUsuario];
        
        // Campos que se pueden actualizar
        $camposPermitidos = [
            'numero_identidad', 
            'nombre_usuario', 
            'id_rol', 
            'estado_usuario', 
            'correo_electronico',
            'fecha_vencimiento'
        ];
        
        foreach ($camposPermitidos as $campo) {
            if (isset($datos[$campo])) {
                $updates[] = "$campo = :$campo";
                $params[$campo] = $datos[$campo];
            }
        }
        
        // Manejar contraseña si se proporciona
        if (!empty($datos['nueva_contraseña'])) {
            $updates[] = "CONTRASENA = :contrasena";
            $params['contrasena'] = Security::createPassword($datos['nueva_contraseña']);
        }
        
        // Siempre actualizar fecha de modificación y quién modificó
        $updates[] = "FECHA_MODIFICACION = NOW()";
        $updates[] = "MODIFICADO_POR = :modificado_por";
        $params['modificado_por'] = $datos['modificado_por'] ?? 'SISTEMA';
        
        if (empty($updates)) {
            return ['success' => false, 'message' => 'No hay datos para actualizar'];
        }
        
        $sql = "UPDATE TBL_MS_USUARIOS SET " . implode(', ', $updates) . " WHERE ID_USUARIO = :id_usuario";
        
        $query = $con->prepare($sql);
        $query->execute($params);
        
        // Registrar en bitácora
        self::registrarBitacora($idUsuario, 'ACTUALIZAR_USUARIO', 'Usuario actualizado', $params['modificado_por']);
        
        return ['success' => true, 'message' => 'Usuario actualizado correctamente'];
        
    } catch (\PDOException $e) {
        error_log("userModel::actualizarUsuario -> " . $e->getMessage());
        return ['success' => false, 'message' => 'Error al actualizar el usuario: ' . $e->getMessage()];
    }
}
    
    // Resetear contraseña usando procedimiento almacenado
    public static function resetearPassword($idUsuario, $nuevaPassword, $modificadoPor = 'SISTEMA') {
        try {
            $con = connectionDB::getConnection();
            
            // Usar el mismo procedimiento que authModel::cambiarPassword sin contraseña actual
            $result = authModel::cambiarPassword($idUsuario, $nuevaPassword, null, $modificadoPor);
            
            return $result;
            
        } catch (\PDOException $e) {
            error_log("userModel::resetearPassword -> " . $e->getMessage());
            return ['success' => false, 'message' => 'Error al resetear la contraseña'];
        }
    }
    
    // Verificar si usuario existe
    public static function usuarioExiste($usuario) {
        try {
            $con = connectionDB::getConnection();
            $sql = "SELECT COUNT(*) as EXISTE FROM TBL_MS_USUARIOS WHERE USUARIO = :usuario";
            $query = $con->prepare($sql);
            $query->execute(['usuario' => strtoupper($usuario)]);
            $result = $query->fetch(PDO::FETCH_ASSOC);
            return $result['EXISTE'] > 0;
        } catch (\PDOException $e) {
            error_log("userModel::usuarioExiste -> " . $e->getMessage());
            return true; // Por seguridad, asumir que existe si hay error
        }
    }

    // Verificar si un número de identidad ya existe (opcionalmente excluir un ID de usuario)
    public static function numeroIdentidadExiste($numeroIdentidad, $excludeId = null) {
        try {
            $con = connectionDB::getConnection();

            // Normalizar número: quitar todo lo que no sea dígito
            $numero = preg_replace('/\D/', '', $numeroIdentidad);

            $sql = "SELECT COUNT(*) as EXISTE FROM TBL_MS_USUARIOS WHERE REPLACE(NUMERO_IDENTIDAD, '-', '') = :numero";
            if (!empty($excludeId)) {
                $sql .= " AND ID_USUARIO != :excludeId";
            }

            $query = $con->prepare($sql);
            $params = ['numero' => $numero];
            if (!empty($excludeId)) $params['excludeId'] = $excludeId;
            $query->execute($params);
            $result = $query->fetch(PDO::FETCH_ASSOC);
            return ($result && $result['EXISTE'] > 0);
        } catch (\PDOException $e) {
            error_log("userModel::numeroIdentidadExiste -> " . $e->getMessage());
            return true; // por seguridad, asumir que existe
        }
    }
    
// Verificar si correo existe (con exclusión opcional)
public static function correoExiste($correo, $excludeId = null) {
    try {
        $con = connectionDB::getConnection();
        $sql = "SELECT COUNT(*) as EXISTE FROM TBL_MS_USUARIOS WHERE CORREO_ELECTRONICO = :correo";
        
        if (!empty($excludeId)) {
            $sql .= " AND ID_USUARIO != :exclude_id";
        }
        
        $query = $con->prepare($sql);
        $params = ['correo' => $correo];
        
        if (!empty($excludeId)) {
            $params['exclude_id'] = $excludeId;
        }
        
        $query->execute($params);
        $result = $query->fetch(PDO::FETCH_ASSOC);
        return $result['EXISTE'] > 0;
    } catch (\PDOException $e) {
        error_log("userModel::correoExiste -> " . $e->getMessage());
        return true;
    }
}
    
    // Obtener todos los roles usando procedimiento almacenado
    public static function obtenerRoles() {
        try {
            $con = connectionDB::getConnection();
            
            $sql = "CALL SP_OBTENER_ROLES()";
            $query = $con->prepare($sql);
            $query->execute();
            
            return $query->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (\PDOException $e) {
            error_log("userModel::obtenerRoles -> " . $e->getMessage());
            return [];
        }
    }
    
    // Obtener parámetros del sistema
    public static function obtenerParametros() {
        try {
            $con = connectionDB::getConnection();
            
            $sql = "CALL SP_OBTENER_PARAMETROS()";
            $query = $con->prepare($sql);
            $query->execute();
            
            return $query->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (\PDOException $e) {
            error_log("userModel::obtenerParametros -> " . $e->getMessage());
            return [];
        }
    }
    
    // Obtener bitácora
    public static function obtenerBitacora() {
        try {
            $con = connectionDB::getConnection();
            // Algunos drivers/procedimientos pueden devolver múltiples resultsets.
            // Activamos emulate prepares localmente para evitar problemas con procedimientos
            // que retornan metadatos o múltiples sets y para permitir nextRowset().
            try {
                $con->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
            } catch (\Exception $e) {
                // No crítico, pero registrar para diagnóstico
                error_log("userModel::obtenerBitacora - no se pudo setear EMULATE_PREPARES: " . $e->getMessage());
            }

            $sql = "CALL SP_OBTENER_BITACORA()";
            $query = $con->prepare($sql);
            $query->execute();

            $allRows = [];
            // Recolectar filas de todos los resultsets
            do {
                $rows = $query->fetchAll(PDO::FETCH_ASSOC);
                if ($rows && is_array($rows) && count($rows) > 0) {
                    $allRows = array_merge($allRows, $rows);
                }
            } while ($query->nextRowset());

            // DEBUG: registrar conteo y muestra
            error_log("userModel::obtenerBitacora -> filas obtenidas: " . count($allRows));
            if (!empty($allRows)) {
                error_log("userModel::obtenerBitacora - primer registro: " . print_r($allRows[0], true));
            }

            // Intentar resolver ID_OBJETO a un nombre legible (OBJETO)
            $objetoCache = [];
            foreach ($allRows as &$row) {
                // Si ya viene con OBJETO lleno, respetarlo
                if (!empty($row['OBJETO'])) continue;

                // Si no hay ID_OBJETO, nada que mapear
                if (empty($row['ID_OBJETO'])) {
                    // Intentar con posibles claves minúsculas
                    if (!empty($row['id_objeto'])) {
                        $row['OBJETO'] = 'Objeto #' . $row['id_objeto'];
                    }
                    continue;
                }

                $idObj = $row['ID_OBJETO'];

                if (isset($objetoCache[$idObj])) {
                    $row['OBJETO'] = $objetoCache[$idObj];
                    continue;
                }

                $nombre = self::obtenerNombreObjetoPorId($con, $idObj);
                if ($nombre) {
                    $objetoCache[$idObj] = $nombre;
                    $row['OBJETO'] = $nombre;
                } else {
                    $row['OBJETO'] = 'Objeto #' . $idObj;
                    $objetoCache[$idObj] = $row['OBJETO'];
                }
            }
            unset($row);

            return $allRows;
            
        } catch (\PDOException $e) {
            error_log("userModel::obtenerBitacora -> " . $e->getMessage());
            return [];
        }
    }

    // Cambiar estado de usuario
    public static function cambiarEstadoUsuario($idUsuario, $estado, $modificadoPor = 'SISTEMA') {
        try {
            $con = connectionDB::getConnection();
            
            $sql = "UPDATE TBL_MS_USUARIOS 
                    SET ESTADO_USUARIO = :estado, 
                        FECHA_MODIFICACION = NOW(),
                        MODIFICADO_POR = :modificado_por
                    WHERE ID_USUARIO = :id_usuario";
            
            $query = $con->prepare($sql);
            $query->execute([
                'estado' => $estado,
                'modificado_por' => $modificadoPor,
                'id_usuario' => $idUsuario
            ]);
            
            // Registrar en bitácora
            self::registrarBitacora($idUsuario, 'CAMBIAR_ESTADO', "Estado cambiado a $estado", $modificadoPor);
            
            return ['success' => true, 'message' => 'Estado actualizado correctamente'];
            
        } catch (\PDOException $e) {
            error_log("userModel::cambiarEstadoUsuario -> " . $e->getMessage());
            return ['success' => false, 'message' => 'Error al cambiar el estado'];
        }
    }

    // Obtener usuario por ID para edición
public static function obtenerUsuarioCompleto($idUsuario) {
    try {
        $con = connectionDB::getConnection();
        
        $sql = "SELECT U.*, R.ROL, R.DESCRIPCION as DESCRIPCION_ROL
                FROM TBL_MS_USUARIOS U 
                INNER JOIN TBL_MS_ROLES R ON U.ID_ROL = R.ID_ROL 
                WHERE U.ID_USUARIO = :id_usuario";
        
        $query = $con->prepare($sql);
        $query->execute(['id_usuario' => $idUsuario]);
        
        if ($query->rowCount() > 0) {
            $usuario = $query->fetch(PDO::FETCH_ASSOC);
            // DEBUG
            error_log("DEBUG userModel - Usuario encontrado: " . print_r($usuario, true));
            return $usuario;
        }
        
        error_log("DEBUG userModel - Usuario no encontrado para ID: " . $idUsuario);
        return null;
        
    } catch (\PDOException $e) {
        error_log("userModel::obtenerUsuarioCompleto -> " . $e->getMessage());
        return null;
    }
}

    // Registrar en bitácora
    private static function registrarBitacora($idUsuario, $accion, $descripcion, $creadoPor = 'SISTEMA') {
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
            error_log("userModel::registrarBitacora -> " . $e->getMessage());
        }
    }

    /**
     * Intentar resolver un ID_OBJETO a un nombre legible consultando tablas
     * comunes donde se podrían almacenar objetos del sistema.
     * Devuelve el nombre o null si no se encuentra.
     */
    private static function obtenerNombreObjetoPorId($con, $idObjeto) {
        try {
            $posiblesTablas = [
                'TBL_MS_OBJETOS',
                'TBL_MS_OBJETO',
                'TBL_OBJETOS',
                'TBL_OBJETO'
            ];

            $posiblesColumnas = [
                'NOMBRE',
                'NOMBRE_OBJETO',
                'OBJETO',
                'DESCRIPCION'
            ];

            foreach ($posiblesTablas as $tabla) {
                foreach ($posiblesColumnas as $col) {
                    try {
                        $sql = "SELECT $col as nombre FROM $tabla WHERE ID_OBJETO = :id LIMIT 1";
                        $stmt = $con->prepare($sql);
                        $stmt->execute(['id' => $idObjeto]);
                        $row = $stmt->fetch(PDO::FETCH_ASSOC);
                        if ($row && !empty($row['nombre'])) {
                            return $row['nombre'];
                        }
                    } catch (\Exception $ex) {
                        // Tabla/columna no existe o error, continuar con la siguiente combinación
                        continue;
                    }
                }
            }

            // No encontró nada
            return null;
        } catch (\Exception $e) {
            error_log("userModel::obtenerNombreObjetoPorId -> " . $e->getMessage());
            return null;
        }
    }
}