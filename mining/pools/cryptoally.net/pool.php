<?php

include('init.php');




$contents = file_get_contents($url);
$contents = utf8_encode($contents);
$results = json_decode($contents, true);


foreach ($results as $symbol => $res)
{

    if (strpos($symbol, '-') !== false)
    {
        $symbol = strstr($symbol, '-', true);
    }

    $check_coin = COIN_check_if_exit($res['name'], $pool_name, $res['algo'], $symbol, $mysqli);
    if($check_coin != true)
    {
        $asserr .= $check_coin;
    }
    $mysqli->query("INSERT INTO pools_stat (coin_name, symbol, workers, shares, hashrate, estimate, 24h_blocks, timesincelast, pool, algo) VALUES ('".$res['name']."', '".$symbol."', '".$res['workers']."', '".$res['shares']."', '".$res['hashrate']."', '".$res['estimate']."', '".$res['24h_blocks']."', '".$res['timesincelast']."', '".$pool_name."', '".$res['algo']."')");
    if($mysqli->affected_rows <= 0)
    {
        $asserr .= "Error(pools_stat):VALUES ('".$res['name']."', '".$symbol."', '".$res['workers']."', '".$res['shares']."', '".$res['hashrate']."', '".$res['estimate']."', '".$res['24h_blocks']."', '".$res['timesincelast']."', '".$pool_name."', '".$res['algo']."')".PHP_EOL;
    }
//echo "INSERT INTO pools_stat (coin_name, symbol, workers, shares, hashrate, estimate, 24h_blocks, timesincelast, pool) VALUES ('".$res['name']."', '".$symbol."', '".$res['workers']."', '".$res['shares']."', '".$res['hashrate']."', '".$res['estimate']."', '".$res['24h_blocks']."', '".$res['timesincelast']."', '".$pool_name."')<br><br>";

}




?>