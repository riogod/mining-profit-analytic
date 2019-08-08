<?php

include('init.php');



$api_url = $base_url.'index.php?page=api&action=getpoolinfo&api_key='.$api_key;

$contents = file_get_contents($api_url);
$contents = utf8_encode($contents);
$results = json_decode($contents, true);



$res = $results['getpoolinfo']['data'];
$symbol = $res['currency'];
if (strpos($symbol, '-') !== false)
{
    $symbol = strstr($symbol, '-', true);
}


    INS_coin_prices($res['coinname'], $symbol, $res['reward'], $res['algorithm'], $mysqli);


?>