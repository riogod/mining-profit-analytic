<?php

include('init.php');
require __DIR__ . '/../../src/autoload.php';

use CloudflareBypass\RequestMethod\CFCurl;

$curl_cf_wrapper = new CFCurl(array(
    'cache'         => true,   // Caching now enabled by default; stores clearance tokens in Cache folder
    'max_retries'   => 5       // Max attempts to try and get CF clearance
));


$ch = curl_init($api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36');
$contents = $curl_cf_wrapper->exec($ch); // Done! NOTE: HEAD requests not supported!
curl_close($ch);



$contents = utf8_encode($contents);
$results = json_decode($contents, true);

//print_r($results);

foreach ($results as $symbol => $res)
{


    $check_coin = COIN_check_if_exit($res['name'], $pool_name, $res['algo'], $symbol, $mysqli);
    if($check_coin != true)
    {
        $asserr .= $check_coin;
    }
    $mysqli->query("INSERT INTO pools_stat (coin_name, symbol, workers, shares, hashrate, estimate, 24h_blocks, timesincelast, pool, algo) VALUES ('".$res['name']."', '".$symbol."', '".$res['workers']."', '".$res['shares']."', '".$res['hashrate']."', '".$res['estimate']."', '".$res['24h_blocks']."', '".$res['timesincelast']."', '".$pool_name."', '".$res['algo']."')");
    if($mysqli->affected_rows <= 0)
    {
        $asserr .= "Error(pools_stat): VALUES ('".$res['name']."', '".$symbol."', '".$res['workers']."', '".$res['shares']."', '".$res['hashrate']."', '".$res['estimate']."', '".$res['24h_blocks']."', '".$res['timesincelast']."', '".$pool_name."', '".$res['algo']."')".PHP_EOL;
    }
//echo "INSERT INTO pools_stat (coin_name, symbol, workers, shares, hashrate, estimate, 24h_blocks, timesincelast, pool, algo) VALUES ('".$res['name']."', '".$symbol."', '".$res['workers']."', '".$res['shares']."', '".$res['hashrate']."', '".$res['estimate']."', '".$res['24h_blocks']."', '".$res['timesincelast']."', '".$pool_name."', '".$res['algo']."')<br>";
}



function get_contents($url, $ua = 'Mozilla/5.0 (Windows NT 5.1; rv:2.0.1) Gecko/20100101 Firefox/4.0.1', $referer = 'http://www.google.com/') {
    if (function_exists('curl_exec')) {
        $header[0] = "Accept-Language: en-us,en;q=0.5";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_USERAGENT, $ua);
        curl_setopt($curl, CURLOPT_REFERER, $referer);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        $content = curl_exec($curl);
        curl_close($curl);
    }
    else {
        $content = file_get_contents($url);
    }
    return $content;
}

?>