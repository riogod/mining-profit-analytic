<?php

$mysqli = new mysqli("localhost", "root", "", "miningstat");
if ($mysqli->connect_errno) {
    printf("Не удалось подключиться: %s\n", $mysqli->connect_error);
    exit();
}

$url = "https://tradesatoshi.com/api/public/getmarketsummaries";

$contents = file_get_contents($url);
$contents = utf8_encode($contents);
$results = json_decode($contents);

//print_r($results);


foreach ($results->result as $res) {
    $pieces = explode("_", $res->market);
        if($pieces[1] == "BTC")
        {
            $add .= " ('".strtoupper($pieces[0])."', ".$res->last.", ".$res->baseVolume.", ".$res->ask.", ".$res->bid.", 'tradesatoshi'),";
     //       echo " ('".strtoupper($pieces[0])."', ".$res->last.", ".$res->baseVolume.", ".$res->ask.", ".$res->bid.", 'tradesatoshi'),<br><br>";

        }

}
$mysqli->query("INSERT INTO coin_pricehistory (	symbol, last, volume, ask, bid, exchange) VALUES".substr($add,0,-1));
//print("INSERT INTO coin_pricehistory (	symbol, last, volume, ask, bid, exchange) VALUES".substr($add,0,-1)."<br>");


?>