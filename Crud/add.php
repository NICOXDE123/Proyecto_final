<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $img = null;

    if (!empty($_FILES['imagen']['name'])) {
        $nombreOriginal = basename($_FILES['imagen']['name']);
        $img = uniqid() . '_' . $nombreOriginal;
        $rutaDestino = "../uploads/$img";

        // Validación mínima del tipo de archivo
        $tipoPermitido = ['image/jpeg', 'image/png', 'image/webp'];
        if (in_array($_FILES['imagen']['type'], $tipoPermitido)) {
            move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaDestino);
        } else {
            die("Formato de imagen no permitido.");
        }
    }

    $data = [
        'titulo' => $_POST['titulo'],
        'descripcion' => $_POST['descripcion'],
        'url_github' => $_POST['url_github'],
        'url_produccion' => $_POST['url_produccion'],
        'imagen' => $img
    ];

    // Asegúrate que el nombre del archivo de la API es correcto (mayúsculas/minúsculas)
    $ch = curl_init('https://teclab.uct.cl/~nicolas.huenchual/Proyecto_final/api/Proyectos.php');
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        CURLOPT_POSTFIELDS => json_encode($data)
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($response === false) {
        echo "<div class='alert alert-danger'>Error CURL: " . curl_error($ch) . "</div>";
    }
    curl_close($ch);

    if ($httpCode === 200 || $httpCode === 201) {
        header("Location: index.php");
        exit;
    } else {
        $errorMsg = json_decode($response, true)['error'] ?? "Error al guardar el proyecto (código $httpCode)";
        echo "<div class='alert alert-danger'>$errorMsg</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Proyecto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-light">
    <div class="container py-5">
        <h2 class="mb-4">Agregar Proyecto</h2>
        <form method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="titulo" class="form-label">Título:</label>
                <input type="text" class="form-control" id="titulo" name="titulo" required>
            </div>
            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción:</label>
                <textarea class="form-control" id="descripcion" name="descripcion" maxlength="200" required></textarea>
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
            <button type="submit" class="btn btn-primary">Guardar</button>
        </form>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</body>
</html>
