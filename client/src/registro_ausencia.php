<?php
session_start();
if (!isset($_SESSION['autenticado']) || $_SESSION['autenticado'] !== true || $_SESSION['rol'] !== 'admin') {
    header('Location: ./login.php');
    exit();
}
$rolUsuario = $_SESSION['rol'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Ausencia - IES Joan Coromines</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../vendor/bootstrap-5.0.2-dist/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- CSS -->
    <link rel="stylesheet" href="./css/styles.css">
</head>
<body class="bg-light">
    <!-- Navbar -->
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
                            <li><a class="dropdown-item active" href="registro_ausencia.php">Registro Ausencia</a></li>
                            <li><a class="dropdown-item" href="informe_ausencias.php">Informe Ausencias</a></li>
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

    <!-- Modal de éxito -->
    <div class="modal fade" id="exitoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">¡Éxito!</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>La ausencia se ha registrado correctamente.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" data-bs-dismiss="modal">Aceptar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenido principal -->
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <h2 class="text-center fw-bold mb-4">Registro de Ausencia</h2>
                        
                        <form id="form-ausencia" action="../../server/procesar_ausencia.php" method="POST">
                            <!-- Reemplazar la sección de búsqueda de profesor por esto -->
                            <div class="mb-4">
                                <label for="documento" class="form-label fw-bold">DNI del profesor</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-dark text-light">
                                        <i class="fas fa-id-card"></i>
                                    </span>
                                    <input type="text" id="documento" name="documento" class="form-control" placeholder="Introduce el DNI..." required>
                                </div>
                            </div>

                            <!-- Tipo de ausencia -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">Tipo de ausencia</label>
                                <div class="bg-light p-3 rounded border">
                                    <div class="form-check mb-2">
                                        <input type="radio" name="tipo" id="mismo-dia" value="dia" checked class="form-check-input">
                                        <label for="mismo-dia" class="form-check-label">
                                            <i class="fas fa-clock me-2"></i>El mismo día
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input type="radio" name="tipo" id="periodo" value="periodo" class="form-check-input">
                                        <label for="periodo" class="form-check-label">
                                            <i class="fas fa-calendar-alt me-2"></i>Periodo de tiempo
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Para mismo día -->
                            <div id="campo-mismo-dia" class="mb-4">
                                <div class="bg-light p-3 rounded border">
                                    <div class="mb-3">
                                        <label for="fecha" class="form-label fw-bold">Fecha</label>
                                        <input type="date" id="fecha" name="fecha" class="form-control" required>
                                    </div>
                                    <div id="horario-profesor" class="mb-3">
                                        <!-- Aquí se cargarán dinámicamente las horas del profesor -->
                                    </div>
                                </div>
                            </div>

                            <!-- Para periodo -->
                            <div id="campo-periodo" class="mb-4" style="display:none;">
                                <div class="bg-light p-3 rounded border">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="fecha-inicio" class="form-label fw-bold">Fecha inicio</label>
                                            <input type="date" id="fecha-inicio" name="fecha_inicio" class="form-control">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="fecha-fin" class="form-label fw-bold">Fecha fin</label>
                                            <input type="date" id="fecha-fin" name="fecha_fin" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Campo motivo -->
                            <div class="mb-4">
                                <div class="bg-light p-3 rounded border">
                                    <label for="motivo" class="form-label fw-bold">Motivo de la ausencia</label>
                                    <textarea id="motivo" name="motivo" class="form-control" rows="3" required></textarea>
                                </div>
                            </div>

                            <!-- Después del campo motivo y antes de los botones -->
                            <div class="mb-4">
                                <div class="bg-light p-3 rounded border">
                                    <div class="form-check">
                                        <input type="checkbox" id="justificada" name="justificada" class="form-check-input" value="1">
                                        <label for="justificada" class="form-label fw-bold">
                                            <i class="fas fa-check-circle me-2"></i>Ausencia justificada
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Reemplazar la sección de los botones -->
                            <div class="d-flex justify-content-center gap-3 mt-4">
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-save me-2"></i>guardar
                                </button>
                                <a href="index.php" class="btn btn-secondary">
                                    <i class="fas fa-times me-2"></i>Cancelar
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JS de Bootstrap -->
    <script src="../vendor/bootstrap-5.0.2-dist/js/bootstrap.bundle.min.js"></script>
    <script src="./js/registro_ausencia.js"></script>
</body>
</html>