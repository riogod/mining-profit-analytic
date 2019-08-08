<?php

$mysqli = new mysqli("localhost", "root", "", "miningstat");
if ($mysqli->connect_errno) {
    printf("Не удалось подключиться: %s\n", $mysqli->connect_error);
    exit();
}

$url = "https://poloniex.com/public?command=returnTicker";

$contents = file_get_contents($url);
$contents = utf8_encode($contents);
$results = json_decode($contents);

//print_R($results);


foreach ($results as $key=>$res) {
    $pieces = explode("_", $key);
        if($pieces[0] == "BTC")
        {
            $add .= " ('".strtoupper($pieces[1])."', ".$res->last.", ".$res->baseVolume.", ".$res->lowestAsk.", ".$res->highestBid.", 'poloniex'),";
        }

}

$mysqli->query("INSERT INTO coin_pricehistory (	symbol, last, volume, ask, bid, exchange) VALUES".substr($add,0,-1));

?>