<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include 'api/config.php'; // Asegúrate de que este archivo contiene la conexión a la base de datos

$error = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $username = $_POST['username'];
  $password = md5($_POST['password']);

  $sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
  $result = $conn->query($sql);

  if ($result->num_rows === 1) {
    $_SESSION['user'] = $username;
    header("Location: index.php");
    exit;
  } else {
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
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style-custom.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light d-flex justify-content-center align-items-center vh-100">

<div class="card shadow p-4" style="min-width: 350px;">
  <h3 class="text-center mb-4 text-primary"><i class="fa fa-user-circle"></i> Iniciar Sesión</h3>

  <?php if ($error): ?>   
    <div class="alert alert-danger"><?= $error ?></div>
  <?php endif; ?>

  <form method="post">
    <div class="mb-3">
      <label for="username" class="form-label">Usuario</label>
      <input type="text" class="form-control" id="username" name="username" required>
    </div>

    <div class="mb-3">
      <label for="password" class="form-label">Contraseña</label>
      <input type="password" class="form-control" id="password" name="password" required>
    </div>

    <div class="d-grid">
      <button type="submit" class="btn btn-primary"><i class="fa fa-sign-in-alt"></i> Ingresar</button>
    </div>
  </form>
</div>

</body>
</html>
