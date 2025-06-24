<?php
// Mostrar errores (solo en desarrollo, coméntalo en producción)
ini_set('display_errors', 1); // Muestra errores en pantalla
ini_set('display_startup_errors', 1); // Muestra errores de inicio
error_reporting(E_ALL); // Reporta todos los errores

// Configuración de CORS (permite peticiones desde otros orígenes)
header("Access-Control-Allow-Methods: GET, POST, PATCH, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Parámetros de conexión a la base de datos
$host = "localhost"; // Host de la base de datos
$db = "nicolas_huenchual_db1"; // Nombre de la base de datos
$user = "nicolas_huenchual"; // Usuario de la base de datos
$pass = "nicolas_huenchual2025"; // Contraseña de la base de datos

// Conexión a MySQL usando mysqli
$conn = new mysqli($host, $user, $pass, $db);

// Verificar si la conexión fue exitosa
if ($conn->connect_error) {
    echo json_encode(["error" => "Conexión fallida: " . $conn->connect_error]);
    exit();
}

// Establece el conjunto de caracteres a UTF-8 para soportar caracteres especiales
$conn->set_charset("utf8");
?>
