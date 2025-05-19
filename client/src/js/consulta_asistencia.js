// Este archivo controla la página de consulta de asistencias.
// Aquí se gestiona el formulario para buscar asistencias por docente, día o mes, y se muestran los resultados.

document.addEventListener('DOMContentLoaded', function() {
    try {
        const form = document.getElementById('form-consulta');
        const tablaAsistencia = document.getElementById('tabla-asistencias');
        const tipoConsulta = document.getElementById('tipo-consulta');
        const campoDocente = document.getElementById('campo-docente');
        const tipoFecha = document.getElementById('tipo-fecha');
        const campoFecha = document.getElementById('campo-fecha');
        const campoMes = document.getElementById('campo-mes');
        const btnBuscar = document.getElementById('btnBuscar');
        const btnCancelar = document.getElementById('btnCancelar');
        const fechaInput = document.getElementById('fecha');

        if (!form) {
            console.warn('Formulario no encontrado');
            return;
        }

        if (!tablaAsistencia) {
            console.warn('Tabla de asistencia no encontrada');
            return;
        }

        if (!fechaInput) {
            console.warn('Campo de fecha no encontrado');
            return;
        }

        // Establecer la fecha actual como valor predeterminado
        const hoy = new Date();
        fechaInput.value = hoy.toISOString().split('T')[0];

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

        // Función para validar el formulario
        function validarFormulario() {
            const fecha = new Date(fechaInput.value);
            const hoy = new Date();
            hoy.setHours(0, 0, 0, 0);

            if (fecha < hoy) {
                mostrarAlerta('No se pueden consultar asistencias para fechas pasadas');
                return false;
            }

            return true;
        }

        // Función para obtener la asistencia
        function obtenerAsistencia() {
            // Recoger los valores del formulario
            const tipoConsultaValor = tipoConsulta.value;
            const tipoFechaValor = tipoFecha.value;
            const documentoValor = document.getElementById('documento').value;
            const fechaValor = document.getElementById('fecha').value;
            const mesValor = document.getElementById('mes').value;

            // Construir el objeto de datos a enviar
            const datos = {
                tipo_consulta: tipoConsultaValor,
                tipo_fecha: tipoFechaValor,
                documento: documentoValor,
                fecha: fechaValor,
                mes: mesValor
            };

            return CONFIG.fetch(`${CONFIG.API_URL}?accion=${CONFIG.ACCIONES.ASISTENCIA}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(datos)
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    throw new Error(data.message || CONFIG.MENSAJES.ERROR_SERVIDOR);
                }
                return data.asistencias;
            });
        }

        // Función para mostrar la asistencia en la tabla
        function mostrarAsistencia(asistencia) {
            const tbody = tablaAsistencia.getElementsByTagName('tbody')[0];
            if (!tbody) {
                console.warn('Elemento tbody no encontrado');
                return;
            }

            tbody.innerHTML = '';

            if (asistencia.length === 0) {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td colspan="5" class="text-center">
                        No hay registros de asistencia para la fecha seleccionada
                    </td>
                `;
                tbody.appendChild(row);
                return;
            }

            asistencia.forEach(registro => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${registro.nombre}</td>
                    <td>${registro.fecha}</td>
                    <td>${registro.hora_entrada || '-'}</td>
                    <td>${registro.hora_salida || '-'}</td>
                    <td>${registro.hora_entrada ? 'Presente' : ''}</td>
                `;
                tbody.appendChild(row);
            });
        }

        // Función para cargar la asistencia
        async function cargarAsistencia() {
            try {
                const response = await CONFIG.fetch(`${CONFIG.API_URL}?accion=${CONFIG.ACCIONES.ASISTENCIA}`);
                const data = await response.json();

                if (data.success) {
                    const tbody = tablaAsistencia.getElementsByTagName('tbody')[0];
                    if (!tbody) {
                        console.warn('Elemento tbody no encontrado');
                        return;
                    }

                    tbody.innerHTML = '';
                    data.asistencias.forEach(asistencia => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td>${asistencia.fecha}</td>
                            <td>${asistencia.hora_inicio}</td>
                            <td>${asistencia.hora_fin}</td>
                            <td>${asistencia.estado}</td>
                        `;
                        tbody.appendChild(tr);
                    });
                } else {
                    throw new Error(data.message || CONFIG.MENSAJES.ERROR_SERVIDOR);
                }
            } catch (error) {
                mostrarAlerta(error.message || CONFIG.MENSAJES.ERROR_CONEXION);
            }
        }

        // Función para cargar los docentes
        async function cargarDocentes() {
            try {
                const response = await CONFIG.fetch(`${CONFIG.API_URL}?accion=${CONFIG.ACCIONES.DOCENTES}`);
                const data = await response.json();
                
                if (data.success) {
                    const selectDocente = document.getElementById('documento');
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

        // Evento para cambiar la visibilidad del campo docente
        tipoConsulta.addEventListener('change', function() {
            const selectDocente = document.getElementById('documento');
            if (this.value === 'docente') {
                campoDocente.style.display = 'block';
                selectDocente.required = true;
            } else {
                campoDocente.style.display = 'none';
                selectDocente.required = false;
            }
        });

        // Evento para cambiar entre fecha y mes
        tipoFecha.addEventListener('change', function() {
            const inputFecha = document.getElementById('fecha');
            const inputMes = document.getElementById('mes');
            
            if (this.value === 'dia') {
                campoFecha.style.display = 'block';
                campoMes.style.display = 'none';
                inputFecha.required = true;
                inputMes.required = false;
            } else {
                campoFecha.style.display = 'none';
                campoMes.style.display = 'block';
                inputFecha.required = false;
                inputMes.required = true;
            }
        });

        // Evento submit del formulario
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            if (!validarFormulario()) {
                return;
            }

            if (btnBuscar) {
                btnBuscar.disabled = true;
                btnBuscar.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Buscando...';
            }

            try {
                const asistencia = await obtenerAsistencia();
                mostrarAsistencia(asistencia);
            } catch (error) {
                console.error('Error:', error);
                mostrarAlerta(error.message || CONFIG.MENSAJES.ERROR_CONEXION);
            } finally {
                if (btnBuscar) {
                    btnBuscar.disabled = false;
                    btnBuscar.innerHTML = 'Buscar';
                }
            }
        });

        // Evento click del botón cancelar
        if (btnCancelar) {
            btnCancelar.addEventListener('click', function() {
                window.location.href = 'index.php';
            });
        }

        // Mostrar mensaje inicial en la tabla
        const tbody = tablaAsistencia.getElementsByTagName('tbody')[0];
        if (tbody) {
            tbody.innerHTML = `<tr><td colspan="5" class="text-center">Realiza una consulta para ver resultados</td></tr>`;
        }

        // Cargar docentes al iniciar
        cargarDocentes();
    } catch (error) {
        console.warn('Error en la inicialización de consulta_asistencia:', error);
    }
});