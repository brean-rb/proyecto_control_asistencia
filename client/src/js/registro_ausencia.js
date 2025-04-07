document.addEventListener('DOMContentLoaded', () => {
    const campoMismoDia = document.getElementById('campo-mismo-dia');
    const campoPeriodo = document.getElementById('campo-periodo');
    const tipoRadios = document.querySelectorAll('input[name="tipo"]');

    function toggleCampos() {
        const tipo = document.querySelector('input[name="tipo"]:checked').value;

        if (tipo === 'dia') {
            campoMismoDia.style.display = 'block';
            campoPeriodo.style.display = 'none';

            // Hacer required los campos de mismo día
            document.getElementById('fecha').required = true;
            document.getElementById('hora-inicio').required = true;
            document.getElementById('hora-fin').required = true;

            // Quitar required de los campos de periodo
            document.getElementById('fecha-inicio').required = false;
            document.getElementById('fecha-fin').required = false;
        } else {
            campoMismoDia.style.display = 'none';
            campoPeriodo.style.display = 'block';

            // Hacer required los campos de periodo
            document.getElementById('fecha-inicio').required = true;
            document.getElementById('fecha-fin').required = true;

            // Quitar required de los campos de mismo día
            document.getElementById('fecha').required = false;
            document.getElementById('hora-inicio').required = false;
            document.getElementById('hora-fin').required = false;
        }
    }

    // Toggle inicial y eventos
    tipoRadios.forEach(radio => radio.addEventListener('change', toggleCampos));
    toggleCampos();
});