<?php

$mysqli = new mysqli("localhost", "root", "", "miningstat");
if ($mysqli->connect_errno) {
    printf("Не удалось подключиться: %s\n", $mysqli->connect_error);
    exit();
}

$url = "https://graviex.net/api/v2/tickers.json";

$arrContextOptions=array(
    "ssl"=>array(

        "verify_peer"=> false,
        "verify_peer_name"=> false,
    ),
);

$contents = file_get_contents($url, false, stream_context_create($arrContextOptions));
$contents = utf8_encode($contents);
$results = json_decode($contents, true);


//print_r($results);


foreach ($results as $key=>$res) {
  //  print_r($res);



if (substr($key, -3) == 'btc') {
//   echo substr($key, -3).'-'.$res['ticker']['last'].'<br>';

 //   echo $res->symbol.': '.substr($res->symbol,0,-3).'<br>';
            $coinsymb = substr($key,0,-3);



            $add .= " ('".strtoupper($coinsymb)."', ".$res['ticker']['last'].", ".$res['ticker']['volbtc'].", ".$res['ticker']['sell'].", ".$res['ticker']['buy'].", 'graviex'),";
        }

}
//echo "INSERT INTO coin_pricehistory (	symbol, last, volume, ask, bid, exchange) VALUES".substr($add,0,-1);

$mysqli->query("INSERT INTO coin_pricehistory (	symbol, last, volume, ask, bid, exchange) VALUES".substr($add,0,-1));




?>