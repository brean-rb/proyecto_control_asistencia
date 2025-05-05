<?php
session_start();
if (!isset($_SESSION['autenticado']) || $_SESSION['autenticado'] !== true) {
    header('Location: ./login.php');
    exit();
}
// Añadir la variable de rol
$rolUsuario = $_SESSION['rol'] ?? 'profesor';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Consulta de Guardias - IES Joan Coromines</title>
    <link rel="icon" type="image/png" href="./img/favi.png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../vendor/bootstrap-5.0.2-dist/css/bootstrap.min.css">
    <!-- Añadir Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="./css/styles.css">
</head>
<body class="bg-light">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom mb-4">
        <div class="container">
            <!-- Logo / Nombre del centro -->
            <a class="navbar-brand text-dark fw-bold" href="index.php">
                IES Joan Coromines
            </a>

            <!-- Botón hamburguesa -->
            <button 
                class="navbar-toggler" 
                type="button" 
                data-bs-toggle="collapse" 
                data-bs-target="#navbarMain" 
                aria-controls="navbarMain" 
                aria-expanded="false" 
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Contenido del navbar -->
            <div class="collapse navbar-collapse" id="navbarMain">
                <?php if ($rolUsuario === 'admin'): ?>
                    <!-- Enlaces con espaciado para admin -->
                    <ul class="navbar-nav mx-auto">
                        <li class="nav-item">
                            <a class="nav-link active" href="consulta_guardias.php">Guardias</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="guardias_realizadas.php">Guardias realizadas</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link" href="#" id="adminDropdown" role="button">
                                Administración
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="adminDropdown">
                                <li><a class="dropdown-item" href="consulta_asistencia.php">Consulta Asistencia</a></li>
                                <li><a class="dropdown-item" href="registro_ausencia.php">Registro Ausencia</a></li>
                                <li><a class="dropdown-item" href="informe_ausencias.php">Informe Ausencias</a></li>
                            </ul>
                        </li>
                    </ul>
                <?php else: ?>
                    <!-- Enlaces centrados para profesor -->
                    <ul class="navbar-nav position-absolute start-50 translate-middle-x">
                        <li class="nav-item">
                            <a class="nav-link active px-4" href="consulta_guardias.php">Guardias</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link px-4" href="guardias_realizadas.php">Guardias realizadas</a>
                        </li>
                    </ul>
                <?php endif; ?>

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

    <div class="container py-4">
        <input type="hidden" id="docente_actual" value="<?php echo $_SESSION['dni']; ?>">
        
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

    <script src="../vendor/bootstrap-5.0.2-dist/js/bootstrap.bundle.min.js"></script>
    <script src="./js/consulta_guardias.js"></script>
</body>
</html>