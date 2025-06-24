<?php
session_start(); // Inicia la sesión

// Mostrar errores (solo para desarrollo)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Incluye la configuración y conexión a la base de datos
include './api/config.php'; // Ruta correcta al archivo de conexión

// Verificar envío del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']); // Obtiene el usuario ingresado
    $password = md5(trim($_POST['password'])); // Obtiene y encripta la contraseña ingresada

    // Prepara y ejecuta la consulta para verificar las credenciales
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $resultado = $stmt->get_result();

    // Si existe el usuario, inicia sesión y redirige al panel
    if ($resultado->num_rows === 1) {
        $_SESSION['user'] = $username;
        header("Location: index.php");
        exit();
    } else {
        // Si las credenciales son incorrectas, muestra un error
        $error = "Credenciales incorrectas.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Iniciar Sesión</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap para estilos visuales -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center vh-100">
  <div class="card shadow p-4" style="width: 100%; max-width: 400px;">
    <h3 class="text-center text-primary mb-3">Iniciar Sesión</h3>

    <!-- Muestra mensaje de error si las credenciales son incorrectas -->
    <?php if (isset($error)): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <!-- Formulario de inicio de sesión -->
    <form method="post">
      <div class="mb-3">
        <label for="username" class="form-label">Usuario:</label>
        <input type="text" class="form-control" name="username" id="username" required>
      </div>

      <div class="mb-3">
        <label for="password" class="form-label">Contraseña:</label>
        <input type="password" class="form-control" name="password" id="password" required>
      </div>

      <div class="d-grid">
        <button type="submit" class="btn btn-primary">Ingresar</button>
      </div>
    </form>
  </div>
</body>
</html>