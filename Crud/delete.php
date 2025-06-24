<?php
session_start(); // Inicia la sesión

// Verifica si el usuario está autenticado, si no lo está lo redirige al login
if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit();
}

// Verifica que se haya recibido un ID válido por GET, si no lo está lo redirige al panel principal
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = intval($_GET['id']); // Convierte el ID recibido a entero
$api_url = "https://teclab.uct.cl/~nicolas.huenchual/Proyecto_final/api/Proyectos.php/$id"; // URL de la API para eliminar el proyecto

$data = ['_method' => 'DELETE']; // Datos para simular el método DELETE en la API

$mensaje = null; // Variable para el mensaje de respuesta
$esExito = false; // Variable para saber si la operación fue exitosa

// Inicializa cURL para hacer la petición a la API
$ch = curl_init($api_url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true, // Retorna la respuesta como string
    CURLOPT_POST => true, // Usa POST para simular DELETE
    CURLOPT_HTTPHEADER => ['Content-Type: application/json'], // Indica que el contenido es JSON
    CURLOPT_POSTFIELDS => json_encode($data), // Envía los datos en formato JSON
    CURLOPT_COOKIE => 'PHPSESSID=' . session_id(), // Envía la cookie de sesión
    CURLOPT_TIMEOUT => 5 // Tiempo máximo de espera de la petición
]);

$response = curl_exec($ch); // Ejecuta la petición y guarda la respuesta
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); // Obtiene el código de respuesta HTTP
$curlError = curl_error($ch); // Obtiene el error de cURL si existe
curl_close($ch); // Cierra la sesión cURL

// Evaluar respuesta de la API
if (in_array($httpCode, [200, 204])) { // Si la respuesta es exitosa
    $mensaje = "✅ Proyecto eliminado correctamente. Redirigiendo al panel...";
    $esExito = true;
} else { // Si hubo algún error
    $mensaje = "❌ Error al eliminar el proyecto. Código HTTP: $httpCode";
    if ($curlError) { // Si hay error de cURL
        $mensaje = "❌ Error CURL: $curlError";
    } elseif (!empty($response)) { // Si la respuesta tiene contenido
        $json = json_decode($response, true);
        if (isset($json['error'])) { // Si la respuesta tiene un mensaje de error
            $mensaje = "❌ Error: " . htmlspecialchars($json['error']);
        }
    }
    $esExito = false;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title><?= $esExito ? 'Proyecto eliminado' : 'Error al eliminar' ?></title>
  <?php if ($esExito): ?>
    <!-- Si la eliminación fue exitosa, redirige al panel después de 2 segundos -->
    <meta http-equiv="refresh" content="2;url=index.php">
  <?php endif; ?>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Incluye Bootstrap para estilos -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center vh-100">
  <!-- Muestra el mensaje de éxito o error en una alerta -->
  <div class="alert <?= $esExito ? 'alert-success' : 'alert-danger' ?> text-center p-4 shadow" style="max-width: 500px;">
    <h4 class="mb-3"><?= $esExito ? 'Proyecto eliminado' : 'Error al eliminar' ?></h4>
    <p><?= $mensaje ?></p>
    <!-- Botón para volver al panel principal -->
    <a href="index.php" class="btn btn-<?= $esExito ? 'success' : 'secondary' ?> mt-3">Volver al panel</a>
  </div>
</body>
</html>