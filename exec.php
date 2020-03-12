<?php

require_once dirname(__FILE__) . "/db.php";

$config = json_decode(file_get_contents('./config.json'), true);

$db = isset($_GET['db']) ? $config['databases'][$_GET['db']] : null;
$conn = new DB($db["host"], $db["port"], $db["database"], $db["user"], $db["password"]);

$input = json_decode(preg_replace('/[\n\s]+/', ' ', file_get_contents("php://input")), true);

$exec = $conn->query($input["sql"]);

if ($exec === false) {
    $response = [
        "status" => "fail",
        "message" => "SQL Error! (#" . $conn->errorNum() . ") " . $conn->errorDesc()
    ];
} else {
    $response = [
        "status" => "ok",
        "message" => "SQL executada com sucesso em {$db["host"]}:{$db["port"]}@{$db["database"]}"
    ];
}

echo json_encode($response);

?>