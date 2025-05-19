<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informe de Ausencias - Control de Asistencia</title>
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
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <h2 class="text-center fw-bold mb-4">Informes de Ausencias</h2>
                        
                        <form id="form-informe" class="mb-4">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Tipo de informe</label>
                                    <select class="form-select" id="tipo-informe" name="tipo_informe" required>
                                        <option value="dia">Por día</option>
                                        <option value="semana">Por semana</option>
                                        <option value="mes">Por mes</option>
                                        <option value="trimestre">Por trimestre</option>
                                        <option value="curso">Por curso académico</option>
                                        <option value="docente">Por docente</option>
                                    </select>
                                </div>

                                <div class="col-md-4" id="campo-fecha">
                                    <label for="fecha" class="form-label fw-bold">Fecha</label>
                                    <input type="date" class="form-control" id="fecha" name="fecha" required>
                                </div>

                                <div class="col-md-4" id="campo-docente" style="display: none;">
                                    <label for="documento" class="form-label fw-bold">Docente</label>
                                    <select class="form-select" id="documento" name="documento">
                                        <option value="">Selecciona un docente...</option>
                                    </select>
                                </div>

                                <div class="col-12">
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-file-alt me-2"></i>Generar Informe
                                    </button>
                                </div>
                            </div>
                        </form>

                        <div class="table-responsive mt-4">
                            <table class="table table-hover" id="tabla-ausencias">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Fecha Inicio</th>
                                        <th>Fecha Fin</th>
                                        <th>Motivo</th>
                                        <th>Justificada</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Los resultados se cargarán aquí dinámicamente -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/config.js"></script>
    <script src="../js/common.js"></script>
    <script src="./js/informe_ausencias.js"></script>
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        if (typeof CONFIG !== 'undefined' && CONFIG.handleNavVisibility) {
          CONFIG.handleNavVisibility();
        }
      });
    </script>
</body>
</html>