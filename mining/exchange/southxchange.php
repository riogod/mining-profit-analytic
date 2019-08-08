<?php

$mysqli = new mysqli("localhost", "root", "", "miningstat");
if ($mysqli->connect_errno) {
    printf("Не удалось подключиться: %s\n", $mysqli->connect_error);
    exit();
}

$url = "https://www.southxchange.com/api/prices";

$contents = file_get_contents($url);
$contents = utf8_encode($contents);
$results = json_decode($contents);


//print_r($pieces);
foreach ($results as $res) {
    $pieces = explode('/', $res->Market);

        if($pieces[1] == "BTC")
        {

            $add .= " ('".strtoupper($pieces[0])."', ".($res->Last=='' ? 0 : $res->Last).", ".($res->Volume24Hr=='' ? 0 : ($res->Volume24Hr*$res->Last)).", ".($res->Ask=='' ? 0 : $res->Ask).", ".($res->Bid=='' ? 0 : $res->Bid).", 'southxchange'),";

        }

}

$mysqli->query("INSERT INTO coin_pricehistory (	symbol, last, volume, ask, bid, exchange) VALUES".substr($add,0,-1));


?>