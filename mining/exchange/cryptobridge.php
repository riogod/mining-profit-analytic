<?php

$mysqli = new mysqli("localhost", "root", "", "miningstat");
if ($mysqli->connect_errno) {
    printf("Не удалось подключиться: %s\n", $mysqli->connect_error);
    exit();
}

$url = "https://api.crypto-bridge.org/api/v1/ticker/";

$contents = file_get_contents($url);
$contents = utf8_encode($contents);
$results = json_decode($contents);


foreach ($results as $res) {
    $pieces = explode("_", $res->id);
        if($pieces[1] == "BTC")
        {
            $add .= " ('".strtoupper($pieces[0])."', ".$res->last.", ".$res->volume.", ".$res->ask.", ".$res->bid.", 'cryptobridge'),";
        }

}
$mysqli->query("INSERT INTO coin_pricehistory (	symbol, last, volume, ask, bid, exchange) VALUES".substr($add,0,-1));

?>