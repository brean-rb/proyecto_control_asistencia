// Este archivo controla la página de registro de ausencias de docentes.
// Permite registrar una ausencia para un día o un periodo, seleccionar horas concretas y ver el horario del profesor.

document.addEventListener('DOMContentLoaded', function() {
    try {
        const form = document.getElementById('form-ausencia');
        const selectDocente = document.getElementById('select-docente');
        const tipoAusencia = document.getElementsByName('tipo');
        const campoMismoDia = document.getElementById('campo-mismo-dia');
        const campoPeriodo = document.getElementById('campo-periodo');
        const btnGuardar = document.getElementById('btnGuardar');
        const btnCancelar = document.getElementById('btnCancelar');
        const fechaInput = document.getElementById('fecha');
        const motivoInput = document.getElementById('motivo');
        const horarioContainer = document.getElementById('horario-profesor');

        if (!form) {
            console.error('Formulario no encontrado. ID del formulario:', 'form-ausencia');
            return;
        }

        // Establecer la fecha actual como valor predeterminado
        const hoy = new Date();
        if (fechaInput) {
            fechaInput.value = hoy.toISOString().split('T')[0];
        }

        // Función para mostrar alertas
        function mostrarAlerta(mensaje, tipo = 'error') {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${tipo} alert-dismissible fade show`;
            alertDiv.innerHTML = `
                ${mensaje}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;
            form.insertBefore(alertDiv, form.firstChild);
        }

        // Función para cargar los docentes
        async function cargarDocentes() {
            try {
                if (!selectDocente) {
                    console.error('Elemento select-docente no encontrado');
                    return;
                }

                const response = await CONFIG.fetch(`${CONFIG.API_URL}?accion=${CONFIG.ACCIONES.DOCENTES}`);
                const data = await response.json();
                
                if (data.success) {
                    selectDocente.innerHTML = '<option value="">Selecciona un docente...</option>';
                    
                    data.docentes.forEach(docente => {
                        const option = document.createElement('option');
                        option.value = docente.document;
                        option.textContent = docente.nombre;
                        selectDocente.appendChild(option);
                    });
                } else {
                    throw new Error(data.message || 'Error al cargar los docentes');
                }
            } catch (error) {
                console.error('Error al cargar docentes:', error);
                mostrarAlerta('Error al cargar la lista de docentes: ' + error.message);
            }
        }

        // Función para cargar el horario del docente
        async function cargarHorarioDocente(documento, fecha) {
            try {
                if (!horarioContainer) {
                    console.error('Contenedor de horario no encontrado');
                    return;
                }

                const response = await CONFIG.fetch(`${CONFIG.API_URL}?accion=${CONFIG.ACCIONES.HORARIO_PROFESOR}&documento=${documento}&fecha=${fecha}`);
                const data = await response.json();

                if (data.success && data.horario) {
                    // Crear tabla de horario
                    let html = `
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Hora</th>
                                        <th>Grupo</th>
                                        <th>Materia</th>
                                        <th>Seleccionar</th>
                                    </tr>
                                </thead>
                                <tbody>
                    `;

                    data.horario.forEach(clase => {
                        html += `
                            <tr>
                                <td>${clase.hora_inicio} - ${clase.hora_fin}</td>
                                <td>${clase.grupo}</td>
                                <td>${clase.materia}</td>
                                <td>
                                    <div class="form-check">
                                        <input class="form-check-input clase-checkbox" 
                                               type="checkbox" 
                                               name="clases[]" 
                                               value="${clase.id}"
                                               data-hora-inicio="${clase.hora_inicio}"
                                               data-hora-fin="${clase.hora_fin}">
                                    </div>
                                </td>
                            </tr>
                        `;
                    });

                    html += `
                                </tbody>
                            </table>
                        </div>
                    `;

                    horarioContainer.innerHTML = html;
                } else {
                    horarioContainer.innerHTML = '<div class="alert alert-info">No hay clases programadas para esta fecha</div>';
                }
            } catch (error) {
                console.error('Error al cargar horario:', error);
                horarioContainer.innerHTML = '<div class="alert alert-danger">Error al cargar el horario</div>';
            }
        }

        // Manejar el cambio de tipo de ausencia
        if (tipoAusencia && tipoAusencia.length > 0) {
            tipoAusencia.forEach(radio => {
                radio.addEventListener('change', function() {
                    if (campoMismoDia && campoPeriodo) {
                        if (this.value === 'dia') {
                            campoMismoDia.style.display = 'block';
                            campoPeriodo.style.display = 'none';
                            // Cargar horario si hay docente y fecha seleccionados
                            if (selectDocente.value && fechaInput.value) {
                                cargarHorarioDocente(selectDocente.value, fechaInput.value);
                            }
                        } else {
                            campoMismoDia.style.display = 'none';
                            campoPeriodo.style.display = 'block';
                        }
                    }
                });
            });
        }

        // Evento para cargar horario cuando cambia la fecha
        if (fechaInput) {
            fechaInput.addEventListener('change', function() {
                if (selectDocente.value && tipoAusencia[0].checked) {
                    cargarHorarioDocente(selectDocente.value, this.value);
                }
            });
        }

        // Evento para cargar horario cuando cambia el docente
        if (selectDocente) {
            selectDocente.addEventListener('change', function() {
                if (this.value && fechaInput.value && tipoAusencia[0].checked) {
                    cargarHorarioDocente(this.value, fechaInput.value);
                }
            });
        }

        // Cargar docentes al iniciar
        cargarDocentes();

        // Función para validar el formulario
        function validarFormulario() {
            if (!selectDocente || !selectDocente.value) {
                mostrarAlerta('Debe seleccionar un docente');
                return false;
            }

            if (!fechaInput || !fechaInput.value) {
                mostrarAlerta('Debe seleccionar una fecha');
                return false;
            }

            const fecha = new Date(fechaInput.value);
            const hoy = new Date();
            hoy.setHours(0, 0, 0, 0);

            if (fecha < hoy) {
                mostrarAlerta('No se pueden registrar ausencias para fechas pasadas');
                return false;
            }

            if (!motivoInput || !motivoInput.value.trim()) {
                mostrarAlerta('Debe especificar un motivo');
                return false;
            }

            // Verificar si se seleccionó al menos una clase cuando es ausencia de un día
            if (tipoAusencia[0].checked) {
                const clasesSeleccionadas = document.querySelectorAll('.clase-checkbox:checked');
                if (clasesSeleccionadas.length === 0) {
                    mostrarAlerta('Debe seleccionar al menos una clase');
                    return false;
                }
            }

            return true;
        }

        // Evento submit del formulario
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            if (!validarFormulario()) {
                return;
            }

            try {
                const formData = new FormData(form);
                const datos = {
                    documento: formData.get('documento'),
                    tipo: formData.get('tipo'),
                    fecha: formData.get('fecha'),
                    motivo: formData.get('motivo'),
                    justificada: formData.get('justificada') ? 1 : 0
                };

                // Si es ausencia de un día, agregar las clases seleccionadas
                if (datos.tipo === 'dia') {
                    const clasesSeleccionadas = Array.from(document.querySelectorAll('.clase-checkbox:checked')).map(checkbox => ({
                        id: checkbox.value,
                        hora_inicio: checkbox.dataset.horaInicio,
                        hora_fin: checkbox.dataset.horaFin
                    }));
                    datos.clases = clasesSeleccionadas;
                } else if (datos.tipo === 'periodo') {
                    // Recoger fechas de periodo
                    datos.fecha_inicio = formData.get('fecha_inicio');
                    datos.fecha_fin = formData.get('fecha_fin');
                }

                // Obtener el token
                const token = localStorage.getItem('token');
                if (!token) {
                    throw new Error('No hay token de autenticación');
                }

                const response = await fetch(`${CONFIG.API_URL}?accion=${CONFIG.ACCIONES.AUSENCIA}`, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(datos)
                });

                if (!response.ok) {
                    throw new Error(`Error HTTP: ${response.status}`);
                }

                const data = await response.json();
                
                if (data.success) {
                    // Mostrar modal de éxito
                    const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                    successModal.show();
                    
                    // Limpiar formulario
                    form.reset();
                    if (fechaInput) {
                        fechaInput.value = new Date().toISOString().split('T')[0];
                    }
                    if (horarioContainer) {
                        horarioContainer.innerHTML = '';
                    }
                } else {
                    throw new Error(data.message || 'Error al registrar la ausencia');
                }
            } catch (error) {
                console.error('Error:', error);
                mostrarAlerta(error.message || 'Error al registrar la ausencia');
            }
        });

        // Evento click del botón cancelar
        if (btnCancelar) {
            btnCancelar.addEventListener('click', function() {
                window.location.href = 'index.php';
            });
        }
    } catch (error) {
        console.error('Error en la inicialización de registro_ausencia:', error);
    }
});