<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit();
}

$id = intval($_GET['id']);
$json = file_get_contents('https://teclab.uct.cl/~nicolas.huenchual/Proyecto_final/api/Proyectos.php/' . $id);
$p = json_decode($json, true);

// Validar si el proyecto existe
if (!$p || isset($p['error'])) {
    echo "<div class='alert alert-danger'>Proyecto no encontrado o error al cargar los datos.</div>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = [
        'titulo' => $_POST['titulo'],
        'descripcion' => $_POST['descripcion'],
        'url_github' => $_POST['url_github'],
        'url_produccion' => $_POST['url_produccion']
    ];

    if (!empty($_FILES['imagen']['name'])) {
        $nombreOriginal = basename($_FILES['imagen']['name']);
        $img = uniqid() . '_' . $nombreOriginal;
        $tipoPermitido = ['image/jpeg', 'image/png', 'image/webp'];
        if (in_array($_FILES['imagen']['type'], $tipoPermitido)) {
            move_uploaded_file($_FILES['imagen']['tmp_name'], "../uploads/$img");
            $data['imagen'] = $img;
        } else {
            die("Formato de imagen no permitido.");
        }
    }

    $ch = curl_init('https://teclab.uct.cl/~nicolas.huenchual/Proyecto_final/api/Proyectos.php/' . $id);
    curl_setopt_array($ch, [
        CURLOPT_CUSTOMREQUEST => 'PATCH',
        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POSTFIELDS => json_encode($data)
    ]);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200) {
        header("Location: index.php");
        exit;
    } else {
        $errorMsg = json_decode($response, true)['error'] ?? "Error al actualizar el proyecto (código $httpCode)";
        echo "<div class='alert alert-danger'>$errorMsg</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Editar proyecto</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
  <h2>Editar: <?= htmlspecialchars($p['titulo']) ?></h2>
  <form method="post" enctype="multipart/form-data">
    <div class="mb-3">
      <label for="titulo" class="form-label">Título:</label>
      <input type="text" class="form-control" id="titulo" name="titulo" value="<?= htmlspecialchars($p['titulo']) ?>" required>
    </div>

    <div class="mb-3">
      <label for="descripcion" class="form-label">Descripción:</label>
      <textarea class="form-control" id="descripcion" name="descripcion"><?= htmlspecialchars($p['descripcion']) ?></textarea>
    </div>

    <div class="mb-3">
      <label for="url_github" class="form-label">URL GitHub:</label>
      <input type="text" class="form-control" id="url_github" name="url_github" value="<?= htmlspecialchars($p['url_github']) ?>">
    </div>

    <div class="mb-3">
      <label for="url_produccion" class="form-label">URL Producción:</label>
      <input type="text" class="form-control" id="url_produccion" name="url_produccion" value="<?= htmlspecialchars($p['url_produccion']) ?>">
    </div>

    <div class="mb-3">
      <label for="imagen" class="form-label">Imagen:</label>
      <input type="file" class="form-control" id="imagen" name="imagen">
      <?php if (!empty($p['imagen'])): ?>
        <p>Imagen actual: <img src="../uploads/<?= htmlspecialchars($p['imagen']) ?>" width="100"></p>
      <?php endif; ?>
    </div>

    <button type="submit" class="btn btn-primary">Actualizar</button>
  </form>
</body>
</html>
