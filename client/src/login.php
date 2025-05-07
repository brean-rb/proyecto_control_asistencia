<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="icon" type="image/png" href="./img/favi.png">
    <!-- Bootstrap CSS (Local) -->
    <link 
        rel="stylesheet"
        href="../vendor/bootstrap-5.0.2-dist/css/bootstrap.min.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Tu CSS personalizado -->
    <link rel="stylesheet" href="css/styles.css">
</head>
<body class="bg-light d-flex justify-content-center align-items-center" style="min-height: 100vh;">

    <!-- Contenedor principal -->
    <div class="login-container p-4">
        <!-- Título -->
        <h1 class="login-title mb-4">login</h1>

        <!-- Formulario de login -->
        <form action="../../server/login.php" method="POST">
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

    <!-- Bootstrap JS (Local) -->
    <script src="../vendor/bootstrap-5.0.2-dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.getElementById('togglePassword').addEventListener('click', function () {
            const passwordInput = document.getElementById('password');
            const icon = document.getElementById('icon-eye');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    </script>
    
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
