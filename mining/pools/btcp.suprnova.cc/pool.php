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
$check_coin = COIN_check_if_exit($res['coinname'], $pool_name, $res['algorithm'], $symbol, $mysqli);
if($check_coin != true)
{
    $asserr .= $check_coin;
}
$api_hashinfo = $base_url.'index.php?page=api&action=public';

$contentshash = file_get_contents($api_hashinfo);
$contentshash = utf8_encode($contentshash);
$resulthash = json_decode($contentshash, true);



$api_blockinfo = $base_url.'index.php?page=api&action=getblockstats&api_key='.$api_key;

$contentblock = file_get_contents($api_blockinfo);
$contentblock = utf8_encode($contentblock);
$resultblock = json_decode($contentblock, true);



$api_getpoolstatus = $base_url.'index.php?page=api&action=getpoolstatus&api_key='.$api_key;

$contengetpoolstatus = file_get_contents($api_getpoolstatus);
$contengetpoolstatus = utf8_encode($contengetpoolstatus);
$contengetpoolstatus = json_decode($contengetpoolstatus, true);

//print_r($results);
//print_r($resulthash);
//print_r($resultblock);
//print_r($contengetpoolstatus);



$mysqli->query("INSERT INTO pools_stat (coin_name, symbol, workers, shares, hashrate, estimate, 24h_blocks, timesincelast, pool, algo) VALUES ('".$res['coinname']."', '".$symbol."', '".$resulthash['workers']."', '".$resultblock['getblockstats']['data']['24HourEstimatedShares']."', '".($resulthash['hashrate']*1000)."', 0, '".$resultblock['getblockstats']['data']['24HourValid']."', '".$contengetpoolstatus['getpoolstatus']['data']['timesincelast']."', '".$pool_name."', '".$res['algorithm']."')");
//print("<br><br>INSERT INTO pools_stat (coin_name, symbol, workers, shares, hashrate, estimate, 24h_blocks, timesincelast, pool, algo) VALUES ('".$res['coinname']."', '".$symbol."', '".$resulthash['workers']."', '".$resultblock['getblockstats']['data']['24HourEstimatedShares']."', '".$resulthash['hashrate']."', 0, '".$resultblock['getblockstats']['data']['24HourValid']."', '".$contengetpoolstatus['getpoolstatus']['data']['timesincelast']."', '".$pool_name."', '".$res['algorithm']."')");
if($mysqli->affected_rows <= 0)
{
    $asserr .= "Error(pools_stat): VALUES ('".$res['coinname']."', '".$symbol."', '".$resulthash['workers']."', '".$resultblock['getblockstats']['data']['24HourEstimatedShares']."', '".($resulthash['hashrate']*1000)."', 0, '".$resultblock['getblockstats']['data']['24HourValid']."', '".$contengetpoolstatus['getpoolstatus']['data']['timesincelast']."', '".$pool_name."', '".$res['algorithm']."')".PHP_EOL;
}

?>