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
    
    // Actualizar usuario usando procedimiento almacenado
    public static function actualizarUsuario($idUsuario, $datos) {
        try {
            $con = connectionDB::getConnection();
            
            // NOTA: Necesitarías crear un procedimiento SP_ACTUALIZAR_USUARIO
            // Por ahora usaremos consulta directa
            $sql = "UPDATE TBL_MS_USUARIOS 
                    SET NUMERO_IDENTIDAD = :numero_identidad,
                        NOMBRE_USUARIO = :nombre_usuario,
                        ID_ROL = :id_rol,
                        ESTADO_USUARIO = :estado_usuario,
                        CORREO_ELECTRONICO = :correo_electronico,
                        FECHA_MODIFICACION = NOW(),
                        MODIFICADO_POR = :modificado_por
                    WHERE ID_USUARIO = :id_usuario";
            
            $query = $con->prepare($sql);
            $query->execute([
                'id_usuario' => $idUsuario,
                'numero_identidad' => $datos['numero_identidad'] ?? null,
                'nombre_usuario' => $datos['nombre_usuario'] ?? null,
                'id_rol' => $datos['id_rol'] ?? null,
                'estado_usuario' => $datos['estado_usuario'] ?? null,
                'correo_electronico' => $datos['correo_electronico'] ?? null,
                'modificado_por' => $datos['modificado_por'] ?? 'SISTEMA'
            ]);
            
            // Registrar en bitácora
            self::registrarBitacora($idUsuario, 'ACTUALIZAR_USUARIO', 'Usuario actualizado', $datos['modificado_por'] ?? 'SISTEMA');
            
            return ['success' => true, 'message' => 'Usuario actualizado correctamente'];
            
        } catch (\PDOException $e) {
            error_log("userModel::actualizarUsuario -> " . $e->getMessage());
            return ['success' => false, 'message' => 'Error al actualizar el usuario'];
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
    
    // Verificar si correo existe
    public static function correoExiste($correo) {
        try {
            $con = connectionDB::getConnection();
            $sql = "SELECT COUNT(*) as EXISTE FROM TBL_MS_USUARIOS WHERE CORREO_ELECTRONICO = :correo";
            $query = $con->prepare($sql);
            $query->execute(['correo' => $correo]);
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
            
            $sql = "CALL SP_OBTENER_BITACORA()";
            $query = $con->prepare($sql);
            $query->execute();
            
            return $query->fetchAll(PDO::FETCH_ASSOC);
            
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
                return $query->fetch(PDO::FETCH_ASSOC);
            }
            
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
}