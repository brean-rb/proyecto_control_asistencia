<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control de Asistencia</title>
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

    <!-- Contenido principal -->
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div id="alertContainer"></div>
                <h2 class="text-center mb-4">Bienvenido, <span id="userName"></span></h2>
                
                <!-- Botones para inicio/fin de jornada -->
                <div class="d-flex justify-content-center gap-3 mb-4">
                    <button id="btnInicio" class="btn btn-inicio-jornada">
                        <i class="fas fa-play me-2"></i>
                        Iniciar Jornada
                    </button>
                    <button id="btnFin" class="btn btn-fin-jornada" disabled>
                        <i class="fas fa-stop me-2"></i>
                        Finalizar Jornada
                    </button>
                </div>

                <!-- Tabla de horario -->
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Día</th>
                                <th>Hora Inicio</th>
                                <th>Hora Fin</th>
                                <th>Asignatura</th>
                                <th>Grupo</th>
                                <th>Aula</th>
                            </tr>
                        </thead>
                        <tbody id="tablaHorario">
                            <!-- El horario se cargará dinámicamente -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- JS de Bootstrap (CDN) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Tu JavaScript personalizado -->
    <script src="./js/config.js"></script>
    <script src="./js/common.js"></script>
    <script src="./js/app.js"></script>
</body>
</html>
