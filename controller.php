<?php
require "todo.php";
require "DB.php";

function return_response($status, $statusMessage, $data) {
    header("HTTP/1.1 $status $statusMessage");
    header("Content-Type: application/json; charset=UTF-8");
    echo json_encode($data);
}

$bodyRequest = file_get_contents("php://input");

switch ($_SERVER['REQUEST_METHOD']) {
    case 'POST':
        $db = new DB();
        $new_todo = new Todo;
        $new_todo->jsonConstruct($bodyRequest);
        $new_todo->DB_insert($db->connection);
        $todo_list = Todo::DB_selectAll($db->connection);
        return_response(200, "OK", $todo_list);
        break;

    case 'DELETE':
        if (isset($_GET['id']) && is_numeric($_GET['id'])) {
            $db = new DB();
            $item_id = (int)$_GET['id'];

            if (Todo::DB_delete($db->connection, $item_id)) {
                $todo_list = Todo::DB_selectAll($db->connection);
                return_response(200, "OK", $todo_list);
            } else {
                return_response(500, "Internal Server Error", ["message" => "Error al eliminar el elemento"]);
            }
        } else {
            return_response(400, "Bad Request", ["message" => "ID no válido o ausente"]);
        }
        break;

    case 'PUT':  // Nuevo caso para manejar la actualización de tareas
        if (isset($_GET['id']) && is_numeric($_GET['id'])) {
            $db = new DB();
            $item_id = (int)$_GET['id'];

            $updated_todo = new Todo;
            $updated_todo->jsonConstruct($bodyRequest);
            $updated_todo->setItem_id($item_id); // Asignar el ID recibido a la tarea

            if ($updated_todo->DB_update($db->connection)) {
                $todo_list = Todo::DB_selectAll($db->connection);
                return_response(200, "OK", $todo_list);
            } else {
                return_response(500, "Internal Server Error", ["message" => "Error al actualizar el elemento"]);
            }
        } else {
            return_response(400, "Bad Request", ["message" => "ID no válido o ausente"]);
        }
        break;

    default:
        return_response(405, "Method Not Allowed", ["message" => "Método no permitido"]);
        break;
}
?>
