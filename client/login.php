<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <h2>Iniciar sesión</h2>

    <form id="loginForm">
        <label for="document">Documento:</label>
        <input type="text" id="document" name="document" required>

        <label for="password">Contraseña:</label>
        <input type="password" id="password" name="password" required>

        <button type="submit">Entrar</button>
    </form>

    <p id="mensaje"></p>

    <script src="js/app.js"></script>
</body>
</html>
