<?php require_once 'partials/header.php'; ?>
<?php require_once 'partials/sidebar.php'; ?>

 
  
    <style>
        .password-toggle { cursor: pointer; }
        .form-control:read-only { background-color: #e9ecef; }
        .auto-generated { background-color: #e8f5e8 !important; }
        
        /* Estilos para los requisitos de contraseña - IDÉNTICOS AL LOGIN */
        .password-requirements {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 5px;
            padding: 0.75rem;
            margin-top: 0.5rem;
            font-size: 0.8rem;
            color: #666;
        }
        
        .password-requirements ul {
            margin: 0;
            padding-left: 1rem;
        }
        
        .requirement-met {
            color: #28a745;
        }
        
        .requirement-not-met {
            color: #dc3545;
        }
    </style>

<main id="main" class="main">
    <div class="pagetitle">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="h2">Crear Nuevo Usuario</h1>
            <a href='/sistema/public/gestion-usuarios' class="btn btn-secondary">Volver a Gestión</a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header bg-primary text-white text-center">
                    <h5 class="card-title mb-0">Información del Usuario</h5>
                </div>
                <div class="card-body">
                    <form id="formCrearUsuario">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="numero_identidad" class="form-label">Número de Identidad</label>
                                <input type="text" class="form-control" id="numero_identidad" name="numero_identidad" 
                                       maxlength="20" oninput="this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '')"
                                       placeholder="Solo letras y números">
                                <div class="form-text">Máximo 20 caracteres. Solo letras y números.</div>
                                <div class="invalid-feedback" id="error-identidad"></div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="usuario" class="form-label">Usuario </label>
                                <input type="text" class="form-control" id="usuario" name="usuario" 
                                       maxlength="15" required 
                                       oninput="this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '')"
                                       onblur="crearUsuario.validarUsuarioUnico()"
                                       placeholder="Solo letras y números en mayúsculas">
                                <div class="form-text">Máximo 15 caracteres. Solo letras y números.</div>
                                <div class="invalid-feedback" id="error-usuario"></div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="nombre_usuario" class="form-label">Nombre Completo *</label>
                            <input type="text" class="form-control" id="nombre_usuario" name="nombre_usuario" 
                                   maxlength="100" required 
                                   oninput="crearUsuario.validarNombreUsuario(this)"
                                   placeholder="Nombre completo del usuario">
                            <div class="form-text">Máximo 100 caracteres. Solo un espacio entre palabras.</div>
                            <div class="invalid-feedback" id="error-nombre"></div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="id_rol" class="form-label">Rol de Usuario *</label>
                                <select class="form-select" id="id_rol" name="id_rol" required>
                                    <option value="">Seleccionar Rol</option>
                                </select>
                                <div class="invalid-feedback" id="error-rol"></div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="correo_electronico" class="form-label">Correo Electrónico</label>
                                <input type="email" class="form-control" id="correo_electronico" name="correo_electronico" 
                                       maxlength="50" onblur="crearUsuario.validarEmail(this)"
                                       placeholder="ejemplo@dominio.com">
                                <div class="form-text">Formato válido: usuario@dominio.com</div>
                                <div class="invalid-feedback" id="error-correo"></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="contraseña" class="form-label">Contraseña *</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="contraseña" name="contraseña" 
                                           required minlength="5" maxlength="10"
                                           oninput="crearUsuario.validarPasswordEnTiempoReal(this.value)">
                                    <span class="input-group-text password-toggle" onclick="crearUsuario.togglePassword('contraseña')">
                                        👁️
                                    </span>
                                </div>
                                <div class="form-text">Mínimo 5 caracteres, máximo 10. No se permiten espacios.</div>
                                <!-- CONTENEDOR DE REQUISITOS DE CONTRASEÑA - IGUAL AL LOGIN -->
                                <div id="passwordRequirements" class="password-requirements" style="display: none;">
                                    <strong>Requisitos de contraseña:</strong>
                                    <ul>
                                        <li id="reqLength">Mínimo 5 caracteres, máximo 10</li>
                                        <li id="reqUpper">Al menos una mayúscula</li>
                                        <li id="reqLower">Al menos una minúscula</li>
                                        <li id="reqNumber">Al menos un número</li>
                                        <li id="reqSpecial">Al menos un carácter especial</li>
                                        <li id="reqNoSpaces">Sin espacios</li>
                                    </ul>
                                </div>
                                <div class="invalid-feedback" id="error-password"></div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="confirmar_contraseña" class="form-label">Confirmar Contraseña *</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="confirmar_contraseña" 
                                           required oninput="crearUsuario.validarConfirmacionPassword(this)">
                                    <span class="input-group-text password-toggle" onclick="crearUsuario.togglePassword('confirmar_contraseña')">
                                        👁️
                                    </span>
                                </div>
                                <div class="invalid-feedback" id="error-confirmar"></div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="autogenerar_password">
                                    <label class="form-check-label" for="autogenerar_password">
                                        Autogenerar contraseña robusta
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="mostrar_password">
                                    <label class="form-check-label" for="mostrar_password">
                                        Mostrar contraseñas
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body py-2">
                                        <small class="text-muted">
                                            <strong>Fecha Creación:</strong><br>
                                            <span id="fecha_creacion"><?php echo date('d/m/Y H:i:s'); ?></span>
                                        </small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body py-2">
                                        <small class="text-muted">
                                            <strong>Fecha Vencimiento:</strong><br>
                                            <span id="fecha_vencimiento">Calculando...</span>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card bg-light mb-3">
                            <div class="card-body">
                                <h6 class="card-title">Estado del Usuario: <span class="badge bg-warning">NUEVO</span></h6>
                                <small class="text-muted">
                                    • El usuario deberá configurar preguntas de seguridad en su primer ingreso<br>
                                    • El estado cambiará a ACTIVO después del primer ingreso exitoso
                                </small>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="button" class="btn btn-secondary me-md-2" onclick="crearUsuario.limpiarFormulario()">
                                Limpiar Formulario
                            </button>
                            <button type="submit" class="btn btn-primary" id="btnCrearUsuario">
                                <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                                Crear Usuario
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>
 <!-- Vendor JS Files -->
  <script src="/sistema/src/Views/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="/sistema/src/Views/assets/vendor/php-email-form/validate.js"></script>
  <script src="/sistema/src/Views/assets/vendor/aos/aos.js"></script>
  <script src="/sistema/src/Views/assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="/sistema/src/Views/assets/vendor/purecounter/purecounter_vanilla.js"></script>
  <script src="/sistema/src/Views/assets/vendor/swiper/swiper-bundle.min.js"></script>
  


  <!-- Main JS File -->
  <script src="/sistema/src/Views/assets/js/main.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    class CrearUsuario {
        constructor() {
            this.init();
        }

        async init() {
            await this.cargarRoles();
            this.configurarEventos();
            this.calcularFechaVencimiento();
        }

        async cargarRoles() {
            try {
                
                const response = await fetch('index.php?route=user&caso=obtener-roles');
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                
                const selectRol = document.getElementById('id_rol');
                selectRol.innerHTML = '<option value="">Seleccionar Rol</option>';
                
                if (data.status === 200 && data.data && data.data.roles) {
                    data.data.roles.forEach(rol => {
                        const option = document.createElement('option');
                        //Campos en mayúsculas
                        option.value = rol.ID_ROL || rol.Id_Rol;
                        option.textContent = rol.ROL || rol.Rol;
                        selectRol.appendChild(option);
                    });
                } else {
                    // Roles por defecto
                    const rolesPorDefecto = [
                        { ID_ROL: 1, ROL: 'ADMINISTRADOR' },
                        { ID_ROL: 2, ROL: 'USUARIO' }
                    ];
                    
                    rolesPorDefecto.forEach(rol => {
                        const option = document.createElement('option');
                        option.value = rol.ID_ROL;
                        option.textContent = rol.ROL;
                        selectRol.appendChild(option);
                    });
                }
                
            } catch (error) {
                console.error('Error cargando roles:', error);
            }
        }

        configurarEventos() {
            const form = document.getElementById('formCrearUsuario');
            const autogenerarCheckbox = document.getElementById('autogenerar_password');
            const mostrarPasswordCheckbox = document.getElementById('mostrar_password');
            
            form.addEventListener('submit', (e) => this.crearUsuario(e));
            autogenerarCheckbox.addEventListener('change', (e) => this.toggleAutogenerarPassword(e));
            mostrarPasswordCheckbox.addEventListener('change', (e) => this.toggleMostrarPassword(e));
        }

        async calcularFechaVencimiento() {
            try {
                
                const response = await fetch('index.php?route=user&caso=obtener-parametros');
                const data = await response.json();
                
                let diasVigencia = 360; // Valor por defecto
                
                if (data.status === 200 && data.data && data.data.parametros) {
                    // Campo en mayúsculas
                    const parametro = data.data.parametros.find(p => p.PARAMETRO === 'ADMIN_DIAS_VIGENCIA' || p.Parametro === 'ADMIN_DIAS_VIGENCIA');
                    if (parametro) {
                        diasVigencia = parseInt(parametro.VALOR || parametro.Valor) || 360;
                    }
                }
                
                const fechaActual = new Date();
                const fechaVencimiento = new Date(fechaActual);
                fechaVencimiento.setDate(fechaActual.getDate() + diasVigencia);
                
                document.getElementById('fecha_vencimiento').textContent = 
                    fechaVencimiento.toLocaleDateString('es-ES') + ' (' + diasVigencia + ' días)';
                    
            } catch (error) {
                console.error('Error calculando fecha vencimiento:', error);
                document.getElementById('fecha_vencimiento').textContent = 'Error al calcular';
            }
        }

        // FUNCIÓN IDÉNTICA A LA DEL LOGIN - VALIDACIÓN EN TIEMPO REAL
        validarPasswordEnTiempoReal(password) {
            const requirements = document.getElementById('passwordRequirements');
            
            if (password.length > 0) {
                requirements.style.display = 'block';
                
                // Validar cada requisito - EXACTAMENTE IGUAL AL LOGIN
                document.getElementById('reqLength').className = 
                    (password.length >= 5 && password.length <= 10) ? 'requirement-met' : 'requirement-not-met';
                
                document.getElementById('reqUpper').className = 
                    /[A-Z]/.test(password) ? 'requirement-met' : 'requirement-not-met';
                
                document.getElementById('reqLower').className = 
                    /[a-z]/.test(password) ? 'requirement-met' : 'requirement-not-met';
                
                document.getElementById('reqNumber').className = 
                    /[0-9]/.test(password) ? 'requirement-met' : 'requirement-not-met';
                
                document.getElementById('reqSpecial').className = 
                    /[!@#$%^&*()\-_=+{};:,<.>]/.test(password) ? 'requirement-met' : 'requirement-not-met';
                
                document.getElementById('reqNoSpaces').className = 
                    !/\s/.test(password) ? 'requirement-met' : 'requirement-not-met';
            } else {
                requirements.style.display = 'none';
            }
        }

        async toggleAutogenerarPassword(e) {
            const passwordInput = document.getElementById('contraseña');
            const confirmInput = document.getElementById('confirmar_contraseña');
            
            if (e.target.checked) {
                try {
                    
                    const response = await fetch('index.php?route=user&caso=generar-password');
                    const data = await response.json();
                    
                    if (data.status === 200) {
                        passwordInput.value = data.data.password;
                        confirmInput.value = data.data.password;
                        passwordInput.classList.add('auto-generated');
                        confirmInput.classList.add('auto-generated');
                        
                        // Validar la contraseña autogenerada
                        this.validarPasswordEnTiempoReal(data.data.password);
                    }
                } catch (error) {
                    console.error('Error generando password:', error);
                }
            } else {
                passwordInput.value = '';
                confirmInput.value = '';
                passwordInput.classList.remove('auto-generated');
                confirmInput.classList.remove('auto-generated');
                
                // Ocultar requisitos
                document.getElementById('passwordRequirements').style.display = 'none';
            }
        }

        toggleMostrarPassword(e) {
            const passwordInput = document.getElementById('contraseña');
            const confirmInput = document.getElementById('confirmar_contraseña');
            const type = e.target.checked ? 'text' : 'password';
            
            passwordInput.setAttribute('type', type);
            confirmInput.setAttribute('type', type);
        }

        validarConfirmacionPassword(input) {
            const password = document.getElementById('contraseña').value;
            const confirmPassword = input.value;
            
            if (confirmPassword !== password) {
                this.mostrarError('confirmar', 'Las contraseñas no coinciden');
            } else {
                this.limpiarError('confirmar');
            }
        }

        async validarUsuarioUnico() {
            const usuario = document.getElementById('usuario').value.trim();
            
            if (usuario.length === 0) return;
            
            try {
                
                const response = await fetch('index.php?route=user&caso=verificar-usuario', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ usuario: usuario })
                });
                
                const data = await response.json();
                
                if (data.status === 400) {
                    this.mostrarError('usuario', 'Este usuario ya existe en el sistema');
                } else {
                    this.limpiarError('usuario');
                }
            } catch (error) {
                console.error('Error validando usuario:', error);
            }
        }

        validarNombreUsuario(input) {
            let valor = input.value;
            // Permitir solo letras, números y un solo espacio entre palabras
            valor = valor.replace(/[^A-ZÁÉÍÓÚÑÜ\s]/gi, '');
            valor = valor.replace(/\s+/g, ' '); // Reemplazar múltiples espacios por uno solo
            input.value = valor.toUpperCase();
            
            if (valor.trim().length === 0) {
                this.mostrarError('nombre', 'El nombre es requerido');
            } else {
                this.limpiarError('nombre');
            }
        }

        validarEmail(input) {
            const email = input.value.trim();
            if (email === '') {
                this.limpiarError('correo');
                return;
            }
            
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                this.mostrarError('correo', 'Formato de correo electrónico inválido');
            } else {
                this.limpiarError('correo');
            }
        }

        async crearUsuario(e) {
            e.preventDefault();
            
            if (!this.validarFormulario()) {
                return;
            }
            
            const btnSubmit = document.getElementById('btnCrearUsuario');
            const spinner = btnSubmit.querySelector('.spinner-border');
            
            btnSubmit.disabled = true;
            spinner.classList.remove('d-none');
            
            try {
                const formData = new FormData(document.getElementById('formCrearUsuario'));
                const data = Object.fromEntries(formData);
                
                // Agregar campos adicionales
                data.creado_por = 'ADMIN';
                
                //ACTUALIZADO: Ruta del sistema
                const response = await fetch('index.php?route=user&caso=crear', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                
                if (result.status === 201) {
    alert('Usuario creado exitosamente');
    this.limpiarFormulario();
    // Opcional: redirigir después de 2 segundos
    setTimeout(() => {
        // ACTUALIZADO: Ruta del sistema
        window.location.href = 'index.php?route=gestion-usuarios';
    }, 2000);
} else {
    alert('Error: ' + (result.message || 'No se pudo crear el usuario')); // ← CORREGIDO
}
            } catch (error) {
                console.error('Error creando usuario:', error);
                alert('Error de conexión con el servidor');
            } finally {
                btnSubmit.disabled = false;
                spinner.classList.add('d-none');
            }
        }

        validarFormulario() {
            let isValid = true;
            
            // Validar usuario
            const usuario = document.getElementById('usuario').value.trim();
            if (usuario.length === 0) {
                this.mostrarError('usuario', 'El usuario es requerido');
                isValid = false;
            } else if (usuario.length < 3) {
                this.mostrarError('usuario', 'El usuario debe tener al menos 3 caracteres');
                isValid = false;
            }
            
            // Validar nombre
            const nombre = document.getElementById('nombre_usuario').value.trim();
            if (nombre.length === 0) {
                this.mostrarError('nombre', 'El nombre completo es requerido');
                isValid = false;
            }
            
            // Validar rol
            const rol = document.getElementById('id_rol').value;
            if (rol === '') {
                this.mostrarError('rol', 'El rol es requerido');
                isValid = false;
            }
            
            // Validar contraseñas
            const password = document.getElementById('contraseña').value;
            const confirmPassword = document.getElementById('confirmar_contraseña').value;
            
            if (password !== confirmPassword) {
                this.mostrarError('confirmar', 'Las contraseñas no coinciden');
                isValid = false;
            } else {
                this.limpiarError('confirmar');
            }
            
            if (password.length < 5 || password.length > 10) {
                this.mostrarError('password', 'La contraseña debe tener entre 5 y 10 caracteres');
                isValid = false;
            } else {
                this.limpiarError('password');
            }
            
            // Validar email si se proporciona
            const email = document.getElementById('correo_electronico').value;
            if (email && !this.validarEmailFormat(email)) {
                this.mostrarError('correo', 'Formato de correo electrónico inválido');
                isValid = false;
            }
            
            return isValid;
        }

        validarEmailFormat(email) {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(email);
        }

        limpiarFormulario() {
            document.getElementById('formCrearUsuario').reset();
            document.getElementById('fecha_creacion').textContent = new Date().toLocaleString('es-ES');
            this.calcularFechaVencimiento();
            
            // Limpiar todas las clases de error
            const inputs = document.querySelectorAll('.is-invalid');
            inputs.forEach(input => input.classList.remove('is-invalid'));
            
            const feedbacks = document.querySelectorAll('.invalid-feedback');
            feedbacks.forEach(feedback => feedback.textContent = '');
            
            // Remover clases de autogenerado
            document.getElementById('contraseña').classList.remove('auto-generated');
            document.getElementById('confirmar_contraseña').classList.remove('auto-generated');
            
            // Ocultar requisitos de contraseña
            document.getElementById('passwordRequirements').style.display = 'none';
            
            console.log('Formulario limpiado exitosamente');
        }

        mostrarError(campo, mensaje) {
            const errorElement = document.getElementById(`error-${campo}`);
            const inputElement = document.getElementById(campo) || document.getElementById(`confirmar_${campo}`);
            
            if (errorElement && inputElement) {
                errorElement.textContent = mensaje;
                inputElement.classList.add('is-invalid');
            }
        }

        limpiarError(campo) {
            const errorElement = document.getElementById(`error-${campo}`);
            const inputElement = document.getElementById(campo) || document.getElementById(`confirmar_${campo}`);
            
            if (errorElement && inputElement) {
                errorElement.textContent = '';
                inputElement.classList.remove('is-invalid');
            }
        }

        togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
            input.setAttribute('type', type);
        }
    }

    // Instancia global
    const crearUsuario = new CrearUsuario();

    // Inicializar cuando el DOM esté listo
    document.addEventListener('DOMContentLoaded', () => {
        window.crearUsuario = crearUsuario;
    });
</script>

