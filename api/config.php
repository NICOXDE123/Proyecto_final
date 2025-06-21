<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: GET, POST, PATCH, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

$host = "localhost";
$db = "nicolas_huenchual_db1";
$user = "nicolas_huenchual";
$pass = "nicolas_huenchual2025";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die(json_encode(["error" => "Conexión fallida: " . $conn->connect_error]));
}
?>