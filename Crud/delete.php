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

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://teclab.uct.cl/~nicolas.huenchual/Proyecto_final/api/Proyectos.php/' . $id);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    header("Location: index.php");
    exit();
} else {
    echo "<div style='padding:20px;font-family:sans-serif;'>Error al eliminar el proyecto (código $httpCode)</div>";
}
?>
