<?php

if (php_sapi_name() == 'cli-server') {
    $info = parse_url($_SERVER['REQUEST_URI']);

    if (file_exists( dirname(__FILE__) . "{$info['path']}")) {
        return false;
    }

    switch($info['path']) {
        case '/diff':
            include_once "diff.php";
            break;
        default:
            include_once "index.php";
            break;
    }
    return true;
}

?>