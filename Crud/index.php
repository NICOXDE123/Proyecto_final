<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit();
}

$json = @file_get_contents('https://teclab.uct.cl/~nicolas.huenchual/Proyecto_final/api/Proyectos.php/');
$proyectos = json_decode($json, true);

if (!is_array($proyectos)) {
    $proyectos = [];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Proyectos</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>
<body class="bg-light">

  <div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2>Proyectos</h2>
      <a href="add.php" class="btn btn-primary">
        <i class="fa fa-plus"></i> Agregar
      </a>
    </div>

    <div class="row">
      <?php foreach ($proyectos as $p): ?>
        <div class="col-md-4 mb-4">
          <div class="card shadow-sm h-100">
            <img src="../uploads/<?= htmlspecialchars($p['imagen']) ?>" class="card-img-top" alt="Imagen del proyecto">
            <div class="card-body">
              <h5 class="card-title"><?= htmlspecialchars($p['titulo']) ?></h5>
              <p class="card-text"><?= htmlspecialchars($p['descripcion']) ?></p>
            </div>
            <div class="card-footer d-flex justify-content-between">
              <div>
                <a href="<?= htmlspecialchars($p['url_github']) ?>" target="_blank" class="btn btn-outline-dark btn-sm">
                  <i class="fab fa-github"></i> GitHub
                </a>
                <a href="<?= htmlspecialchars($p['url_produccion']) ?>" target="_blank" class="btn btn-outline-success btn-sm">
                  En producción
                </a>
              </div>
              <div>
                <a href="edit.php?id=<?= $p['id'] ?>" class="btn btn-warning btn-sm">Editar</a>
                <a href="delete.php?id=<?= $p['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Seguro que deseas eliminar este proyecto?')">Eliminar</a>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

</body>
</html>
