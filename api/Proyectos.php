<?php
// Mostrar errores (solo en desarrollo)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Cabecera JSON
header('Content-Type: application/json');

// Conexión a la base de datos
include 'api/config.php';

// Obtener el método de la petición
$method = $_SERVER['REQUEST_METHOD'];

// Obtener el ID desde la URL si existe
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

// Manejo de métodos HTTP
switch ($method) {
    case 'GET':
        if ($id) {
            $res = $conn->query("SELECT * FROM proyectos WHERE id=$id");
            echo json_encode($res->fetch_assoc());
        } else {
            $res = $conn->query("SELECT * FROM proyectos ORDER BY created_at DESC");
            $out = [];
            while ($row = $res->fetch_assoc()) {
                $out[] = $row;
            }
            echo json_encode($out);
        }
        break;

    case 'POST':
        $d = getInput();
        $stmt = $conn->prepare("INSERT INTO proyectos (titulo, descripcion, url_github, url_produccion, imagen) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $d['titulo'], $d['descripcion'], $d['url_github'], $d['url_produccion'], $d['imagen']);
        $stmt->execute();
        echo json_encode(["success" => true, "id" => $stmt->insert_id]);
        break;

    case 'PATCH':
        $d = getInput();
        // Solo permitir ciertos campos
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
            $sql = "UPDATE proyectos SET " . implode(", ", $sets) . " WHERE id = ?";
            $types .= 'i';
            $params[] = $id;
            $stmt = $conn->prepare($sql);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            echo json_encode(["success" => true]);
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