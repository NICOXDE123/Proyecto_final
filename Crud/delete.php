<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = intval($_GET['id']);
$api_url = "https://teclab.uct.cl/~nicolas.huenchual/Proyecto_final/api/Proyectos.php/$id";

$data = ['_method' => 'DELETE'];

$ch = curl_init($api_url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
    CURLOPT_POSTFIELDS => json_encode($data),
    CURLOPT_COOKIE => 'PHPSESSID=' . session_id(),
    CURLOPT_TIMEOUT => 5
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

// Si fue exitoso
if (in_array($httpCode, [200, 204])) {
    $mensaje = "✅ Proyecto eliminado correctamente. Redirigiendo al panel...";
    $esExito = true;
} else {
    // Si falló
    $mensaje = "❌ Error al eliminar el proyecto. Código HTTP: $httpCode";
    if ($curlError) {
        $mensaje = "❌ Error CURL: $curlError";
    } elseif (!empty($response)) {
        $json = json_decode($response, true);
        if (isset($json['error'])) {
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
  <meta http-equiv="refresh" content="<?= $esExito ? '2;url=index.php' : '' ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center vh-100">
  <div class="alert <?= $esExito ? 'alert-success' : 'alert-danger' ?> text-center p-4 shadow" style="max-width: 500px;">
    <h4 class="mb-3"><?= $esExito ? 'Proyecto eliminado' : 'Error al eliminar' ?></h4>
    <p><?= $mensaje ?></p>
    <a href="index.php" class="btn btn-<?= $esExito ? 'success' : 'secondary' ?> mt-3">Volver al panel</a>
  </div>
</body>
</html>