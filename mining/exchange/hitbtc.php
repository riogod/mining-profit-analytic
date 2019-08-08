<?php

$mysqli = new mysqli("localhost", "root", "", "miningstat");
if ($mysqli->connect_errno) {
    printf("Не удалось подключиться: %s\n", $mysqli->connect_error);
    exit();
}

$url = "https://api.hitbtc.com/api/2/public/ticker";

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



            $add .= " ('".strtoupper($coinsymb)."', ".($res->last=='' ? 0 : $res->last).", ".($res->volumeQuote=='' ? 0 : $res->volumeQuote).", ".($res->ask=='' ? 0 : $res->ask).", ".($res->bid=='' ? 0 : $res->bid).", 'hitbtc'),";
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