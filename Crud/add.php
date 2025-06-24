<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit();
}

$mensaje = null;
$esExito = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $img = null;

    // Subida de imagen
    if (!empty($_FILES['imagen']['name'])) {
        $nombreOriginal = basename($_FILES['imagen']['name']);
        $img = uniqid() . '_' . $nombreOriginal;
        $rutaDestino = "../uploads/$img";
        $tiposPermitidos = ['image/jpeg', 'image/png', 'image/webp'];

        if (in_array($_FILES['imagen']['type'], $tiposPermitidos)) {
            if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaDestino)) {
                $mensaje = "❌ Error al subir la imagen al servidor.";
            }
        } else {
            $mensaje = "⚠️ Formato de imagen no permitido.";
        }
    }

    if (!$mensaje) {
        $data = [
            'titulo' => trim($_POST['titulo']),
            'descripcion' => trim($_POST['descripcion']),
            'url_github' => trim($_POST['url_github']),
            'url_produccion' => trim($_POST['url_produccion']),
            'imagen' => $img
        ];

        $ch = curl_init('https://teclab.uct.cl/~nicolas.huenchual/Proyecto_final/api/Proyectos.php');
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_COOKIE => 'PHPSESSID=' . session_id(),
            CURLOPT_TIMEOUT => 10
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $resultado = json_decode($response, true);

        if (($httpCode === 200 || $httpCode === 201) && isset($resultado['success'])) {
            $mensaje = "✅ Proyecto guardado exitosamente. Redirigiendo...";
            $esExito = true;
            header("refresh:2;url=index.php");
        } else {
            $mensaje = "❌ Error al guardar el proyecto. Código HTTP: $httpCode";
            if (isset($resultado['error'])) {
                $mensaje .= "<br>Mensaje: " . htmlspecialchars($resultado['error']);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Agregar Proyecto</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-light">
  <div class="container py-5">
    <h2 class="mb-4 text-primary">Agregar Proyecto</h2>

    <a href="index.php" class="btn btn-outline-secondary mb-3">← Volver al panel</a>

    <?php if ($mensaje): ?>
      <div class="alert <?= $esExito ? 'alert-success' : 'alert-danger' ?> text-center">
        <?= $mensaje ?>
      </div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
      <div class="mb-3">
        <label for="titulo" class="form-label">Título:</label>
        <input type="text" class="form-control" id="titulo" name="titulo" required>
      </div>

      <div class="mb-3">
        <label for="descripcion" class="form-label">Descripción:</label>
        <textarea class="form-control" id="descripcion" name="descripcion" rows="3" maxlength="200" required></textarea>
      </div>

      <div class="mb-3">
        <label for="url_github" class="form-label">URL GitHub:</label>
        <input type="url" class="form-control" id="url_github" name="url_github">
      </div>

      <div class="mb-3">
        <label for="url_produccion" class="form-label">URL Producción:</label>
        <input type="url" class="form-control" id="url_produccion" name="url_produccion">
      </div>

      <div class="mb-3">
        <label for="imagen" class="form-label">Imagen:</label>
        <input type="file" class="form-control" id="imagen" name="imagen" required>
      </div>

      <div class="d-grid">
        <button type="submit" class="btn btn-success">Guardar Proyecto</button>
      </div>
    </form>
  </div>
</body>
</html>
