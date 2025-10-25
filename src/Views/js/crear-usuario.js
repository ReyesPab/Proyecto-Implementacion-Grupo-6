class CrearUsuario {
    constructor() {
        this.init();
    }

    async init() {
        await this.cargarRoles();
        this.cargarFechasAutomaticas();
        this.configurarEventos();
    }

    async cargarRoles() {
        try {
            const response = await fetch('index.php?route=user&caso=obtener-roles');
            const data = await response.json();
            
            if (data.status === 200) {
                const selectRol = document.getElementById('id_rol');
                selectRol.innerHTML = '<option value="">Seleccionar Rol</option>';
                
                data.data.roles.forEach(rol => {
                    const option = document.createElement('option');
                    option.value = rol.Id_Rol;
                    option.textContent = rol.Rol;
                    selectRol.appendChild(option);
                });
            }
        } catch (error) {
            console.error('Error cargando roles:', error);
        }
    }

    cargarFechasAutomaticas() {
        const fechaActual = new Date().toLocaleDateString('es-ES');
        document.getElementById('fecha_creacion').value = fechaActual;
        document.getElementById('fecha_vencimiento').value = 'Calculada automáticamente';
    }

    configurarEventos() {
        const form = document.getElementById('formCrearUsuario');
        const autogenerarCheckbox = document.getElementById('autogenerar_password');
        
        form.addEventListener('submit', (e) => this.crearUsuario(e));
        autogenerarCheckbox.addEventListener('change', (e) => this.toggleAutogenerarPassword(e));
        
        // Validar usuario único en tiempo real
        document.getElementById('usuario').addEventListener('blur', () => this.validarUsuarioUnico());
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
                    passwordInput.readOnly = true;
                    confirmInput.readOnly = true;
                }
            } catch (error) {
                console.error('Error generando password:', error);
            }
        } else {
            passwordInput.value = '';
            confirmInput.value = '';
            passwordInput.readOnly = false;
            confirmInput.readOnly = false;
        }
    }

    async validarUsuarioUnico() {
        const usuario = document.getElementById('usuario').value.trim();
        
        if (usuario.length === 0) return;
        
        try {
            // Simular validación - en una implementación real harías una llamada API
            this.limpiarError('usuario');
        } catch (error) {
            console.error('Error validando usuario:', error);
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
            data.creado_por = '<?php echo $_SESSION["usuario"] ?? "ADMIN"; ?>';
            
            const response = await fetch('index.php?route=user&caso=crear', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            });
            
            const result = await response.json();
            
            if (result.status === 201) {
                this.mostrarMensaje('success', result.message);
                setTimeout(() => {
                    window.location.href = 'index.php?view=gestion-usuarios';
                }, 2000);
            } else {
                this.mostrarMensaje('error', result.message);
            }
        } catch (error) {
            console.error('Error creando usuario:', error);
            this.mostrarMensaje('error', 'Error de conexión');
        } finally {
            btnSubmit.disabled = false;
            spinner.classList.add('d-none');
        }
    }

    validarFormulario() {
        let isValid = true;
        
        // Validar contraseñas coinciden
        const password = document.getElementById('contraseña').value;
        const confirmPassword = document.getElementById('confirmar_contraseña').value;
        
        if (password !== confirmPassword) {
            this.mostrarError('confirmar', 'Las contraseñas no coinciden');
            isValid = false;
        } else {
            this.limpiarError('confirmar');
        }
        
        // Validar longitud de contraseña
        if (password.length < 5 || password.length > 10) {
            this.mostrarError('password', 'La contraseña debe tener entre 5 y 10 caracteres');
            isValid = false;
        } else {
            this.limpiarError('password');
        }
        
        // Validar espacios en contraseña
        if (/\s/.test(password)) {
            this.mostrarError('password', 'La contraseña no puede contener espacios');
            isValid = false;
        }
        
        // Validar email si se proporciona
        const email = document.getElementById('correo_electronico').value;
        if (email && !this.validarEmail(email)) {
            this.mostrarError('correo', 'Formato de correo electrónico inválido');
            isValid = false;
        } else {
            this.limpiarError('correo');
        }
        
        return isValid;
    }

    validarEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
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

    mostrarMensaje(tipo, mensaje) {
        // Implementar toast o alerta
        alert(mensaje);
    }
}

// Funciones globales
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
    input.setAttribute('type', type);
}

// Inicializar
document.addEventListener('DOMContentLoaded', () => {
    new CrearUsuario();
});