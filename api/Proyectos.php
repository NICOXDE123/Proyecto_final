<?php
ini_set('display_errors', 1); // Muestra errores en pantalla (desarrollo)
ini_set('display_startup_errors', 1);
error_reporting(E_ALL); // Reporta todos los errores

// Configura cabeceras para API REST
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

session_start(); // Inicia la sesión PHP
require_once 'config.php'; // Incluye la configuración de la base de datos

$rawMethod = $_SERVER['REQUEST_METHOD']; // Método HTTP original
$input = null;

// Si no es GET, intenta obtener el cuerpo de la petición como JSON
if ($rawMethod !== 'GET') {
    $input = json_decode(file_get_contents("php://input"), true);
}

// Permite simular métodos HTTP usando _method en POST
if ($rawMethod === 'POST' && isset($input['_method'])) {
    $method = strtoupper($input['_method']);
} else {
    $method = $rawMethod;
}

// Obtiene el ID del recurso desde la URL si existe
$id = null;
if (!empty($_SERVER['PATH_INFO']) && $_SERVER['PATH_INFO'] !== '/') {
    $partes = explode('/', trim($_SERVER['PATH_INFO'], '/'));
    if (is_numeric($partes[0])) {
        $id = intval($partes[0]);
    }
}

// Función para obtener datos de entrada (POST/JSON)
function getInput() {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') return null;
    return $_POST ?: json_decode(file_get_contents("php://input"), true);
}

// Protege los métodos de escritura si no hay usuario autenticado
if (in_array($method, ['POST', 'PATCH', 'DELETE']) && !isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(["error" => "No autorizado"]);
    exit;
}

// Maneja la petición según el método HTTP
switch ($method) {
    case 'GET':
        // Obtener uno o todos los proyectos
        if ($id) {
            $stmt = $conn->prepare("SELECT * FROM proyectos WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $res = $stmt->get_result();
            echo json_encode($res->fetch_assoc() ?: ["error" => "No encontrado"]);
            $stmt->close();
        } else {
            $res = $conn->query("SELECT * FROM proyectos ORDER BY created_at DESC");
            echo json_encode($res->fetch_all(MYSQLI_ASSOC));
        }
        break;

    case 'POST':
        // Crear un nuevo proyecto
        $data = getInput();
        if (empty($data['titulo']) || empty($data['descripcion'])) {
            http_response_code(400);
            echo json_encode(["error" => "Título y descripción requeridos"]);
            exit;
        }
        $stmt = $conn->prepare("INSERT INTO proyectos (titulo, descripcion, url_github, url_produccion, imagen) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $data['titulo'], $data['descripcion'], $data['url_github'], $data['url_produccion'], $data['imagen']);
        $stmt->execute();
        echo json_encode(["success" => true, "id" => $conn->insert_id]);
        $stmt->close();
        break;

    case 'PATCH':
        // Actualizar un proyecto existente
        $data = getInput();
        $campos = ['titulo', 'descripcion', 'url_github', 'url_produccion', 'imagen'];
        $updates = [];
        $params = [];
        $types = '';

        // Construye la consulta dinámica según los campos recibidos
        foreach ($campos as $campo) {
            if (isset($data[$campo])) {
                $updates[] = "$campo = ?";
                $params[] = $data[$campo];
                $types .= 's';
            }
        }

        if ($id && count($updates)) {
            $params[] = $id;
            $types .= 'i';
            $sql = "UPDATE proyectos SET " . implode(", ", $updates) . " WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            http_response_code(200); 
            echo json_encode(["success" => true]);
            $stmt->close();
        } else {
            http_response_code(400);
            echo json_encode(["error" => "ID o datos inválidos"]);
        }
        break;

    case 'DELETE':
        // Eliminar un proyecto por ID
        if ($id) {
            $stmt = $conn->prepare("DELETE FROM proyectos WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            http_response_code(200);
            echo json_encode(["success" => true]);
            $stmt->close();
        } else {
            http_response_code(400);
            echo json_encode(["error" => "ID inválido"]);
        }
        break;

    default:
        // Método no permitido
        http_response_code(405);
        echo json_encode(["error" => "Método no permitido"]);
        break;
}
?>
