<?php
session_start();
if (!isset($_SESSION['autenticado']) || $_SESSION['autenticado'] !== true) {
    header('Location: ./login.php');
    exit();
}
$rolUsuario = $_SESSION['rol'] ?? 'profesor';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Guardias Realizadas - IES Joan Coromines</title>
    <link rel="icon" type="image/png" href="./img/favi.png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../vendor/bootstrap-5.0.2-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="./css/styles.css">
</head>
<body class="bg-light">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom mb-4">
        <div class="container">
            <a class="navbar-brand text-dark fw-bold flex-shrink-0" href="index.php">
                IES Joan Coromines
            </a>
            <button 
                class="navbar-toggler ms-2" 
                type="button" 
                data-bs-toggle="collapse" 
                data-bs-target="#navbarMain" 
                aria-controls="navbarMain" 
                aria-expanded="false" 
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav mx-auto align-items-center">
                    <?php if (
                        $rolUsuario === 'admin'
                    ): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="consulta_guardias.php">Guardias</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="guardias_realizadas.php">Guardias realizadas</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Administración
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="adminDropdown">
                                <li><a class="dropdown-item" href="consulta_asistencia.php">Consulta Asistencia</a></li>
                                <li><a class="dropdown-item" href="registro_ausencia.php">Registro Ausencia</a></li>
                                <li><a class="dropdown-item" href="informe_ausencias.php">Informe Ausencias</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link px-4" href="consulta_guardias.php">Guardias</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link px-4" href="guardias_realizadas.php">Guardias realizadas</a>
                        </li>
                    <?php endif; ?>
                </ul>
                <form action="../../server/logout.php" method="POST" class="d-flex ms-lg-auto mt-3 mt-lg-0">
                    <button type="submit" class="btn btn-danger">
                        log out <i class="fas fa-sign-out-alt ms-2"></i>
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        <h2 class="text-center fw-bold mb-4">Control de Guardias Realizadas</h2>
        <div class="row justify-content-center">
            <div class="col-md-10 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Filtros de Búsqueda</h5>
                        <form id="filtroGuardias">
                            <div class="mb-3">
                                <label for="fecha" class="form-label">Fecha</label>
                                <input type="date" class="form-control" id="fecha" name="fecha" value="<?php echo date('Y-m-d'); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="hora" class="form-label">Hora (opcional)</label>
                                <input type="time" class="form-control" id="hora" name="hora">
                            </div>
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-search me-2"></i>Buscar
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-10">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Guardias Realizadas</h5>
                        <div class="table-responsive">
                            <table class="table table-hover" id="tablaGuardias">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Hora Inicio</th>
                                        <th>Hora Fin</th>
                                        <th>Profesor Ausente</th>
                                        <th>Profesor Guardia</th>
                                        <th>Asignatura</th>
                                        <th>Grupo</th>
                                        <th>Aula</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Las guardias se cargarán aquí -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../vendor/bootstrap-5.0.2-dist/js/bootstrap.bundle.min.js"></script>
    <script src="./js/guardias_realizadas.js"></script>
</body>
</html> 