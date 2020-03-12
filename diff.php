<?php

require_once dirname(__FILE__) . "/db.php";

$config = json_decode(file_get_contents('./config.json'), true);

$db1 = isset($_GET['db1']) ? $config['databases'][$_GET['db1']] : null;
$db2 = isset($_GET['db2']) ? $config['databases'][$_GET['db2']] : null;

$conn1 = new DB($db1["host"], $db1["port"], $db1["database"], $db1["user"], $db1["password"]);
$conn2 = new DB($db2["host"], $db2["port"], $db2["database"], $db2["user"], $db2["password"]);


$response = [];




$t1 = $conn1->query("SHOW TABLES;");
$t2 = $conn2->query("SHOW TABLES;");

$tableIn1 = array_map(function($row) {return $row[0];}, $t1);
$tableIn2 = array_map(function($row) {return $row[0];}, $t2);

$tablesToCreateOn1 = array_diff($tableIn2, $tableIn1);
$tablesToCreateOn2 = array_diff($tableIn1, $tableIn2);


$response["table"] = [];

foreach($tablesToCreateOn1 as $t) {
    $queryCreateTable = $conn2->query("SHOW CREATE TABLE {$t}");
    $response["table"][] = [
        "id" => rand() * 1000000,
        "to" => "1",
        "from" => "2",
        "sql" => $queryCreateTable[0][1],
        "reversesql" => "DROP TABLE {$t}"
    ];
}

foreach($tablesToCreateOn2 as $t) {
    $queryCreateTable = $conn1->query("SHOW CREATE TABLE {$t}");
    $response["table"][] = [
        "id" => rand() * 1000000,
        "to" => "2",
        "from" => "1",
        "sql" => $queryCreateTable[0][1],
        "reversesql" => "DROP TABLE {$t}"
    ];
}






$response["field"] = [];
$tablesInBoth = array_intersect($tableIn1, $tableIn2);

foreach($tablesInBoth as $t) {
    $fieldsOnTable1 = $conn1->query("SHOW COLUMNS FROM {$t}");
    $fieldsOnTable2 = $conn2->query("SHOW COLUMNS FROM {$t}");

    $mapFields1 = array_map(function($item) { return $item['Field'];}, $fieldsOnTable1);
    $mapFields2 = array_map(function($item) { return $item['Field'];}, $fieldsOnTable2);

    $fieldsOnlyOn1 = array_diff($mapFields1, $mapFields2);
    $fieldsOnlyOn2 = array_diff($mapFields2, $mapFields1);

    foreach($fieldsOnTable1 as $f1 => $field1) {
        $acceptNull = $fieldsOnTable1[$f1]["Null"] == 'YES' ? " NULL" : " NOT NULL";
        $default = strlen($fieldsOnTable1[$f1]["Default"]) > 0 ? (" DEFAULT " . $fieldsOnTable1[$f1]["Default"]) : "";

        if (strlen($acceptNull) == 5 && strlen($default) == 0) {
            $acceptNull = "";
            $default = " DEFAULT NULL ";
        }

        if (in_array($field1["Field"], $fieldsOnlyOn1)) {
            $response["field"][] = [
                "id" => rand() * 1000000,
                "to" => "2",
                "from" => "1",
            "sql" => "ALTER TABLE '{$t}' ADD COLUMN \`{$field1["Field"]}\` {$field1["Type"]}{$acceptNull}{$default};",
                "reversesql" => "ALTER TABLE {$t} DROP COLUMN {$field1["Field"]}"
            ];
        } else if (isset($fieldsOnTable2[$f1])) {
            if ($fieldsOnTable1[$f1]["Type"] != $fieldsOnTable2[$f1]["Type"] || 
                    $fieldsOnTable1[$f1]["Null"] != $fieldsOnTable2[$f1]["Null"] ||
                    $fieldsOnTable1[$f1]["Key"] != $fieldsOnTable2[$f1]["Key"] ||
                    $fieldsOnTable1[$f1]["Default"] != $fieldsOnTable2[$f1]["Default"] ||
                    $fieldsOnTable1[$f1]["Extra"] != $fieldsOnTable2[$f1]["Extra"]){

                $acceptNull2 = $fieldsOnTable2[$f1]["Null"] == 'YES' ? " NULL" : " NOT NULL";
                $default2 = strlen($fieldsOnTable2[$f1]["Default"]) > 0 ? (" DEFAULT " . $fieldsOnTable2[$f1]["Default"]) : "";
        
                if (strlen($acceptNull2) == 5 && strlen($default2) == 0) {
                    $acceptNull2 = "";
                    $default2 = " DEFAULT NULL ";
                }

                $response["field"][] = [
                    "id" => rand() * 1000000,
                    "to" => "2",
                    "from" => "1",
                    "sql" => "ALTER TABLE '{$t}' MODIFY COLUMN \`{$field1["Field"]}\` {$field1["Type"]}{$acceptNull}{$default};",
                    "reversesql" => "ALTER TABLE '{$t}' MODIFY COLUMN \`{$field1["Field"]}\` {$fieldsOnTable2[$f1]["Type"]}{$acceptNull2}{$default2};"
                ];
            }
        }
    }

    foreach($fieldsOnTable2 as $f2 => $field2) {
        $acceptNull = $fieldsOnTable2[$f2]["Null"] == 'YES' ? " NULL" : " NOT NULL";
        $default = strlen($fieldsOnTable2[$f2]["Default"]) > 0 ? (" DEFAULT " . $fieldsOnTable2[$f2]["Default"]) : "";

        if (strlen($acceptNull) == 5 && strlen($default) == 0) {
            $acceptNull = "";
            $default = " DEFAULT NULL ";
        }

        if (in_array($field2["Field"], $fieldsOnlyOn2)) {
            $response["field"][] = [
                "id" => rand() * 1000000,
                "to" => "1",
                "from" => "2",
                "sql" => "ALTER TABLE '{$t}' ADD COLUMN \`{$field2["Field"]}\` {$field2["Type"]}{$acceptNull}{$default};",
                "reversesql" => "ALTER TABLE '{$t}' DROP COLUMN \`{$field2["Field"]}\`"
            ];
        } else if (isset($fieldsOnTable1[$f2])) {
            if ($fieldsOnTable1[$f2]["Type"] != $fieldsOnTable2[$f2]["Type"] || 
                    $fieldsOnTable1[$f2]["Null"] != $fieldsOnTable2[$f2]["Null"] ||
                    $fieldsOnTable1[$f2]["Key"] != $fieldsOnTable2[$f2]["Key"] ||
                    $fieldsOnTable1[$f2]["Default"] != $fieldsOnTable2[$f2]["Default"] ||
                    $fieldsOnTable1[$f2]["Extra"] != $fieldsOnTable2[$f2]["Extra"]){

                $acceptNull2 = $fieldsOnTable1[$f2]["Null"] == 'YES' ? " NULL" : " NOT NULL";
                $default2 = strlen($fieldsOnTable1[$f2]["Default"]) > 0 ? (" DEFAULT " . $fieldsOnTable1[$f2]["Default"]) : "";
        
                if (strlen($acceptNull2) == 5 && strlen($default2) == 0) {
                    $acceptNull2 = "";
                    $default2 = " DEFAULT NULL ";
                }

                $response["field"][] = [
                    "id" => rand() * 1000000,
                    "to" => "1",
                    "from" => "2",
                    "sql" => "ALTER TABLE '{$t}' MODIFY COLUMN \`{$field2["Field"]}\` {$field2["Type"]}{$acceptNull}{$default};",
                    "reversesql" => "ALTER TABLE '{$t}' MODIFY COLUMN \`{$field2["Field"]}\` {$fieldsOnTable1[$f1]["Type"]}{$acceptNull2}{$default2};"
                ];
            }
        }
    }
}




echo json_encode($response);


?>