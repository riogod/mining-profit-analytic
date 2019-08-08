<?php

include('init.php');
$tofind_arr = array();



$contents = file_get_contents($api_url);
$contents = utf8_encode($contents);
$results = json_decode($contents, true);


//print_r($results);

foreach ($results as $skey => $res) {

    if (strpos($skey, '-') !== false) {
        $skey = strstr($skey, '-', true);
    }
    INS_coin_prices($res['name'], $skey, $res['reward'], $res['algo'], $mysqli);

}
?>