<?php
include 'config.php';

// Manejar CORS preflight
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

// Obtener método HTTP
$method = $_SERVER['REQUEST_METHOD'];

// Obtener ID si existe
$id = isset($_GET['id']) ? intval($_GET['id']) : null;

// Obtener datos del cuerpo
function getInput() {
    return json_decode(file_get_contents('php://input'), true);
}

try {
    switch ($method) {
        case 'GET':
            if ($id) {
                $stmt = $conn->prepare("SELECT * FROM proyectos WHERE id = ?");
                $stmt->execute([$id]);
                $proyecto = $stmt->fetch(PDO::FETCH_ASSOC);
                echo json_encode($proyecto ?: ['error' => 'Proyecto no encontrado']);
            } else {
                $stmt = $conn->query("SELECT * FROM proyectos ORDER BY created_at DESC");
                echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
            }
            break;

        case 'POST':
            $data = getInput();
            $stmt = $conn->prepare("INSERT INTO proyectos (titulo, descripcion, url_github, url_produccion, imagen) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                $data['titulo'],
                $data['descripcion'],
                $data['url_github'],
                $data['url_produccion'],
                $data['imagen']
            ]);
            echo json_encode(['success' => true, 'id' => $conn->lastInsertId()]);
            break;

        case 'PATCH':
            $data = getInput();
            $fields = [];
            $values = [];
            
            foreach ($data as $key => $value) {
                $fields[] = "$key = ?";
                $values[] = $value;
            }
            $values[] = $id;
            
            $sql = "UPDATE proyectos SET ".implode(', ', $fields)." WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute($values);
            
            echo json_encode(['success' => $stmt->rowCount() > 0]);
            break;

        case 'DELETE':
            $stmt = $conn->prepare("DELETE FROM proyectos WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(['success' => $stmt->rowCount() > 0]);
            break;

        default:
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
    }
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>