<?php

class DB {
    public $connection;
    public function __construct() {
        $db_host = "127.0.0.1";
        $db_name = "todo_db";
        $db_user = "todo";
        $db_pass = "pepito";
        $this->connection = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);;
    }

    public function __destruct() {
        $this->connection = null;
    }
}