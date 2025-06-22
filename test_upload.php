<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Test Upload</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-5">
  <div class="container">
    <h2 class="mb-4">🧪 Probar subida de imagen</h2>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $uploadsDir = __DIR__ . 'uploads';
        $nombreOriginal = basename($_FILES['imagen']['name']);
        $rutaDestino = $uploadsDir . uniqid() . '_' . $nombreOriginal;

        if (!is_dir($uploadsDir)) {
            echo '<div class="alert alert-danger">❌ La carpeta <code>uploads/</code> no existe.</div>';
        } elseif (!is_writable($uploadsDir)) {
            echo '<div class="alert alert-danger">❌ La carpeta <code>uploads/</code> no tiene permisos de escritura.</div>';
        } elseif (move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaDestino)) {
            echo '<div class="alert alert-success">✅ Imagen subida correctamente: <code>' . basename($rutaDestino) . '</code></div>';
        } else {
            echo '<div class="alert alert-danger">⚠️ Error al subir la imagen al servidor.</div>';
        }
    }
    ?>

    <form method="post" enctype="multipart/form-data">
      <div class="mb-3">
        <label for="imagen" class="form-label">Selecciona una imagen:</label>
        <input type="file" name="imagen" id="imagen" class="form-control" required>
      </div>
      <button type="submit" class="btn btn-primary">Subir Imagen</button>
    </form>
  </div>
</body>
</html>
