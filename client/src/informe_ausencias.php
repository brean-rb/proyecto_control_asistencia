<?php
session_start();
if (!isset($_SESSION['autenticado']) || $_SESSION['autenticado'] !== true || $_SESSION['rol'] !== 'admin') {
    header('Location: ./login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Informes de Ausencias - IES Joan Coromines</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../vendor/bootstrap-5.0.2-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="./css/styles.css">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom mb-4">
        <div class="container">
            <a class="navbar-brand text-dark fw-bold" href="index.php">
                IES Joan Coromines
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="consulta_guardias.php">Guardias</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="guardias_realizadas.php">Guardias realizadas</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link active" href="#" id="adminDropdown" role="button">
                            Administración
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="consulta_asistencia.php">Consulta Asistencia</a></li>
                            <li><a class="dropdown-item" href="registro_ausencia.php">Registro Ausencia</a></li>
                            <li><a class="dropdown-item active" href="informe_ausencias.php">Informe Ausencias</a></li>
                        </ul>
                    </li>
                </ul>
                <div class="ms-auto">
                    <form action="../../server/logout.php" method="POST">
                        <button type="submit" class="btn btn-danger">
                            log out <i class="fas fa-sign-out-alt ms-2"></i>
                        </button>
                    </form>
                </div>
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
                                    <label for="documento" class="form-label fw-bold">DNI Docente</label>
                                    <input type="text" class="form-control" id="documento" name="documento">
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

    <script src="../vendor/bootstrap-5.0.2-dist/js/bootstrap.bundle.min.js"></script>
    <script src="./js/informe_ausencias.js"></script>
</body>
</html>