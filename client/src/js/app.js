document.addEventListener('DOMContentLoaded', function() {
    // Prevenir que el enlace redirija al hacer clic
    document.querySelector('#adminDropdown').addEventListener('click', function(e) {
        e.preventDefault();
    });

    // Mejorar la experiencia en dispositivos táctiles
    if('ontouchstart' in document.documentElement) {
        document.querySelector('#adminDropdown').addEventListener('click', function() {
            this.parentElement.classList.toggle('show');
            document.querySelector('.dropdown-menu').classList.toggle('show');
        });
    }
});