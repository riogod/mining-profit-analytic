<?php

include('init.php');

$arrContextOptions=array(
    "ssl"=>array(

        "verify_peer"=> false,
        "verify_peer_name"=> false,
    ),
);

$contents = file_get_contents($base_url.'explorer/', false, stream_context_create($arrContextOptions));


preg_match_all('/<tr class="ssrow">[\s\S]*<\/b><\/td><td><b>(.*)<\/b><\/td><td>(.*)<\/td><td>[\s\S]*_peers\((.*)\)[\s\S]*<\/tr>/U', $contents, $hsr);


foreach ($hsr[1] as $hsr_key=>$hsr_coin)
{
    if (strpos($hsr_coin, '-') !== false) {
        $hsr_coin = strstr($hsr_coin, '-', true);
    }

    $get_coin_data = file_get_contents($base_url.'site/block_results?id='.$hsr[3][$hsr_key], false, stream_context_create($arrContextOptions));
    preg_match('/<tr class=\'ssrow\'>[\s\S]*<\/td><td>[\s\S]*<\/td><td>[\s\S]*<\/td><td>[\s\S]*([0-9.]*)<\/td><td class="generate">Confirmed[\s\S]*<\/tr>/U', $get_coin_data, $coin);



    INS_coin_prices(find_cur_name($hsr_coin, $mysqli), $hsr_coin, $coin[1], $hsr[2][$hsr_key], $mysqli);
//        $mysqli->query("INSERT INTO coin_prices (coin_name, symbol, reward_per_b, algo) VALUES ('".find_cur_name($hsr_coin, $mysqli)."', '".$hsr_coin."', ".$coin[1].", '".$hsr[2][$hsr_key]."') ON DUPLICATE KEY UPDATE reward_per_b=".$coin[1]);

//        print("INSERT INTO coin_prices (coin_name, symbol, reward_per_b, algo) VALUES ('".find_cur_name($hsr_coin, $mysqli)."', '".$hsr_coin."', ".$coin[1].", '".$hsr[2][$hsr_key]."') ON DUPLICATE KEY UPDATE reward_per_b=".$coin[1].'<br><br>');


    flush();
    ob_flush();
}





?>