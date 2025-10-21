<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema</title>
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

        .input-icon {
            position: relative;
        }

        .input-icon input {
            width: 100%;
            padding: 0.75rem 2.5rem 0.75rem 2.5rem;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        .input-icon input:focus {
            outline: none;
            border-color: #667eea;
        }

        .input-icon .icon {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
            font-size: 1.2rem;
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
            font-size: 1.2rem;
        }

        .btn {
            width: 100%;
            padding: 0.75rem;
            background: #667eea;
            color: white;
            border: noe;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.3s;
        }

        .btn:hover {
            background: #5a6fd8;
        }

        .btn:disabled {
            background: #ccc;
            cursor: not-allowed;
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

        .loading {
            display: none;
            text-align: center;
            margin: 1rem 0;
            color: #667eea;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Iniciar Sesión</h2>
        
        <div id="alert" class="alert"></div>
        <div id="loading" class="loading">Cargando...</div>
        
        <form id="loginForm">
            <div class="form-group">
                <label for="usuario">Usuario:</label>
                <div class="input-icon">
                    <span class="icon">👤</span>
                    <input type="text" id="usuario" name="usuario" required 
                           maxlength="15" placeholder="INGRESE SU USUARIO EN MAYÚSCULAS"
                           oninput="this.value = this.value.toUpperCase().replace(/\s/g, '')">
                </div>
            </div>
            
            <div class="form-group">
                <label for="password">Contraseña:</label>
                <div class="input-icon password-container">
                    <span class="icon">🔒</span>
                    <input type="password" id="password" name="password" required 
                           maxlength="10" placeholder="Ingrese su contraseña">
                    <button type="button" class="toggle-password" onclick="togglePassword()">
                        👁️
                    </button>
                </div>
            </div>
            
            <button type="submit" class="btn" id="submitBtn">Ingresar</button>
        </form>
        
        <div class="links">
            <a href="/sistema/public/recuperar-password">¿Olvidó su contraseña?</a>
        </div>
    </div>

    <script>
    // Variables globales para el estado 2FA
    let usuario2FA = '';
    let password2FA = '';

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

    function showLoading(show) {
        const loading = document.getElementById('loading');
        const submitBtn = document.getElementById('submitBtn');
        
        if (show) {
            loading.style.display = 'block';
            submitBtn.disabled = true;
            submitBtn.textContent = 'Procesando...';
        } else {
            loading.style.display = 'none';
            submitBtn.disabled = false;
            submitBtn.textContent = 'Ingresar';
        }
    }

    function validarPasswordFrontend(password, usuario) {
        const errores = [];
        
        if (password.length < 5) {
            errores.push('La contraseña debe tener al menos 5 caracteres');
        }
        
        if (password.length > 10) {
            errores.push('La contraseña no puede tener más de 10 caracteres');
        }
        
        if (!/[A-Z]/.test(password)) {
            errores.push('Debe contener al menos una mayúscula');
        }
        
        if (!/[a-z]/.test(password)) {
            errores.push('Debe contener al menos una minúscula');
        }
        
        if (!/[0-9]/.test(password)) {
            errores.push('Debe contener al menos un número');
        }
        
        if (!/[!@#$%^&*()\-_=+{};:,<.>]/.test(password)) {
            errores.push('Debe contener al menos un carácter especial');
        }
        
        if (/\s/.test(password)) {
            errores.push('No puede contener espacios');
        }
        
        if (password.toUpperCase() === usuario.toUpperCase()) {
            errores.push('La contraseña no puede ser igual al usuario');
        }
        
        return errores;
    }

    // Funciones para autenticación en dos pasos
    function mostrarModal2FA(usuario, correoOculto) {
        // Crear modal para 2FA
        const modalHTML = `
            <div id="modal2FA" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: flex; justify-content: center; align-items: center; z-index: 1000;">
                <div style="background: white; padding: 2rem; border-radius: 10px; width: 90%; max-width: 400px; text-align: center; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
                    <h3 style="margin-bottom: 1rem; color: #333;">🔐 Verificación en Dos Pasos</h3>
                    <p style="margin-bottom: 1rem;">Se ha enviado un código de 6 dígitos a:</p>
                    <p style="font-weight: bold; margin-bottom: 1.5rem; color: #007bff;">${correoOculto}</p>
                    
                    <form id="form2FA">
                        <div style="margin-bottom: 1rem;">
                            <label for="codigo2FA" style="display: block; margin-bottom: 0.5rem; font-weight: bold; color: #555;">Código de Verificación:</label>
                            <input type="text" id="codigo2FA" name="codigo" 
                                   maxlength="6" pattern="[0-9]{6}" 
                                   style="width: 100%; padding: 0.75rem; border: 2px solid #ddd; border-radius: 5px; font-size: 1.5rem; text-align: center; letter-spacing: 0.5rem; font-weight: bold;"
                                   placeholder="000000" required
                                   oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0,6)">
                            <small style="color: #666; display: block; margin-top: 0.5rem;">Ingresa el código de 6 dígitos que recibiste por correo</small>
                        </div>
                        
                        <div style="margin-bottom: 1rem; display: flex; flex-direction: column; gap: 0.5rem;">
                            <button type="submit" class="btn" style="background: #28a745; padding: 0.75rem;">Verificar Código</button>
                            <button type="button" id="btnReenviar" class="btn" style="background: #6c757d; padding: 0.75rem;">Reenviar Código</button>
                            <button type="button" id="btnCancelar" class="btn" style="background: #dc3545; padding: 0.75rem;">Cancelar</button>
                        </div>
                    </form>
                    
                    <div id="alert2FA" class="alert" style="display: none; margin-top: 1rem;"></div>
                    <div id="loading2FA" class="loading" style="display: none;">Verificando...</div>
                </div>
            </div>
        `;
        
        document.body.insertAdjacentHTML('beforeend', modalHTML);
        
        // Configurar eventos del modal
        document.getElementById('form2FA').addEventListener('submit', verificarCodigo2FA);
        document.getElementById('btnReenviar').addEventListener('click', reenviarCodigo2FA);
        document.getElementById('btnCancelar').addEventListener('click', cancelar2FA);
        
        // Auto-focus en el input del código
        document.getElementById('codigo2FA').focus();
        
        // Permitir cerrar modal haciendo click fuera
        document.getElementById('modal2FA').addEventListener('click', function(e) {
            if (e.target.id === 'modal2FA') {
                cancelar2FA();
            }
        });
    }

    function ocultarModal2FA() {
        const modal = document.getElementById('modal2FA');
        if (modal) {
            modal.remove();
        }
    }

    function mostrarAlerta2FA(mensaje, tipo) {
        const alert = document.getElementById('alert2FA');
        alert.textContent = mensaje;
        alert.className = `alert alert-${tipo}`;
        alert.style.display = 'block';
        
        setTimeout(() => {
            alert.style.display = 'none';
        }, 5000);
    }

    function mostrarLoading2FA(mostrar) {
        const loading = document.getElementById('loading2FA');
        const btnVerificar = document.querySelector('#form2FA button[type="submit"]');
        const btnReenviar = document.getElementById('btnReenviar');
        const btnCancelar = document.getElementById('btnCancelar');
        
        if (mostrar) {
            loading.style.display = 'block';
            btnVerificar.disabled = true;
            btnReenviar.disabled = true;
            btnCancelar.disabled = true;
        } else {
            loading.style.display = 'none';
            btnVerificar.disabled = false;
            btnReenviar.disabled = false;
            btnCancelar.disabled = false;
        }
    }

    async function verificarCodigo2FA(e) {
        e.preventDefault();
        
        const codigo = document.getElementById('codigo2FA').value.trim();
        
        if (codigo.length !== 6 || !/^\d+$/.test(codigo)) {
            mostrarAlerta2FA('El código debe tener exactamente 6 dígitos numéricos', 'error');
            return;
        }
        
        mostrarLoading2FA(true);
        
        try {
            const response = await fetch('/sistema/public/index.php?route=auth&caso=verificar-2fa', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    codigo: codigo
                })
            });
            
            const result = await response.json();
            console.log("Respuesta verificación 2FA:", result);
            
            if (result.status === '200') {
                mostrarAlerta2FA('¡Verificación exitosa!', 'success');
                
                // Guardar datos del usuario
                if (result.data && result.data.data) {
                    const userData = result.data.data;
                    sessionStorage.setItem('user_id', userData.id_usuario);
                    sessionStorage.setItem('primer_ingreso', userData.primer_ingreso);
                    sessionStorage.setItem('user_name', userData.user?.NOMBRE_USUARIO || 'Usuario');
                }
                
                setTimeout(() => {
                    ocultarModal2FA();
                    
                    if (result.data && result.data.data) {
                        const userData = result.data.data;
                        
                        console.log("===  DEBUG 2FA COMPLETO ===");
                        console.log(" userData COMPLETO:", userData);
                        
                        // Verificar PRIMER_INGRESO en lugar de ESTADO
                        const primerIngreso = userData.primer_ingreso;
                        console.log(" PRIMER_INGRESO encontrado:", primerIngreso);
                        
                        if (primerIngreso === 0 || primerIngreso === false) {
                            console.log(" USUARIO CON PRIMER INGRESO - Redirigiendo a cambiar contraseña");
                            showAlert('Primer ingreso detectado. Debe cambiar su contraseña.', 'success');
                            window.location.href = '/sistema/public/cambiar-password';
                        } else {
                            console.log(" USUARIO ACTIVO - Redirigiendo a inicio");
                            window.location.href = '/sistema/public/inicio';
                        }
                        
                    } else {
                        console.log(" No hay datos de usuario - Redirigiendo a inicio");
                        window.location.href = '/sistema/public/inicio';
                    }
                }, 1500);
                
            } else {
                mostrarAlerta2FA(result.message || 'Código incorrecto', 'error');
                // Limpiar el campo de código
                document.getElementById('codigo2FA').value = '';
                document.getElementById('codigo2FA').focus();
            }
            
        } catch (error) {
            console.error('Error verificando código 2FA:', error);
            mostrarAlerta2FA('Error de conexión. Intente nuevamente.', 'error');
        } finally {
            mostrarLoading2FA(false);
        }
    }

    async function reenviarCodigo2FA() {
        mostrarLoading2FA(true);
        
        try {
            const response = await fetch('/sistema/public/index.php?route=auth&caso=reenviar-codigo-2fa', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({})
            });
            
            const result = await response.json();
            
            if (result.status === '200') {
                mostrarAlerta2FA('Se ha enviado un nuevo código a tu correo', 'success');
                document.getElementById('codigo2FA').value = '';
                document.getElementById('codigo2FA').focus();
            } else {
                mostrarAlerta2FA(result.message || 'Error al reenviar el código', 'error');
            }
            
        } catch (error) {
            console.error('Error reenviando código:', error);
            mostrarAlerta2FA('Error de conexión', 'error');
        } finally {
            mostrarLoading2FA(false);
        }
    }

    function cancelar2FA() {
        ocultarModal2FA();
        // Limpiar el formulario de login
        document.getElementById('loginForm').reset();
        showAlert('Verificación cancelada', 'info');
        // Limpiar variables globales
        usuario2FA = '';
        password2FA = '';
    }

    // Función principal de login
    document.getElementById('loginForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const usuario = document.getElementById('usuario').value.toUpperCase().trim();
        const password = document.getElementById('password').value;
        
        console.log("Enviando login:", { usuario, password });
        
        // Validaciones básicas
        if (!usuario || !password) {
            showAlert('Usuario y contraseña son obligatorios', 'error');
            return;
        }
        
        if (usuario.length > 15) {
            showAlert('El usuario no puede tener más de 15 caracteres', 'error');
            return;
        }
        
        if (/\s/.test(usuario)) {
            showAlert('El usuario no puede contener espacios', 'error');
            return;
        }
        
        // Validar contraseña en frontend
        const erroresPassword = validarPasswordFrontend(password, usuario);
        if (erroresPassword.length > 0) {
            showAlert('Error en contraseña: ' + erroresPassword[0], 'error');
            return;
        }
        
        showLoading(true);
        
        try {
            // Usar el nuevo endpoint de 2FA
            const response = await fetch('/sistema/public/index.php?route=auth&caso=iniciar-2fa', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    usuario: usuario,
                    password: password
                })
            });
            
            const result = await response.json();
            console.log("Respuesta del servidor:", result);
            
            // Procesar resultado
            if (result.status === '200' && result.data && result.data.status === '2fa_required') {
                // Guardar credenciales para usar después
                usuario2FA = usuario;
                password2FA = password;
                
                // Mostrar modal de 2FA
                mostrarModal2FA(usuario, result.data.data.correo);
                showAlert('Se ha enviado un código de verificación a tu correo electrónico', 'success');
                
            } else if (result.status === '200') {
                // Caso donde no se requiere 2FA (para compatibilidad hacia atrás)
                showAlert('Login exitoso', 'success');
                
                // Guardar datos del usuario
                if (result.data && result.data.data) {
                    const userData = result.data.data;
                    sessionStorage.setItem('user_id', userData.id_usuario);
                    sessionStorage.setItem('primer_ingreso', userData.primer_ingreso);
                    sessionStorage.setItem('user_name', userData.user?.NOMBRE_USUARIO || 'Usuario');
                }
                
                setTimeout(() => {
                    if (result.data && result.data.data) {
                        const userData = result.data.data;
                        
                        console.log("===  DEBUG COMPLETO - USUARIO NUEVO ===");
                        console.log(" userData COMPLETO:", userData);
                        
                        // Verificar PRIMER_INGRESO en lugar de ESTADO
                        const primerIngreso = userData.primer_ingreso;
                        console.log(" PRIMER_INGRESO encontrado:", primerIngreso);
                        
                        // DECISIÓN BASADA EN PRIMER_INGRESO
                        if (primerIngreso === 0 || primerIngreso === false) {
                            console.log(" USUARIO CON PRIMER INGRESO - Redirigiendo a cambiar contraseña");
                            showAlert('Primer ingreso detectado. Debe cambiar su contraseña.', 'success');
                            window.location.href = '/sistema/public/cambiar-password';
                        } else {
                            console.log(" USUARIO ACTIVO - Redirigiendo a inicio");
                            window.location.href = '/sistema/public/inicio';
                        }
                    } else {
                        console.log("No hay datos de usuario - Redirigiendo a inicio");
                        window.location.href = '/sistema/public/inicio';
                    }
                }, 1500);
                
            } else if (result.status === '401' || result.status === '400') {
                const errorMessage = result.message || 'Credenciales incorrectas. Verifique usuario y contraseña.';
                showAlert(errorMessage, 'error');
            } else {
                const errorMessage = result.message || 'Error en el servidor. Intente nuevamente.';
                showAlert(errorMessage, 'error');
            }
            
        } catch (error) {
            console.error('Error de conexión:', error);
            
            if (error.name === 'TypeError' && error.message.includes('Failed to fetch')) {
                showAlert('Error de conexión. Verifique su internet e intente nuevamente.', 'error');
            } else if (error.name === 'SyntaxError') {
                showAlert('Error en la respuesta del servidor. Intente nuevamente.', 'error');
            } else {
                showAlert('Error en el servidor. Intente nuevamente.', 'error');
            }
        } finally {
            showLoading(false);
        }
    });

    // Enter key support
    document.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            const modal2FA = document.getElementById('modal2FA');
            if (modal2FA && modal2FA.style.display !== 'none') {
                // Si el modal de 2FA está visible, enviar el formulario de 2FA
                document.getElementById('form2FA').dispatchEvent(new Event('submit'));
            } else {
                // Si no, enviar el formulario de login normal
                document.getElementById('loginForm').dispatchEvent(new Event('submit'));
            }
        }
    });

    // Auto-focus en usuario al cargar
    document.getElementById('usuario').focus();
</script>
</body>
</html>