<?php

class Todo implements \JsonSerializable {
    private int $item_id;
    private string $content;
    
    // Inicializa todas las variables del objeto con las pasadas por parámetros
    public function parametersConstruct(int $item_id, string $content) {
        $this->item_id = $item_id;
        $this->content = $content;
    }

    // Inicializa todas las variables con el json pasado por parametro
    public function jsonConstruct($json) {
        foreach (json_decode($json, true) as $key => $value) {
            $this->{$key} = $value;
        }
    }

    // Convierte el objeto a un json (jsonEncode)
    public function jsonSerialize() {
        $vars = get_object_vars($this);
        return $vars;
    }
    
    public function getContent() {		
        return $this->content;	
    }
    
    public function getItem_id() {		
        return $this->item_id;
    }

    public function setItem_id($item_id) {  // Nuevo método para asignar el ID
        $this->item_id = $item_id;
    }

    // Devuelve todos los elementos de la BBDD en forma de array
    public static function DB_selectAll($dbconn) {
        $todo_list = array();
        foreach($dbconn->query("SELECT item_id, content FROM todo_list") as $row) {
            $new_todo = new Todo;
            $new_todo->parametersConstruct($row['item_id'], $row['content']);
            $todo_list[] = $new_todo;
        }
        return $todo_list;
    }

    // Inserta un nuevo elemento en la base de datos
    public function DB_insert($dbconn) {
        $sql = "INSERT INTO `todo_list` (content) VALUES(?)";
        $stmt = $dbconn->prepare($sql);
        $stmt->execute([$this->content]);
    }

    // Elimina un elemento específico de la base de datos usando su item_id
    public static function DB_delete($dbconn, $item_id) {
        $sql = "DELETE FROM todo_list WHERE item_id = ?";
        $stmt = $dbconn->prepare($sql);
        return $stmt->execute([$item_id]);
    }

    // Nuevo método para actualizar un elemento específico de la base de datos
    public function DB_update($dbconn) {
        $sql = "UPDATE todo_list SET content = ? WHERE item_id = ?";
        $stmt = $dbconn->prepare($sql);
        return $stmt->execute([$this->content, $this->item_id]);
    }
}
?>
