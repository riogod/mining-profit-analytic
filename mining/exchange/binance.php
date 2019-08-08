<?php

$mysqli = new mysqli("localhost", "root", "", "miningstat");
if ($mysqli->connect_errno) {
    printf("Не удалось подключиться: %s\n", $mysqli->connect_error);
    exit();
}

$url = "https://api.binance.com/api/v1/ticker/24hr";

$contents = file_get_contents($url);
$contents = utf8_encode($contents);
$results = json_decode($contents);


//print_r($results);


foreach ($results as $res) {
  //  print_r($res);



if (substr($res->symbol, -3) == 'BTC') {
 //   echo substr($res->symbol, -3).'<br>';

 //   echo $res->symbol.': '.substr($res->symbol,0,-3).'<br>';
            $coinsymb = substr($res->symbol,0,-3);



            $add .= " ('".strtoupper($coinsymb)."', ".$res->lastPrice.", ".$res->quoteVolume.", ".$res->askPrice.", ".$res->bidPrice.", 'binance'),";
        }

}
//echo "INSERT INTO coin_pricehistory (	symbol, last, volume, ask, bid, exchange) VALUES".substr($add,0,-1);

$mysqli->query("INSERT INTO coin_pricehistory (	symbol, last, volume, ask, bid, exchange) VALUES".substr($add,0,-1));


/*
             $coins[] = array(
                'symbol' => $coinsymb,
                'last' => $res->lastPrice,
                'volume' => $res->volume,
                'ask' => $res->askPrice,
                'bid' => $res->bidPrice,
            );
print_r($coins);
 */

?>