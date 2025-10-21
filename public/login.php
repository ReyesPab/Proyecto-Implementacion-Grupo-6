[file name]: login.php
[file content begin]
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema Rosquilla</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .login-container {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 400px;
        }
        
        h2 {
            text-align: center;
            margin-bottom: 1.5rem;
            color: #333;
        }
        
        .form-group {
            margin-bottom: 1rem;
            position: relative;
        }
        
        label {
            display: block;
            margin-bottom: 0.5rem;
            color: #555;
            font-weight: bold;
        }
        
        input {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        
        input:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .password-container {
            position: relative;
        }
        
        .toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #666;
        }
        
        .btn {
            width: 100%;
            padding: 0.75rem;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.3s;
        }
        
        .btn:hover {
            background: #5a6fd8;
        }
        
        .links {
            margin-top: 1rem;
            text-align: center;
        }
        
        .links a {
            color: #667eea;
            text-decoration: none;
            margin: 0 0.5rem;
        }
        
        .links a:hover {
            text-decoration: underline;
        }

        /* NUEVOS ESTILOS PARA LOS ENLACES DE CUENTA */
        .account-links {
            margin-top: 1.5rem;
            padding-top: 1rem;
            border-top: 1px solid #eee;
            text-align: center;
        }
        
        .account-links p {
            margin-bottom: 0.5rem;
            color: #555;
            font-size: 0.9rem;
        }
        
        .account-links a {
            color: #667eea;
            text-decoration: none;
            font-weight: bold;
            margin: 0 0.3rem;
        }
        
        .account-links a:hover {
            text-decoration: underline;
        }
        
        .alert {
            padding: 0.75rem;
            border-radius: 5px;
            margin-bottom: 1rem;
            display: none;
        }
        
        .alert-error {
            background: #fee;
            border: 1px solid #fcc;
            color: #c33;
        }
        
        .alert-success {
            background: #efe;
            border: 1px solid #cfc;
            color: #363;
        }
                /* NUEVOS ESTILOS PARA LOS ENLACES DE CUENTA */
        .account-links {
            margin-top: 1.5rem;
            padding-top: 1rem;
            border-top: 1px solid #eee;
            text-align: center;
        }
        
        .account-links p {
            margin-bottom: 0.5rem;
            color: #555;
            font-size: 0.9rem;
        }
        
        /* ESTILOS PARA EL BOTÓN DE CREAR CUENTA */
        .btn-outline-primary {
            background: white;
            color: #667eea;
            border: 2px solid #667eea;
            padding: 0.75rem;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            transition: all 0.3s;
            width: 100%;
            margin-top: 0.5rem;
        }
        
        .btn-outline-primary:hover {
            background: #667eea;
            color: white;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Iniciar Sesión</h2>
        
        <div id="alert" class="alert"></div>
        
        <form id="loginForm">
            <div class="form-group">
                <label for="usuario">Usuario:</label>
                <input type="text" id="usuario" name="usuario" required 
                       maxlength="15" placeholder="INGRESE SU USUARIO EN MAYÚSCULAS"
                       oninput="this.value = this.value.toUpperCase()">
            </div>
            
            <div class="form-group">
                <label for="password">Contraseña:</label>
                <div class="password-container">
                    <input type="password" id="password" name="password" required 
                           maxlength="10" placeholder="Ingrese su contraseña">
                    <button type="button" class="toggle-password" onclick="togglePassword()">
                        👁️
                    </button>
                </div>
            </div>
            
            <button type="submit" class="btn">Ingresar</button>
        </form>
        
        <div class="links">
            <a href="#" onclick="mostrarRecuperacion()">¿Olvidó su contraseña?</a>
        </div>

        <!-- NUEVA SECCIÓN AGREGADA -->
        <div class="account-links">
            <p>¿No tienes una cuenta?</p>
            <button type="button" class="btn btn-outline-primary" onclick="irARegistro()" style="width: 100%; margin-top: 0.5rem;">
                Crear Cuenta
            </button>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleButton = document.querySelector('.toggle-password');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleButton.textContent = '🔒';
            } else {
                passwordInput.type = 'password';
                toggleButton.textContent = '👁️';
            }
        }
        
        function showAlert(message, type) {
            const alert = document.getElementById('alert');
            alert.textContent = message;
            alert.className = `alert alert-${type}`;
            alert.style.display = 'block';
            
            setTimeout(() => {
                alert.style.display = 'none';
            }, 5000);
        }

        // NUEVAS FUNCIONES AGREGADAS
        function irALogin() {
            // Ya estamos en login, mostrar mensaje informativo
            showAlert('Ya se encuentra en la página de inicio de sesión', 'success');
        }

        function irARegistro() {
            // Redirigir a la página de crear usuario usando la ruta del sistema
            window.location.href = 'index.php?route=crear-usuario';
        }
        
        document.getElementById('loginForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const formData = new FormData(e.target);
            const data = {
                usuario: formData.get('usuario'),
                password: formData.get('password')
            };
            
            try {
                const response = await fetch('http://localhost/rosquilla/public/index.php/auth/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                
                if (response.ok) {
                    showAlert(result.message, 'success');
                    
                    // Redirigir según el tipo de usuario
                    if (result.primer_ingreso) {
                        // Redirigir a configuración de primer ingreso
                        setTimeout(() => {
                            window.location.href = 'primer-ingreso.html?usuario=' + result.user.Id_Usuario;
                        }, 2000);
                    } else {
                        // Redirigir al dashboard
                        setTimeout(() => {
                            window.location.href = 'dashboard.html';
                        }, 2000);
                    }
                } else {
                    showAlert(result.message, 'error');
                }
            } catch (error) {
                showAlert('Error de conexión: ' + error.message, 'error');
            }
        });
        
        function mostrarRecuperacion() {
            const usuario = document.getElementById('usuario').value;
            if (!usuario) {
                showAlert('Ingrese su usuario para recuperar la contraseña', 'error');
                return;
            }
            
            // Aquí implementarías la lógica de recuperación
            alert('Funcionalidad de recuperación para: ' + usuario);
        }
    </script>
</body>
</html>
[file content end]