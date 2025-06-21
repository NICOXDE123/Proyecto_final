<?php
// Mostrar errores (solo en desarrollo)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Cabecera JSON
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PATCH, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

session_start();

// Conexión a la base de datos
require_once 'config.php';

// Obtener el método de la petición
$method = $_SERVER['REQUEST_METHOD'];

// Obtener el ID desde la URL (si existe, por ejemplo /proyectos.php/5)
$id = null;
if (isset($_SERVER['PATH_INFO']) && $_SERVER['PATH_INFO'] !== '/') {
    $request = explode('/', trim($_SERVER['PATH_INFO'], '/'));
    if (isset($request[0]) && is_numeric($request[0])) {
        $id = intval($request[0]);
    }
}

// Función para obtener JSON del cuerpo
function getInput() {
    return json_decode(file_get_contents("php://input"), true);
}

// Protección: solo permitir POST, PATCH y DELETE si el usuario está logueado
if (in_array($method, ['POST', 'PATCH', 'DELETE']) && !isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(["error" => "No autorizado"]);
    exit;
}

// Manejo de métodos HTTP
switch ($method) {
    case 'GET':
        if ($id) {
            $stmt = $conn->prepare("SELECT * FROM proyectos WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            echo json_encode($result->fetch_assoc());
            $stmt->close();
        } else {
            $result = $conn->query("SELECT * FROM proyectos ORDER BY created_at DESC");
            $out = [];
            while ($row = $result->fetch_assoc()) {
                $out[] = $row;
            }
            echo json_encode($out);
        }
        break;

    case 'POST':
        $d = getInput();
        if (empty($d['titulo']) || empty($d['descripcion'])) {
            http_response_code(400);
            echo json_encode(["error" => "Título y descripción son obligatorios"]);
            exit;
        }
        $stmt = $conn->prepare("INSERT INTO proyectos (titulo, descripcion, url_github, url_produccion, imagen) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $d['titulo'], $d['descripcion'], $d['url_github'], $d['url_produccion'], $d['imagen']);
        $stmt->execute();
        echo json_encode(["success" => true, "id" => $conn->insert_id]);
        $stmt->close();
        break;

    case 'PATCH':
        $d = getInput();
        $permitidos = ['titulo', 'descripcion', 'url_github', 'url_produccion', 'imagen'];
        $sets = [];
        $params = [];
        $types = '';
        foreach ($permitidos as $campo) {
            if (isset($d[$campo])) {
                $sets[] = "$campo = ?";
                $params[] = $d[$campo];
                $types .= 's';
            }
        }
        if ($id && count($sets) > 0) {
            $params[] = $id;
            $types .= 'i';
            $sql = "UPDATE proyectos SET " . implode(", ", $sets) . " WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            echo json_encode(["success" => true]);
            $stmt->close();
        } else {
            http_response_code(400);
            echo json_encode(["error" => "Datos inválidos"]);
        }
        break;

    case 'DELETE':
        if ($id) {
            $stmt = $conn->prepare("DELETE FROM proyectos WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            echo json_encode(["success" => true]);
            $stmt->close();
        } else {
            http_response_code(400);
            echo json_encode(["error" => "ID inválido"]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Método no permitido"]);
        break;
}
?>
