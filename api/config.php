<?php
// Mostrar errores (solo en desarrollo, coméntalo en producción)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// CORS y cabeceras API
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: GET, POST, PATCH, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Parámetros de conexión
$host = "localhost";
$db = "nicolas_huenchual_db1";
$user = "nicolas_huenchual";
$pass = "nicolas_huenchual2025";

// Conexión a MySQL
$conn = new mysqli($host, $user, $pass, $db);

// Verificar conexión
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Conexión fallida: " . $conn->connect_error]);
    exit();
}
?>
