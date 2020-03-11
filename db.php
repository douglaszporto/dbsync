<?php

class DB {

    private $conn;

    function __construct($host, $port, $database, $user, $password) {
        $this->conn = mysqli_init();
        $this->conn->connect($host, $user, $password, $database, $port);
    }

    function query($sql) {
        $query = $this->conn->query($sql);
        $result = [];

        while($res = $query->fetch_array()) {
            $result[] = $res;
        }

        return $result;

    }
}

?>