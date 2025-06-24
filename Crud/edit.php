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
$api_url = 'https://teclab.uct.cl/~nicolas.huenchual/Proyecto_final/api/Proyectos.php/' .$id; // URL de la API para obtener el proyecto
$json = @file_get_contents($api_url); // Obtiene los datos del proyecto desde la API
$p = json_decode($json, true); // Decodifica el JSON recibido

// Si no se encuentra el proyecto o hay error, muestra mensaje y termina
if (!$p || isset($p['error'])) {
    echo "<div class='alert alert-danger'>Proyecto no encontrado o error al cargar los datos.</div>";
    exit();
}

$mensaje = null; // Variable para el mensaje de estado
$esExito = false; // Variable para saber si la operación fue exitosa

// Si el formulario fue enviado por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Prepara los datos recibidos del formulario
    $data = [
        'titulo' => trim($_POST['titulo']),
        'descripcion' => trim($_POST['descripcion']),
        'url_github' => trim($_POST['url_github']),
        'url_produccion' => trim($_POST['url_produccion']),
    ];

    // Si se subió una nueva imagen
    if (!empty($_FILES['imagen']['name'])) {
        $permitidos = ['image/jpeg', 'image/png', 'image/webp']; // Tipos de imagen permitidos
        $tipo = mime_content_type($_FILES['imagen']['tmp_name']); // Obtiene el tipo MIME del archivo
        if (in_array($tipo, $permitidos)) { // Verifica si el tipo es permitido
            $nombreOriginal = basename($_FILES['imagen']['name']); // Nombre original del archivo
            $nombreUnico = uniqid() . '_' . $nombreOriginal; // Genera un nombre único para evitar conflictos
            // Mueve la imagen subida a la carpeta de uploads
            if (move_uploaded_file($_FILES['imagen']['tmp_name'], "../uploads/$nombreUnico")) {
                $data['imagen'] = $nombreUnico; // Agrega el nombre de la imagen a los datos
            } else {
                $mensaje = "Error al subir la nueva imagen."; // Error al mover el archivo
            }
        } else {
            $mensaje = "⚠️ Formato de imagen no permitido."; // Tipo de imagen no permitido
        }
    }

    // Si no hubo errores con la imagen
    if (!$mensaje) {
        $data['_method'] = 'PATCH'; // Indica que es una actualización (PATCH)

        // Inicializa cURL para enviar los datos a la API
        $ch = curl_init($api_url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true, // Retorna la respuesta como string
            CURLOPT_POST => true, // Usa POST para simular PATCH
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'], // Indica que el contenido es JSON
            CURLOPT_POSTFIELDS => json_encode($data), // Envía los datos en formato JSON
            CURLOPT_COOKIE => 'PHPSESSID=' . session_id(), // Envía la cookie de sesión
            CURLOPT_TIMEOUT => 10 // Tiempo máximo de espera de la petición
        ]);

        $response = curl_exec($ch); // Ejecuta la petición y guarda la respuesta
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); // Obtiene el código de respuesta HTTP
        $curlError = curl_error($ch); // Obtiene el error de cURL si existe
        curl_close($ch); // Cierra la sesión cURL

        $result = json_decode($response, true); // Decodifica la respuesta JSON

        // Si la actualización fue exitosa
        if (($httpCode === 200 || $httpCode === 204) && isset($result['success'])) {
            $mensaje = "✅ Proyecto actualizado correctamente. Redirigiendo...";
            $esExito = true;
            header("refresh:2;url=index.php"); // Redirige tras 2 segundos
        } else {
            // Si hubo error, muestra el mensaje correspondiente
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
  <!-- Título de la página -->
  <title>Editar Proyecto</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Inclusión de Bootstrap para estilos visuales -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-light">
  <div class="container py-5">
    <!-- Título principal de la página -->
    <h2 class="mb-4 text-primary">Editar Proyecto</h2>

    <!-- Botón para regresar al panel principal -->
    <a href="index.php" class="btn btn-outline-secondary mb-3">
      <i class="fa fa-arrow-left"></i> Regresar
    </a>

    <!-- Mensaje de estado: éxito o error al actualizar -->
    <?php if ($mensaje): ?>
      <div class="alert <?= $esExito ? 'alert-success' : 'alert-danger' ?>">
        <?= $mensaje ?>
      </div>
    <?php endif; ?>

    <!-- Formulario para editar los datos del proyecto -->
    <form method="post" enctype="multipart/form-data">
      <!-- Campo para el título del proyecto -->
      <div class="mb-3">
        <label for="titulo" class="form-label">Título:</label>
        <input type="text" class="form-control" id="titulo" name="titulo" value="<?= htmlspecialchars($p['titulo']) ?>" required>
      </div>

      <!-- Campo para la descripción del proyecto -->
      <div class="mb-3">
        <label for="descripcion" class="form-label">Descripción:</label>
        <textarea class="form-control" id="descripcion" name="descripcion" required><?= htmlspecialchars($p['descripcion']) ?></textarea>
      </div>

      <!-- Campo para la URL de GitHub -->
      <div class="mb-3">
        <label for="url_github" class="form-label">URL GitHub:</label>
        <input type="url" class="form-control" id="url_github" name="url_github" value="<?= htmlspecialchars($p['url_github']) ?>">
      </div>

      <!-- Campo para la URL de producción -->
      <div class="mb-3">
        <label for="url_produccion" class="form-label">URL Producción:</label>
        <input type="url" class="form-control" id="url_produccion" name="url_produccion" value="<?= htmlspecialchars($p['url_produccion']) ?>">
      </div>

      <!-- Campo para subir una nueva imagen -->
      <div class="mb-3">
        <label for="imagen" class="form-label">Imagen:</label>
        <input type="file" class="form-control" id="imagen" name="imagen">
        <!-- Muestra la imagen actual si existe -->
        <?php if (!empty($p['imagen'])): ?>
          <p class="mt-2">Imagen actual:<br>
            <img src="../uploads/<?= htmlspecialchars($p['imagen']) ?>" width="150" class="img-thumbnail">
          </p>
        <?php endif; ?>
      </div>

      <!-- Botón para guardar los cambios -->
      <div class="d-grid">
        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
      </div>
    </form>
  </div>
</body>
</html>
