<?php

include('init.php');
$tofind_arr = array();



$contents = file_get_contents($api_url);
$contents = utf8_encode($contents);
$results = json_decode($contents, true);



foreach ($results as $skey => $res) {

    if (strpos($skey, '-') !== false)
    {
        $skey = strstr($skey, '-', true);
    }
 //   $mysqli->query("INSERT INTO coin_prices (coin_name, symbol, reward_per_b, algo) VALUES ('".$res['name']."', '".$skey."', ".$res['reward'].", '".$res['algo']."' ) ON DUPLICATE KEY UPDATE reward_per_b=".$res['reward']);
    INS_coin_prices($res['name'], $skey, $res['reward'], $res['algo'], $mysqli);
}




?>