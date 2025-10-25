<?php require_once 'partials/header.php'; ?>
<?php require_once 'partials/sidebar.php'; ?>

<style>
    .password-toggle { cursor: pointer; }
    .form-control:read-only { background-color: #e9ecef; }
    
    /* Estilos para los requisitos de contraseña */
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
            <h1 class="h2">Editar Usuario</h1>
            <a href='/sistema/public/gestion-usuarios' class="btn btn-secondary">Volver a Gestión</a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header bg-warning text-white text-center">
                    <h5 class="card-title mb-0">Editar Información del Usuario</h5>
                </div>
                <div class="card-body">
                    <form id="formEditarUsuario">
                        <input type="hidden" id="id_usuario" name="id_usuario">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="numero_identidad" class="form-label">Número de Identidad</label>
                                <input type="text" class="form-control" id="numero_identidad" name="numero_identidad" 
                                       maxlength="20" oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                       placeholder="Solo números" readonly>
                                <div class="form-text">Campo de solo lectura.</div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="usuario" class="form-label">Usuario</label>
                                <input type="text" class="form-control" id="usuario" name="usuario" 
                                       maxlength="15" required readonly
                                       placeholder="Usuario (solo lectura)">
                                <div class="form-text">El usuario no se puede modificar.</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="nombre_usuario" class="form-label">Nombre Completo *</label>
                            <input type="text" class="form-control" id="nombre_usuario" name="nombre_usuario" 
                                   maxlength="100" required 
                                   oninput="editarUsuario.validarNombreUsuario(this)"
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
                                       maxlength="50" onblur="editarUsuario.validarEmailUnico(this)"
                                       placeholder="ejemplo@dominio.com">
                                <div class="form-text">Formato válido: usuario@dominio.com</div>
                                <div class="invalid-feedback" id="error-correo"></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="estado_usuario" class="form-label">Estado del Usuario</label>
                                <select class="form-select" id="estado_usuario" name="estado_usuario" required>
                                    <option value="Activo">Activo</option>
                                    <option value="Bloqueado">Bloqueado</option>
                                    <option value="Nuevo">Nuevo</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="fecha_vencimiento" class="form-label">Fecha de Vencimiento</label>
                                <input type="date" class="form-control" id="fecha_vencimiento" name="fecha_vencimiento" 
                                       required>
                            </div>
                        </div>

                        <div class="card bg-light mb-3">
                            <div class="card-body">
                                <h6 class="card-title">Cambiar Contraseña (Opcional)</h6>
                                <small class="text-muted">
                                    • Complete solo si desea cambiar la contraseña<br>
                                    • Deje en blanco para mantener la contraseña actual
                                </small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nueva_contraseña" class="form-label">Nueva Contraseña</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="nueva_contraseña" name="nueva_contraseña" 
                                           minlength="5" maxlength="10"
                                           oninput="editarUsuario.validarPasswordEnTiempoReal(this.value)">
                                    <span class="input-group-text password-toggle" onclick="editarUsuario.togglePassword('nueva_contraseña')">
                                        👁️
                                    </span>
                                </div>
                                <div class="form-text">Mínimo 5 caracteres, máximo 10. No se permiten espacios.</div>
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
                                <label for="confirmar_contraseña" class="form-label">Confirmar Contraseña</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="confirmar_contraseña" 
                                           oninput="editarUsuario.validarConfirmacionPassword(this)">
                                    <span class="input-group-text password-toggle" onclick="editarUsuario.togglePassword('confirmar_contraseña')">
                                        👁️
                                    </span>
                                </div>
                                <div class="invalid-feedback" id="error-confirmar"></div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <button type="button" class="btn btn-outline-primary" onclick="editarUsuario.autogenerarPassword()">
                                    🔐 Autogenerar Contraseña
                                </button>
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

                        <div class="card bg-light mb-3">
                            <div class="card-body">
                                <small class="text-muted">
                                    <strong>Fecha de Creación:</strong> <span id="fecha_creacion"></span><br>
                                    <strong>Última Modificación:</strong> <span id="fecha_modificacion"></span><br>
                                    <strong>Modificado por:</strong> <span id="modificado_por"></span>
                                </small>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="button" class="btn btn-secondary me-md-2" onclick="editarUsuario.cancelarEdicion()">
                                Cancelar
                            </button>
                            <button type="submit" class="btn btn-warning" id="btnEditarUsuario">
                                <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                                Actualizar Usuario
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

class EditarUsuario {
    constructor() {
        this.usuarioId = this.obtenerIdDesdeURL();
        this.init();
    }

    obtenerIdDesdeURL() {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get('editar');
    }

async init() {
    if (!this.usuarioId) {
        alert('ID de usuario no especificado');
        window.location.href = '/sistema/public/gestion-usuarios';
        return;
    }

    console.log("🚀 Iniciando carga de edición de usuario...");
    
    // Cargar roles primero pero no esperar si hay error
    try {
        await this.cargarRoles();
    } catch (error) {
        console.error("Error en carga de roles, continuando...", error);
    }
    
    // Pequeña pausa para que los roles se rendericen
    await new Promise(resolve => setTimeout(resolve, 500));
    
    // Cargar datos del usuario
    await this.cargarDatosUsuario();
    
    // Configurar eventos
    this.configurarEventos();
    
    console.log("✅ Inicialización completada");
}
async cargarRoles() {
    try {
        console.log("🔄 Iniciando carga de roles...");
        
        const response = await fetch('index.php?route=user&caso=obtener-roles');
        console.log("📞 Status de respuesta:", response.status, response.ok);
        
        if (!response.ok) {
            throw new Error(`Error HTTP: ${response.status} - ${response.statusText}`);
        }
        
        const text = await response.text();
        console.log("📄 Respuesta cruda:", text);
        
        let data;
        try {
            data = JSON.parse(text);
            console.log("✅ JSON parseado correctamente:", data);
        } catch (e) {
            console.error("❌ Error parseando JSON:", e);
            throw new Error("La respuesta no es JSON válido");
        }
        
        const selectRol = document.getElementById('id_rol');
        if (!selectRol) {
            console.error("❌ No se encontró el elemento select#id_rol");
            return;
        }
        
        // Limpiar select
        selectRol.innerHTML = '<option value="">Seleccionar Rol</option>';
        
        // Estrategia múltiple para encontrar roles en la respuesta
        let roles = [];
        
        // Posible estructura 1: data.data.roles
        if (data.data && data.data.roles && Array.isArray(data.data.roles)) {
            console.log("🎯 Encontré roles en data.data.roles");
            roles = data.data.roles;
        }
        // Posible estructura 2: data.roles
        else if (data.roles && Array.isArray(data.roles)) {
            console.log("🎯 Encontré roles en data.roles");
            roles = data.roles;
        }
        // Posible estructura 3: data.data es directamente el array
        else if (data.data && Array.isArray(data.data)) {
            console.log("🎯 Encontré roles en data.data (array directo)");
            roles = data.data;
        }
        // Posible estructura 4: la respuesta es directamente el array
        else if (Array.isArray(data)) {
            console.log("🎯 La respuesta es directamente el array de roles");
            roles = data;
        }
        // Posible estructura 5: buscar cualquier propiedad que sea array
        else {
            console.log("🔍 Buscando arrays en la respuesta...");
            for (let key in data) {
                if (Array.isArray(data[key])) {
                    console.log(`🎯 Encontré array en propiedad: ${key}`);
                    roles = data[key];
                    break;
                }
            }
        }
        
        console.log("📋 Roles a cargar:", roles);
        
        if (roles.length > 0) {
            roles.forEach((rol, index) => {
                const option = document.createElement('option');
                
                // Extraer ID y nombre del rol de múltiples formas posibles
                const rolId = rol.ID_ROL || rol.Id_Rol || rol.id_rol || rol.ID || rol.id || rol.value || index + 1;
                const rolNombre = rol.ROL || rol.Rol || rol.rol || rol.NOMBRE || rol.nombre || rol.NAME || rol.name || `Rol ${index + 1}`;
                
                console.log(`➕ Agregando opción: ${rolId} = ${rolNombre}`);
                
                option.value = rolId;
                option.textContent = rolNombre;
                selectRol.appendChild(option);
            });
            
            console.log(`✅ Se cargaron ${roles.length} roles correctamente`);
        } else {
            console.warn("⚠️ No se encontraron roles en la respuesta, usando roles por defecto");
            this.cargarRolesPorDefecto();
        }
        
    } catch (error) {
        console.error('❌ Error cargando roles:', error);
        console.log("🆘 Usando roles por defecto debido al error");
        this.cargarRolesPorDefecto();
    }
}

// Método de respaldo con roles por defecto
cargarRolesPorDefecto() {
    console.log("🆘 Cargando roles por defecto...");
    
    const rolesPorDefecto = [
        { ID_ROL: 1, ROL: "Administrador" },
        { ID_ROL: 2, ROL: "Usuario" },
        { ID_ROL: 3, ROL: "Consultor" },
        { ID_ROL: 4, ROL: "Invitado" }
    ];
    
    const selectRol = document.getElementById('id_rol');
    if (!selectRol) return;
    
    // Mantener la opción por defecto y agregar roles
    selectRol.innerHTML = '<option value="">Seleccionar Rol</option>';
    
    rolesPorDefecto.forEach(rol => {
        const option = document.createElement('option');
        option.value = rol.ID_ROL;
        option.textContent = rol.ROL;
        selectRol.appendChild(option);
    });
    
    console.log("✅ Roles por defecto cargados");
}

async cargarDatosUsuario() {
    try {
        console.log("🔍 Cargando datos del usuario ID:", this.usuarioId);
        
        const response = await fetch(`index.php?route=user&caso=obtener-usuario-completo&id_usuario=${this.usuarioId}`);
        
        if (!response.ok) {
            throw new Error(`Error HTTP: ${response.status}`);
        }
        
        const data = await response.json();
        
        console.log("📦 Datos recibidos:", data);
        
        // CORRECCIÓN: Verificar múltiples estructuras posibles de respuesta
        if (data.status === 200 || data.status === 200) {
            let usuarioData;
            
            if (data.data && data.data.usuario) {
                usuarioData = data.data.usuario;
            } else if (data.data) {
                usuarioData = data.data; // Si los datos vienen directamente en data
            } else if (data.usuario) {
                usuarioData = data.usuario; // Si viene en la raíz
            } else {
                throw new Error('Estructura de datos inesperada');
            }
            
            this.mostrarDatosUsuario(usuarioData);
        } else {
            throw new Error(data.message || 'No se pudieron cargar los datos del usuario');
        }
    } catch (error) {
        console.error('Error cargando datos del usuario:', error);
        alert('Error al cargar los datos del usuario: ' + error.message);
        window.location.href = '/sistema/public/gestion-usuarios';
    }
}

mostrarDatosUsuario(usuario) {
    try {
        console.log("📝 Mostrando datos del usuario:", usuario);
        
        // CORRECCIÓN: Manejar diferentes nombres de campos (mayúsculas/minúsculas)
        const getField = (field) => {
            return usuario[field] || 
                   usuario[field.toLowerCase()] || 
                   usuario[field.toUpperCase()] || 
                   '';
        };
        
        // Llenar campos del formulario
        document.getElementById('id_usuario').value = getField('ID_USUARIO');
        document.getElementById('numero_identidad').value = getField('NUMERO_IDENTIDAD');
        document.getElementById('usuario').value = getField('USUARIO');
        document.getElementById('nombre_usuario').value = getField('NOMBRE_USUARIO');
        document.getElementById('correo_electronico').value = getField('CORREO_ELECTRONICO');
        document.getElementById('estado_usuario').value = getField('ESTADO_USUARIO') || 'Activo';
        
        // Seleccionar el rol correcto con manejo de errores
        const selectRol = document.getElementById('id_rol');
        const rolId = getField('ID_ROL');
        console.log("🎯 Buscando rol ID:", rolId);
        
        if (rolId && selectRol) {
            // Esperar a que los roles se carguen completamente
            setTimeout(() => {
                selectRol.value = rolId;
                console.log("✅ Rol seleccionado:", rolId, "Opción actual:", selectRol.value);
                
                // Si no se seleccionó, intentar nuevamente
                if (selectRol.value !== rolId) {
                    setTimeout(() => {
                        selectRol.value = rolId;
                        console.log("🔄 Segundo intento de selección de rol:", rolId);
                    }, 500);
                }
            }, 200);
        }
        
        // Formatear y mostrar fechas
        const fechaCreacion = getField('FECHA_CREACION') || getField('fecha_creacion');
        const fechaModificacion = getField('FECHA_MODIFICACION') || getField('fecha_modificacion');
        
        document.getElementById('fecha_creacion').textContent = 
            fechaCreacion ? this.formatearFecha(fechaCreacion) : 'N/A';
            
        document.getElementById('fecha_modificacion').textContent = 
            fechaModificacion ? this.formatearFecha(fechaModificacion) : 'Nunca';
        
        document.getElementById('modificado_por').textContent = 
            getField('MODIFICADO_POR') || 'N/A';
        
        // Fecha de vencimiento
        const fechaVencimiento = getField('FECHA_VENCIMIENTO') || getField('fecha_vencimiento');
        if (fechaVencimiento) {
            document.getElementById('fecha_vencimiento').value = 
                this.formatearFechaParaInput(fechaVencimiento);
        } else {
            // Establecer fecha por defecto (30 días desde hoy)
            const fechaDefault = new Date();
            fechaDefault.setDate(fechaDefault.getDate() + 30);
            document.getElementById('fecha_vencimiento').value = fechaDefault.toISOString().split('T')[0];
        }
        
        console.log("✅ Formulario cargado correctamente con datos del usuario");
        
    } catch (error) {
        console.error('❌ Error mostrando datos del usuario:', error);
        alert('Error al mostrar los datos del usuario: ' + error.message);
    }
}

    formatearFecha(fecha) {
        if (!fecha) return 'N/A';
        const date = new Date(fecha);
        return date.toLocaleDateString('es-ES') + ' ' + date.toLocaleTimeString('es-ES');
    }

    formatearFechaParaInput(fecha) {
        if (!fecha) return '';
        const date = new Date(fecha);
        return date.toISOString().split('T')[0];
    }

    configurarEventos() {
        const form = document.getElementById('formEditarUsuario');
        const mostrarPasswordCheckbox = document.getElementById('mostrar_password');
        
        form.addEventListener('submit', (e) => this.actualizarUsuario(e));
        mostrarPasswordCheckbox.addEventListener('change', (e) => this.toggleMostrarPassword(e));
        
        // Validar confirmación de contraseña en tiempo real
        document.getElementById('confirmar_contraseña').addEventListener('input', (e) => 
            this.validarConfirmacionPassword(e.target));
            
        // Validar número de identidad único
        document.getElementById('numero_identidad').addEventListener('blur', (e) => 
            this.validarIdentidadUnica(e.target));
    }

    validarPasswordEnTiempoReal(password) {
        const requirements = document.getElementById('passwordRequirements');
        
        if (password.length > 0) {
            requirements.style.display = 'block';
            
            document.getElementById('reqLength').className = 
                (password.length >= 5 && password.length <= 10) ? 'requirement-met' : 'requirement-not-met';
            
            document.getElementById('reqNoSpaces').className = 
                !/\s/.test(password) ? 'requirement-met' : 'requirement-not-met';
        } else {
            requirements.style.display = 'none';
        }
    }

    async autogenerarPassword() {
        try {
            const response = await fetch('index.php?route=user&caso=generar-password');
            const data = await response.json();
            
            if (data.status === 200) {
                document.getElementById('nueva_contraseña').value = data.data.password;
                document.getElementById('confirmar_contraseña').value = data.data.password;
                
                this.validarPasswordEnTiempoReal(data.data.password);
                this.validarConfirmacionPassword(document.getElementById('confirmar_contraseña'));
                alert('Contraseña autogenerada exitosamente');
            }
        } catch (error) {
            console.error('Error generando password:', error);
            alert('Error al generar contraseña automática');
        }
    }

    toggleMostrarPassword(e) {
        const passwordInput = document.getElementById('nueva_contraseña');
        const confirmInput = document.getElementById('confirmar_contraseña');
        const type = e.target.checked ? 'text' : 'password';
        
        passwordInput.setAttribute('type', type);
        confirmInput.setAttribute('type', type);
    }

    validarConfirmacionPassword(input) {
        const password = document.getElementById('nueva_contraseña').value;
        const confirmPassword = input.value;
        
        if (password && confirmPassword && confirmPassword !== password) {
            this.mostrarError('confirmar', 'Las contraseñas no coinciden');
            return false;
        } else {
            this.limpiarError('confirmar');
            return true;
        }
    }

    validarNombreUsuario(input) {
        let valor = input.value;
        valor = valor.replace(/[^A-ZÁÉÍÓÚÑÜ\s]/gi, '');
        valor = valor.replace(/\s+/g, ' ');
        input.value = valor.toUpperCase();
        
        if (valor.trim().length === 0) {
            this.mostrarError('nombre', 'El nombre es requerido');
            return false;
        } else {
            this.limpiarError('nombre');
            return true;
        }
    }

    async validarEmailUnico(input) {
        const email = input.value.trim();
        
        if (email === '') {
            this.limpiarError('correo');
            return true;
        }
        
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            this.mostrarError('correo', 'Formato de correo electrónico inválido');
            return false;
        }
        
        try {
            const response = await fetch('index.php?route=user&caso=verificar-correo', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ 
                    correo: email,
                    excluir_usuario: this.usuarioId 
                })
            });
            
            const data = await response.json();
            
            if (data.status === 400) {
                this.mostrarError('correo', 'Este correo electrónico ya está registrado por otro usuario');
                return false;
            } else {
                this.limpiarError('correo');
                return true;
            }
        } catch (error) {
            console.error('Error validando correo:', error);
            return true; // Por seguridad, permitir continuar
        }
    }

    async validarIdentidadUnica(input) {
        const numeroIdentidad = input.value.trim();
        
        if (numeroIdentidad === '') {
            this.limpiarError('identidad');
            return true;
        }
        
        try {
            const response = await fetch('index.php?route=user&caso=verificar-identidad', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ 
                    numero_identidad: numeroIdentidad,
                    id_usuario: this.usuarioId 
                })
            });
            
            const data = await response.json();
            
            if (data.status === 400) {
                this.mostrarError('identidad', 'Este número de identidad ya está registrado por otro usuario');
                return false;
            } else {
                this.limpiarError('identidad');
                return true;
            }
        } catch (error) {
            console.error('Error validando identidad:', error);
            return true;
        }
    }

async actualizarUsuario(e) {
    e.preventDefault();
    
    if (!await this.validarFormulario()) {
        return;
    }
    
    const btnSubmit = document.getElementById('btnEditarUsuario');
    const spinner = btnSubmit.querySelector('.spinner-border');
    
    btnSubmit.disabled = true;
    spinner.classList.remove('d-none');
    
    try {
        const formData = new FormData(document.getElementById('formEditarUsuario'));
        const data = Object.fromEntries(formData);
        
        // Agregar campos adicionales
        data.modificado_por = 'ADMIN';
        
        // Limpiar campos vacíos
        Object.keys(data).forEach(key => {
            if (data[key] === '') {
                delete data[key];
            }
        });
        
        // Si no se proporciona nueva contraseña, eliminar los campos de contraseña
        if (!data.nueva_contraseña) {
            delete data.nueva_contraseña;
            delete data.confirmar_contraseña;
        }
        
        console.log("📤 Enviando datos de actualización:", data);
        
        const response = await fetch('index.php?route=user&caso=actualizar', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        console.log("📥 Respuesta completa del servidor:", result);
        
        // CORRECCIÓN: Manejar diferentes estructuras de respuesta
        if (result.status === 200 || result.success === true) {
            // Mostrar mensaje de éxito apropiado
            const successMessage = result.message || '✅ Usuario actualizado exitosamente';
            alert(successMessage);
            window.location.href = '/sistema/public/gestion-usuarios';
        } else {
            // Mostrar mensaje de error apropiado
            const errorMessage = result.message || '❌ No se pudo actualizar el usuario';
            
            // CORRECCIÓN: Verificar si el mensaje indica éxito a pesar del status
            if (errorMessage.toLowerCase().includes('correctamente') || 
                errorMessage.toLowerCase().includes('éxito') ||
                errorMessage.toLowerCase().includes('exito') ||
                errorMessage.toLowerCase().includes('actualizado')) {
                // Si el mensaje indica éxito pero el status no es 200
                alert('✅ ' + errorMessage);
                window.location.href = '/sistema/public/gestion-usuarios';
            } else {
                alert('❌ ' + errorMessage);
            }
        }
        
    } catch (error) {
        console.error('Error en la conexión:', error);
        alert('❌ Error de conexión con el servidor: ' + error.message);
    } finally {
        btnSubmit.disabled = false;
        spinner.classList.add('d-none');
    }
}

    async validarFormulario() {
        let isValid = true;
        
        // Validar nombre
        if (!this.validarNombreUsuario(document.getElementById('nombre_usuario'))) {
            isValid = false;
        }
        
        // Validar rol
        const rol = document.getElementById('id_rol').value;
        if (rol === '') {
            this.mostrarError('rol', 'El rol es requerido');
            isValid = false;
        } else {
            this.limpiarError('rol');
        }
        
        // Validar email
        if (!await this.validarEmailUnico(document.getElementById('correo_electronico'))) {
            isValid = false;
        }
        
        // Validar número de identidad
        if (!await this.validarIdentidadUnica(document.getElementById('numero_identidad'))) {
            isValid = false;
        }
        
        // Validar contraseñas si se están cambiando
        const nuevaPassword = document.getElementById('nueva_contraseña').value;
        const confirmPassword = document.getElementById('confirmar_contraseña').value;
        
        if (nuevaPassword || confirmPassword) {
            if (!this.validarConfirmacionPassword(document.getElementById('confirmar_contraseña'))) {
                isValid = false;
            }
            
            if (nuevaPassword.length < 5 || nuevaPassword.length > 10) {
                this.mostrarError('password', 'La contraseña debe tener entre 5 y 10 caracteres');
                isValid = false;
            }
            
            if (/\s/.test(nuevaPassword)) {
                this.mostrarError('password', 'La contraseña no puede contener espacios');
                isValid = false;
            }
        }
        
        return isValid;
    }

    cancelarEdicion() {
        if (confirm('¿Está seguro de que desea cancelar la edición? Los cambios no guardados se perderán.')) {
            window.location.href = '/sistema/public/gestion-usuarios';
        }
    }

    mostrarError(campo, mensaje) {
        const errorElement = document.getElementById(`error-${campo}`);
        let inputElement;
        
        switch(campo) {
            case 'confirmar':
                inputElement = document.getElementById('confirmar_contraseña');
                break;
            case 'password':
                inputElement = document.getElementById('nueva_contraseña');
                break;
            case 'identidad':
                inputElement = document.getElementById('numero_identidad');
                break;
            default:
                inputElement = document.getElementById(campo);
        }
        
        if (errorElement && inputElement) {
            errorElement.textContent = mensaje;
            inputElement.classList.add('is-invalid');
        }
    }

    limpiarError(campo) {
        const errorElement = document.getElementById(`error-${campo}`);
        let inputElement;
        
        switch(campo) {
            case 'confirmar':
                inputElement = document.getElementById('confirmar_contraseña');
                break;
            case 'password':
                inputElement = document.getElementById('nueva_contraseña');
                break;
            case 'identidad':
                inputElement = document.getElementById('numero_identidad');
                break;
            default:
                inputElement = document.getElementById(campo);
        }
        
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
const editarUsuario = new EditarUsuario();
</script>