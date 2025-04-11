<?php
session_start();
if (!isset($_SESSION['documento'])) {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Módulo Principal - Gestión Guardias</title>
    <link rel="stylesheet" href="../vendor/bootstrap-5.0.2-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/styles.css">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">Gestión Guardias</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <button id="logout-btn" class="btn btn-danger">Cerrar Sesión</button>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <h1 class="text-center mb-4">Bienvenido, <?php echo htmlspecialchars($_SESSION['documento']); ?></h1>
        <div class="row">
            <div class="col-md-6 mb-3">
                <button id="start-work-btn" class="btn btn-success w-100">Iniciar Jornada</button>
            </div>
            <div class="col-md-6 mb-3">
                <button id="end-work-btn" class="btn btn-danger w-100">Finalizar Jornada</button>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 mb-3">
                <a href="consulta_guardias.php" class="btn btn-primary w-100">Consulta de Guardias</a>
            </div>
            <div class="col-md-4 mb-3">
                <a href="registro_ausencias.php" class="btn btn-primary w-100">Registro de Ausencias</a>
            </div>
            <div class="col-md-4 mb-3">
                <a href="informes.php" class="btn btn-primary w-100">Informes</a>
            </div>
        </div>
    </div>

    <script src="../vendor/bootstrap-5.0.2-dist/js/bootstrap.bundle.min.js"></script>
    <script src="./js/index.js"></script>
</body>
</html>