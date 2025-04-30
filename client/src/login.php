<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login</title>

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
                <input 
                    type="password" 
                    class="form-control rounded-input" 
                    id="password" 
                    name="password" 
                    placeholder="password"
                    required>
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

    <!-- Bootstrap JS (Local) -->
    <script src="../vendor/bootstrap-5.0.2-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
