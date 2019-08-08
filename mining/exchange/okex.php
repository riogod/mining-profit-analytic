<?php

$mysqli = new mysqli("localhost", "root", "", "miningstat");
if ($mysqli->connect_errno) {
    printf("Не удалось подключиться: %s\n", $mysqli->connect_error);
    exit();
}

$url = "https://www.okex.com/api/v1/tickers.do";

$contents = file_get_contents($url);
$contents = utf8_encode($contents);
$results = json_decode($contents);

//print_R($results->tickers);


foreach ($results->tickers as $res) {
    $pieces = explode("_", $res->symbol);
        if($pieces[1] == "btc")
        {
            $add .= " ('".strtoupper($pieces[0])."', ".$res->last.", ".($res->vol*$res->last).", ".$res->buy.", ".$res->sell.", 'okex'),";
        }

}

$mysqli->query("INSERT INTO coin_pricehistory (	symbol, last, volume, ask, bid, exchange) VALUES".substr($add,0,-1));

?>