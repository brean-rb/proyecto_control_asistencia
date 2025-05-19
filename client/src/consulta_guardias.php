<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta de Guardias - Control de Asistencia</title>
    <link rel="icon" type="image/png" href="./img/favi.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="./css/styles.css" rel="stylesheet">
</head>
<body class="bg-light">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom mb-4">
        <div class="container">
            <a class="navbar-brand text-dark fw-bold flex-shrink-0" href="index.php">
                IES Joan Coromines
            </a>
            <button class="navbar-toggler ms-2" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav mx-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link" href="consulta_guardias.php">Guardias</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="guardias_realizadas.php">Guardias realizadas</a>
                    </li>
                    <li class="nav-item dropdown admin-only" style="display: none;">
                        <a class="nav-link dropdown-toggle" href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Administración
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="adminDropdown">
                            <li><a class="dropdown-item" href="consulta_asistencia.php">Asistencias</a></li>
                            <li><a class="dropdown-item" href="registro_ausencia.php">Registro ausencia</a></li>
                            <li><a class="dropdown-item" href="informe_ausencias.php">Informe ausencia</a></li>
                        </ul>
                    </li>
                </ul>
                <form id="logoutForm" class="d-flex ms-lg-auto mt-3 mt-lg-0">
                    <button type="submit" class="btn btn-danger">
                        log out <i class="fas fa-sign-out-alt ms-2"></i>
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        <div class="row">
            <div class="col-lg-12">
                <h2 class="text-center fw-bold mb-4">Profesores Ausentes Hoy</h2>
                <div class="table-responsive">
                    <table class="table table-hover" id="tabla-profesores-ausentes">
                        <thead class="table-dark">
                            <tr>
                                <th>Profesor</th>
                                <th>Fecha Inicio</th>
                                <th>Fecha Fin</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Los profesores ausentes se cargarán aquí -->
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="col-lg-12 mt-5" id="horario-container" style="display: none;">
                <h2 class="text-center fw-bold mb-4">Horario del Profesor</h2>
                <div class="table-responsive">
                    <table class="table table-hover" id="tabla-horario">
                        <thead class="table-dark">
                            <tr>
                                <th>Horario</th>
                                <th>Asignatura</th>
                                <th>Grupo</th>
                                <th>Aula</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- El horario se cargará aquí -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="../js/config.js"></script>
    <script src="../js/common.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/consulta_guardias.js"></script>

    <!-- Modal de Éxito para guardia reservada -->
    <div class="modal fade success-modal" id="successModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <i class="fas fa-check-circle success-icon"></i>
                    <p class="success-message">Guardia reservada correctamente</p>
                </div>
                <div class="modal-footer border-0 justify-content-center">
                    <button type="button" class="btn btn-success" data-bs-dismiss="modal">Entendido</button>
                </div>
            </div>
        </div>
    </div>

    <script>
      if (typeof CONFIG !== 'undefined' && CONFIG.handleNavVisibility) {
        CONFIG.handleNavVisibility();
      }
    </script>
</body>
</html>