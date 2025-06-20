<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: ../login.php/");
    exit();
}

$id = intval($_GET['id']);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://teclab.uct.cl/~nicolas.huenchual/Proyecto_final/api/Proyectos.php/'.$id);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

header("Location: index.php");
exit;
?>