<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Control de Asistencia</title>
    <link rel="icon" type="image/png" href="./img/favi.png">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS (CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- CSS -->
    <link rel="stylesheet" href="./css/styles.css">
</head>
<body class="bg-light d-flex justify-content-center align-items-center" style="min-height: 100vh;">

    <!-- Contenedor principal -->
    <div class="login-container p-4">
        <!-- Título -->
        <h1 class="login-title mb-4">login</h1>

        <!-- Formulario de login -->
        <form id="loginForm" method="POST">
            <div class="mb-3">
                <label for="dni" class="form-label text-white">DNI:</label>
                <input 
                    type="text" 
                    class="form-control rounded-input" 
                    id="dni" 
                    name="dni" 
                    placeholder="DNI"
                    required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label text-white">PASSWORD:</label>
                <div class="input-group rounded-input-group">
                    <input 
                        type="password" 
                        class="form-control rounded-input" 
                        id="password" 
                        name="password" 
                        placeholder="password"
                        required>
                    <span class="input-group-text bg-white border-start-0 rounded-input" style="cursor:pointer;" id="togglePassword">
                        <i class="fa fa-eye" id="icon-eye"></i>
                    </span>
                </div>
            </div>

            <div class="d-flex justify-content-end">
                <button 
                    type="submit" 
                    class="btn btn-custom">
                    sign in <i class="fa-solid fa-arrow-right-to-bracket ms-2"></i>
                </button>
            </div>
        </form>
    </div>

    <!-- Modal de Error -->
    <div class="modal fade error-modal" id="errorModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <i class="fas fa-exclamation-circle error-icon"></i>
                    <p class="error-message">Credenciales incorrectas. Por favor, inténtalo de nuevo.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Entendido</button>
                </div>
            </div>
        </div>
    </div>

    <!-- JS de Bootstrap (CDN) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Tu JavaScript personalizado -->
    <script src="./js/config.js"></script>
    <script src="./js/login.js"></script>
    
    <?php if (isset($_GET['error']) && $_GET['error'] == '1'): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
            errorModal.show();
        });
    </script>
    <?php endif; ?>
</body>
</html>
