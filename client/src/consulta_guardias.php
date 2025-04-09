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
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <h2 class="text-center fw-bold mb-4">Consulta de Guardias</h2>
                        
                        <form id="form-guardias" class="mb-4">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="fecha" class="form-label fw-bold">Fecha</label>
                                    <input type="date" class="form-control" id="fecha" name="fecha" required>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-search me-2"></i>Consultar
                                    </button>
                                </div>
                            </div>
                        </form>

                        <div class="table-responsive mt-4">
                            <table class="table table-hover" id="tabla-guardias">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Docente Ausente</th>
                                        <th>Hora Inicio</th>
                                        <th>Hora Fin</th>
                                        <th>Grupo</th>
                                        <th>Aula</th>
                                        <th>Acción</th>
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
    <script src="./js/consulta_guardias.js"></script>
</body>
</html>