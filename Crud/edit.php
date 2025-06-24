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
$api_url = 'https://teclab.uct.cl/~nicolas.huenchual/Proyecto_final/api/Proyectos.php/' .$id;
$json = @file_get_contents($api_url);
$p = json_decode($json, true);

if (!$p || isset($p['error'])) {
    echo "<div class='alert alert-danger'>Proyecto no encontrado o error al cargar los datos.</div>";
    exit();
}

$mensaje = null;
$esExito = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'titulo' => trim($_POST['titulo']),
        'descripcion' => trim($_POST['descripcion']),
        'url_github' => trim($_POST['url_github']),
        'url_produccion' => trim($_POST['url_produccion']),
    ];

    if (!empty($_FILES['imagen']['name'])) {
        $permitidos = ['image/jpeg', 'image/png', 'image/webp'];
        $tipo = mime_content_type($_FILES['imagen']['tmp_name']);
        if (in_array($tipo, $permitidos)) {
            $nombreOriginal = basename($_FILES['imagen']['name']);
            $nombreUnico = uniqid() . '_' . $nombreOriginal;
            if (move_uploaded_file($_FILES['imagen']['tmp_name'], "../uploads/$nombreUnico")) {
                $data['imagen'] = $nombreUnico;
            } else {
                $mensaje = "Error al subir la nueva imagen.";
            }
        } else {
            $mensaje = "⚠️ Formato de imagen no permitido.";
        }
    }

    if (!$mensaje) {
        $data['_method'] = 'PATCH';

        $ch = curl_init($api_url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_COOKIE => 'PHPSESSID=' . session_id(),
            CURLOPT_TIMEOUT => 10
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        $result = json_decode($response, true);

        if (($httpCode === 200 || $httpCode === 204) && isset($result['success'])) {
            $mensaje = "✅ Proyecto actualizado correctamente. Redirigiendo...";
            $esExito = true;
            header("refresh:2;url=index.php"); // redirige tras 2 segundos
        } else {
            $mensaje = $result['error'] ?? "Error al actualizar el proyecto (código $httpCode)";
            if ($curlError) {
                $mensaje .= "<br>Error CURL: $curlError";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Editar Proyecto</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-light">
  <div class="container py-5">
    <h2 class="mb-4 text-primary">Editar Proyecto</h2>

    <a href="index.php" class="btn btn-outline-secondary mb-3">
      <i class="fa fa-arrow-left"></i> Regresar
    </a>

    <!-- ✅ MENSAJE DE ESTADO -->
    <?php if ($mensaje): ?>
      <div class="alert <?= $esExito ? 'alert-success' : 'alert-danger' ?>">
        <?= $mensaje ?>
      </div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
      <div class="mb-3">
        <label for="titulo" class="form-label">Título:</label>
        <input type="text" class="form-control" id="titulo" name="titulo" value="<?= htmlspecialchars($p['titulo']) ?>" required>
      </div>

      <div class="mb-3">
        <label for="descripcion" class="form-label">Descripción:</label>
        <textarea class="form-control" id="descripcion" name="descripcion" required><?= htmlspecialchars($p['descripcion']) ?></textarea>
      </div>

      <div class="mb-3">
        <label for="url_github" class="form-label">URL GitHub:</label>
        <input type="url" class="form-control" id="url_github" name="url_github" value="<?= htmlspecialchars($p['url_github']) ?>">
      </div>

      <div class="mb-3">
        <label for="url_produccion" class="form-label">URL Producción:</label>
        <input type="url" class="form-control" id="url_produccion" name="url_produccion" value="<?= htmlspecialchars($p['url_produccion']) ?>">
      </div>

      <div class="mb-3">
        <label for="imagen" class="form-label">Imagen:</label>
        <input type="file" class="form-control" id="imagen" name="imagen">
        <?php if (!empty($p['imagen'])): ?>
          <p class="mt-2">Imagen actual:<br>
            <img src="../uploads/<?= htmlspecialchars($p['imagen']) ?>" width="150" class="img-thumbnail">
          </p>
        <?php endif; ?>
      </div>

      <div class="d-grid">
        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
      </div>
    </form>
  </div>
</body>
</html>
