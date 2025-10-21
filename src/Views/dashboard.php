<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistema Rosquilla</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
        }
        
        .header {
            background: #667eea;
            color: white;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .logout-btn {
            background: #fff;
            color: #667eea;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
        }
        
        .main-content {
            padding: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .welcome-card {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin-top: 2rem;
        }
        
        .menu-card {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
            cursor: pointer;
            transition: transform 0.3s;
        }
        
        .menu-card:hover {
            transform: translateY(-5px);
        }
        
        .menu-card h3 {
            color: #667eea;
            margin-bottom: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Sistema Rosquilla</h1>
        <div class="user-info">
            <span id="userName">Usuario</span>
            <a href="/rosquilla/public/index.php?route=login" class="logout-btn">Cerrar Sesión</a>
        </div>
    </div>
    
    <div class="main-content">
        <div class="welcome-card">
            <h2>¡Bienvenido al Sistema!</h2>
            <p>Has iniciado sesión correctamente en el Sistema Rosquilla.</p>
        </div>
        
        <div class="menu-grid">
            <div class="menu-card" onclick="cambiarContraseña()">
                <h3>🔐 Cambiar Contraseña</h3>
                <p>Actualiza tu contraseña de acceso</p>
            </div>
            
            <div class="menu-card" onclick="gestionarUsuarios()">
                <h3>👥 Gestión de Usuarios</h3>
                <p>Administrar usuarios del sistema</p>
            </div>
            
            <div class="menu-card" onclick="verBitacora()">
                <h3>📊 Bitácora</h3>
                <p>Ver registro de actividades</p>
            </div>
        </div>
    </div>

   <script>
        // Cargar nombre de usuario desde sessionStorage
        document.addEventListener('DOMContentLoaded', function() {
            const userName = sessionStorage.getItem('user_name') || 'Usuario';
            document.getElementById('userName').textContent = userName;
        });
        
        function cambiarContraseña() {
            window.location.href = '/rosquilla/public/index.php?route=cambiar-password';
        }
        
        function gestionarUsuarios() {
            window.location.href = '/rosquilla/public/index.php?route=gestion-usuarios';
        }
        
        function verBitacora() {
            window.location.href = '/rosquilla/public/index.php?route=bitacora';
        }
    </script>
</body>
</html>