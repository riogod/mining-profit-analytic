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
if($t_algo != '')
{
    $res['algorithm'] = $t_algo;
}
$api_blockinfo = $base_url.'index.php?page=api&action=getblockstats&api_key='.$api_key;

$contentblock = file_get_contents($api_blockinfo);
$contentblock = utf8_encode($contentblock);
$resultblock = json_decode($contentblock, true);

$reward = $resultblock['getblockstats']['data']['7DaysAmount']/$resultblock['getblockstats']['data']['7DaysValid'];

    INS_coin_prices($res['coinname'], $symbol, $reward, $res['algorithm'], $mysqli);


?>