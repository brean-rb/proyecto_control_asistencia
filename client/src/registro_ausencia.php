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
            <!-- Logo -->
            <a class="navbar-brand text-dark fw-bold" href="#">
                IES Joan Coromines
            </a>

            <!-- Botón hamburguesa -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Menú -->
            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#">Guardias</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Guardias realizadas</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link active" href="#" id="adminDropdown" role="button">
                            Administración
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">Asistencias</a></li>
                            <li><a class="dropdown-item active" href="registro_ausencia.php">Registro Ausencia</a></li>
                            <li><a class="dropdown-item" href="#">Informe Ausencia</a></li>
                        </ul>
                    </li>
                </ul>
                <!-- Botón logout -->
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

    <!-- Contenido principal -->
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <h2 class="text-center fw-bold mb-4">Registro de Ausencia</h2>
                        
                        <form id="form-ausencia" action="../../server/procesar_ausencia.php" method="POST">
                            <!-- DNI Profesor -->
                            <div class="mb-4">
                                <label for="profesor" class="form-label fw-bold">DNI del profesor</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-dark text-light">
                                        <i class="fas fa-id-card"></i>
                                    </span>
                                    <input type="text" id="profesor" name="documento" class="form-control" required>
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
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="hora-inicio" class="form-label fw-bold">Hora inicio</label>
                                            <input type="time" id="hora-inicio" name="hora_inicio" class="form-control" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="hora-fin" class="form-label fw-bold">Hora fin</label>
                                            <input type="time" id="hora-fin" name="hora_fin" class="form-control" required>
                                        </div>
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

                            <!-- Botones -->
                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <a href="index.php" class="btn btn-secondary">
                                    <i class="fas fa-times me-2"></i>Cancelar
                                </a>
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-save me-2"></i>Guardar
                                </button>
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