<?php

$config = json_decode(file_get_contents('./config.json'), true);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css?family=Roboto&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/statics/styles.css">
    <title>DB Sync</title>
</head>
<body>
    
    <template id="template-query">
        <div class="query-exec-wrapper">
            <div class="query-exec-element">
                <pre class="query-exec-element-query">&nbsp;</pre>
                <div class="query-exec-element-options">
                    <div class="query-exec-element-ignore">Ignorar</div>
                    <div class="query-exec-element-execute">Executar</div>
                </div>
            </div>
        </div>
    </template>
    <img id="logo" src="/statics/logo.png" alt=""/>
    <p id="instructions">
        Selecione o bando de dados de origem e de destino (configurados no config.json), depois clique em comparar.<br />
        Cada SQL necessária poderá ser executada unitariamente no DB escolhido
    </p>
    <div class="database-selector">
        <div class="database database-first">
            <select id="input-database-1">
                <option value="">Selecione o DB "mine"</option>
                <?php foreach($config["databases"] as $i=>$db): ?>
                    <option value="<?php echo $i; ?>"><?php echo $db["name"]?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="database database-secound">
            <select id="input-database-2">
            <option value="">Selecione o DB "theirs"</option>
                <?php foreach($config["databases"] as $i=>$db): ?>
                    <option value="<?php echo $i; ?>"><?php echo $db["name"]?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <button id="btn-compare" class="disabled">Comparar</button>
    <div id="database-diff"></div>

    <script src="/statics/scripts.js"></script>
</body>
</html>