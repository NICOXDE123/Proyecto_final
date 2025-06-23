<?php

session_start();
if (!isset($_SESSION['user'])) {
    header("Location: ./login.php");
    exit();
}

$ch = curl_init('https://teclab.uct.cl/~nicolas.huenchual/Proyecto_final/api/Proyectos.php/');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 5 // ⏱ máximo 5 segundos de espera
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$proyectos = [];

if ($httpCode === 200 && $response !== false) {
    $data = json_decode($response, true);
    if (is_array($data)) {
        $proyectos = $data;
    }
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Panel de Proyectos</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <!-- Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
</head>
<body class="bg-light">

  <div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="text-primary"><i class="fa fa-folder-open"></i> Mis Proyectos</h2>
      <div>
        <a href="add.php" class="btn btn-success me-2">
          <i class="fa fa-plus"></i> Agregar Proyecto
        </a>
        <a href="../login.php" class="btn btn-secondary">
          <i class="fa fa-sign-out-alt"></i> Cerrar sesión
        </a>
      </div>
    </div>

    <div class="row">
      <?php if (empty($proyectos)): ?>
        <div class="col-12">
          <div class="alert alert-info text-center">No hay proyectos cargados aún.</div>
        </div>
      <?php else: ?>
        <?php foreach ($proyectos as $p): ?>
          <div class="col-md-4 mb-4">
            <div class="card shadow-sm h-100">
              <?php if (!empty($p['imagen'])): ?>
                <img src="../uploads/<?= htmlspecialchars($p['imagen']) ?>" class="card-img-top" alt="Imagen del proyecto">
              <?php endif; ?>
              <div class="card-body">
                <h5 class="card-title"><?= htmlspecialchars($p['titulo']) ?></h5>
                <p class="card-text"><?= htmlspecialchars($p['descripcion']) ?></p>
              </div>
              <div class="card-footer d-flex justify-content-between">
                <div>
                  <?php if (!empty($p['url_github'])): ?>
                    <a href="<?= htmlspecialchars($p['url_github']) ?>" target="_blank" class="btn btn-outline-dark btn-sm">
                      <i class="fab fa-github"></i>
                    </a>
                  <?php endif; ?>
                  <?php if (!empty($p['url_produccion'])): ?>
                    <a href="<?= htmlspecialchars($p['url_produccion']) ?>" target="_blank" class="btn btn-outline-success btn-sm">
                      Ver sitio
                    </a>
                  <?php endif; ?>
                </div>
                <div>
                  <a href="edit.php?id=<?= $p['id'] ?>" class="btn btn-warning btn-sm">
                    <i class="fa fa-edit"></i>
                  </a>
                  <a href="delete.php?id=<?= $p['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Seguro que deseas eliminar este proyecto?')">
                    <i class="fa fa-trash"></i>
                  </a>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>

</body>
</html>
