<?php
session_start();
if (isset($_SESSION['user'])) {
    header("Location: Crud/index.php"); // o cambia según tu ruta real del panel
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Portada Proyecto Final</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex justify-content-center align-items-center vh-100">

  <div class="card shadow p-5 text-center" style="min-width: 350px;">
    <h1 class="mb-4 text-primary">¡Bienvenido a tu Portafolio!</h1>
    <p class="mb-4">Este es el proyecto final CRUD de <b>Nicolás Huenchual</b>.</p>
    <a href="login.php" class="btn btn-primary btn-lg">Ingresar</a>
  </div>

</body>
</html>
