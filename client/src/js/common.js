// Este archivo contiene funciones de utilidad comunes que se usan en todas las páginas

// Funciones de utilidad comunes para toda la aplicación
document.addEventListener('DOMContentLoaded', function() {
    try {
        // Inicializar todos los tooltips de Bootstrap
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Inicializar todos los popovers de Bootstrap
        const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
        popoverTriggerList.map(function (popoverTriggerEl) {
            return new bootstrap.Popover(popoverTriggerEl);
        });

        // Inicializar todos los select2
        const selectElements = document.querySelectorAll('.select2');
        if (selectElements.length > 0 && typeof $.fn.select2 !== 'undefined') {
            selectElements.forEach(select => {
                $(select).select2({
                    theme: 'bootstrap-5',
                    width: '100%'
                });
            });
        }

        // Inicializar todos los datepickers
        const dateInputs = document.querySelectorAll('.datepicker');
        if (dateInputs.length > 0 && typeof $.fn.datepicker !== 'undefined') {
            dateInputs.forEach(input => {
                $(input).datepicker({
                    format: 'yyyy-mm-dd',
                    autoclose: true,
                    language: 'es'
                });
            });
        }

        // Inicializar todos los timepickers
        const timeInputs = document.querySelectorAll('.timepicker');
        if (timeInputs.length > 0 && typeof $.fn.timepicker !== 'undefined') {
            timeInputs.forEach(input => {
                $(input).timepicker({
                    showMeridian: false,
                    minuteStep: 1
                });
            });
        }

        // Inicializar todos los checkboxes personalizados
        const checkboxes = document.querySelectorAll('.form-check-input');
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const formCheck = this.closest('.form-check');
                if (formCheck) {
                    if (this.checked) {
                        formCheck.classList.add('checked');
                    } else {
                        formCheck.classList.remove('checked');
                    }
                }
            });
        });

        // Inicializar todos los desplegables de Bootstrap
        const dropdowns = document.querySelectorAll('.dropdown-toggle');
        dropdowns.forEach(dropdown => {
            dropdown.addEventListener('click', function(e) {
                e.preventDefault();
                const dropdownMenu = this.nextElementSibling;
                if (dropdownMenu && dropdownMenu.classList.contains('dropdown-menu')) {
                    dropdownMenu.classList.toggle('show');
                }
            });
        });

        // Cerrar dropdowns al hacer clic fuera
        document.addEventListener('click', function(e) {
            const dropdowns = document.querySelectorAll('.dropdown-menu.show');
            dropdowns.forEach(dropdown => {
                if (!dropdown.contains(e.target) && !e.target.classList.contains('dropdown-toggle')) {
                    dropdown.classList.remove('show');
                }
            });
        });

    } catch (error) {
        console.warn('Error en la inicialización de common.js:', error);
    }
});

// Función para mostrar alertas
function mostrarAlerta(mensaje, tipo = 'error') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${tipo} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${mensaje}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    document.querySelector('form')?.insertBefore(alertDiv, document.querySelector('form').firstChild);
}

// Función para validar fechas
function validarFechas(fechaInicio, fechaFin) {
    const inicio = new Date(fechaInicio);
    const fin = new Date(fechaFin);
    return inicio <= fin;
}

// Función para validar horas
function validarHoras(horaInicio, horaFin) {
    return horaInicio < horaFin;
}

// Función para formatear fecha
function formatearFecha(fecha) {
    return new Date(fecha).toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}

// Función para formatear hora
function formatearHora(hora) {
    return hora.substring(0, 5);
}

// Función para limpiar formulario
function limpiarFormulario(formId) {
    const form = document.getElementById(formId);
    if (form) {
        form.reset();
        // Limpiar select2 si existe
        const select2Elements = form.querySelectorAll('.select2');
        if (select2Elements.length > 0 && typeof $.fn.select2 !== 'undefined') {
            select2Elements.forEach(select => {
                $(select).val(null).trigger('change');
            });
        }
    }
}

// Función para deshabilitar botón
function deshabilitarBoton(boton) {
    if (boton) {
        boton.disabled = true;
        boton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Procesando...';
    }
}

// Función para habilitar botón
function habilitarBoton(boton, textoOriginal) {
    if (boton) {
        boton.disabled = false;
        boton.innerHTML = textoOriginal;
    }
} 