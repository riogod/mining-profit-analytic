<?php

set_time_limit(36000);
include('init.php');


require __DIR__ . '/../../src/autoload.php';

use CloudflareBypass\RequestMethod\CFCurl;

$curl_cf_wrapper = new CFCurl(array(
    'cache'         => true,   // Caching now enabled by default; stores clearance tokens in Cache folder
    'max_retries'   => 5       // Max attempts to try and get CF clearance
));

$ch = curl_init($base_url.'explorer/');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36');
$contents = $curl_cf_wrapper->exec($ch); // Done! NOTE: HEAD requests not supported!
curl_close($ch);
echo $contents;

/*

preg_match_all('/<tr class="ssrow">[\s\S]*<\/b><\/td><td><b>(.*)<\/b><\/td><td>(.*)<\/td><td>[\s\S]*_peers\((.*)\)[\s\S]*<\/tr>/U', $contents, $hsr);
sleep(30);


foreach ($hsr[1] as $hsr_key=>$hsr_coin)
{


  if (strpos($hsr_coin, '-') !== false) {
        $hsr_coin = strstr($hsr_coin, '-', true);
    }

    $get_coin_data = file_get_contents($base_url.'site/block_results?id='.$hsr[3][$hsr_key], false, stream_context_create($arrContextOptions));
    preg_match('/<tr class=\'ssrow\'>[\s\S]*<\/td><td>[\s\S]*<\/td><td>[\s\S]*<\/td><td>[\s\S]*([0-9.]*)<\/td><td class="generate">Confirmed[\s\S]*<\/tr>/U', $get_coin_data, $coin);


    INS_coin_prices(find_cur_name($hsr_coin, $mysqli), $hsr_coin, $coin[1], $hsr[2][$hsr_key], $mysqli);
 //       $mysqli->query("INSERT INTO coin_prices (coin_name, symbol, reward_per_b, algo) VALUES ('".find_cur_name($hsr_coin, $mysqli)."', '".$hsr_coin."', ".$coin[1].", '".$hsr[2][$hsr_key]."') ON DUPLICATE KEY UPDATE reward_per_b=".$coin[1]);

        print("INSERT INTO coin_prices (coin_name, symbol, reward_per_b, algo) VALUES ('".find_cur_name($hsr_coin, $mysqli)."', '".$hsr_coin."', ".$coin[1].", '".$hsr[2][$hsr_key]."') ON DUPLICATE KEY UPDATE reward_per_b=".$coin[1].'<br><br>');

    flush();
    ob_flush();
sleep(10);

}
*/

?>