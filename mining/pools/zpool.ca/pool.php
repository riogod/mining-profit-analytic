<?php
set_time_limit(3600);
include('init.php');

//GET PHPSESS --------------------------------------------------------------------------

$ch = curl_init($base_url);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36');
curl_setopt($ch, CURLOPT_HEADER, 1);
curl_setopt($ch, CURLOPT_COOKIESESSION, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, "cookies.txt");
curl_setopt($ch, CURLOPT_COOKIEFILE, "cookies.txt");
//curl_setopt( $ch, CURLOPT_COOKIE, $strCookie );

$contents = curl_exec($ch); // Done! NOTE: HEAD requests not supported!
curl_close($ch);

preg_match_all ('|Set-Cookie: PHPSESSID=(.*);|isU',$contents,$set);

$this_PHPSESS = 'PHPSESSID=$set[1][0]5; path=/';


$get_algos_url = 'https://zpool.ca/site/current_results';
$ch = curl_init($get_algos_url);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36');
curl_setopt($ch, CURLOPT_HEADER, 1);
curl_setopt($ch, CURLOPT_COOKIESESSION, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, "cookies.txt");
curl_setopt($ch, CURLOPT_COOKIEFILE, "cookies.txt");
curl_setopt( $ch, CURLOPT_COOKIE, $this_PHPSESS );

$contents = curl_exec($ch); // Done! NOTE: HEAD requests not supported!
curl_close($ch);


preg_match_all('/elect_algo\("(.*)"\)\'>/U', $contents, $matches);


foreach ($matches[1] as $algo)
{
    $ch = curl_init('https://zpool.ca/site/gomining?algo='.$algo);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36');
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLOPT_COOKIESESSION, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, "cookies.txt");
    curl_setopt($ch, CURLOPT_COOKIEFILE, "cookies.txt");
    curl_setopt( $ch, CURLOPT_COOKIE, $this_PHPSESS );
    curl_exec($ch); // Done! NOTE: HEAD requests not supported!
    curl_close($ch);



    $ch = curl_init('https://zpool.ca/site/history_results');

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36');
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLOPT_COOKIESESSION, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, "cookies.txt");
    curl_setopt($ch, CURLOPT_COOKIEFILE, "cookies.txt");
    curl_setopt( $ch, CURLOPT_COOKIE, $this_PHPSESS );
    $contents = curl_exec($ch); // Done! NOTE: HEAD requests not supported!
    curl_close($ch);

//echo $contents;
    preg_match_all('/<tr class=\"ssrow\">[\s\S]*id=[0-9]*">(.*)<\/a>[\s\S]*\"symb\">(.*)<\/td>[\s\S]*9em;\">[\s\S]*9em;\">(.*)<\/td>[\s\S]*<\/tr>/U', $contents, $hsr);

//print_r($hsr);

    foreach($hsr[2] as $key=>$pdk)
    {
        $gdata[$pdk][$algo] = array(
            'name' => $hsr[1][$key],
            'block24' => $hsr[3][$key]
        );

    }

    $ch = curl_init('https://zpool.ca/site/mining_results');

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36');
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLOPT_COOKIESESSION, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, "cookies.txt");
    curl_setopt($ch, CURLOPT_COOKIEFILE, "cookies.txt");
    curl_setopt( $ch, CURLOPT_COOKIE, $this_PHPSESS );
    $contents = curl_exec($ch); // Done! NOTE: HEAD requests not supported!
    curl_close($ch);

//echo $contents;
    preg_match_all('/id=.*>(.*)<\/a>[\s\S]*\)<\/td><td class=row right><b>(.*) (.*)<\/a>[\s\S]*s\" data=\"(.*)\"/U', $contents, $hsr);

//print_r($hsr);

    foreach($hsr[3] as $key=>$pdk)
    {
        $gdata[$pdk][$algo]['reward'] = $hsr[2][$key];
        $gdata[$pdk][$algo]['hashrate'] = $hsr[4][$key];


    }



}

$arrContextOptions=array(
    "ssl"=>array(

        "verify_peer"=> false,
        "verify_peer_name"=> false,
    ),
);

$contents = file_get_contents($url, false, stream_context_create($arrContextOptions));
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

unset($dat_blk);
    unset($dat_hsr);
    if(!isset($gdata[$symbol][$res['algo']]['hashrate']) or $gdata[$symbol][$res['algo']]['hashrate'] = '')
    {
       $dat_hsr = 0;
    } else {
       $dat_hsr =  $gdata[$symbol][$res['algo']]['hashrate'];
    }

    if(!isset($gdata[$symbol][$res['algo']]['block24']) or $gdata[$symbol][$res['algo']]['block24'] = '')
    {
        $dat_blk = 0;

    } else {
        $dat_blk = $gdata[$symbol][$res['algo']]['block24'];
    }



    $mysqli->query("INSERT INTO pools_stat (coin_name, symbol, workers, shares, hashrate, estimate, 24h_blocks, timesincelast, pool, algo) VALUES ('".$res['name']."', '".$symbol."', '".$res['workers']."', '".$res['shares']."', ".$dat_hsr.", '".$res['estimate']."', ".$dat_blk.", '".$res['timesincelast']."', '".$pool_name."', '".$res['algo']."')");
    if($mysqli->affected_rows <= 0)
    {
        $asserr .= "Error(pools_stat): VALUES ('".$res['name']."', '".$symbol."', '".$res['workers']."', '".$res['shares']."', '".$dat_hsr."', '".$res['estimate']."', '".$dat_blk."', '".$res['timesincelast']."', '".$pool_name."', '".$res['algo']."')".PHP_EOL;
    }
//echo "INSERT INTO pools_stat (coin_name, symbol, workers, shares, hashrate, estimate, 24h_blocks, timesincelast, pool, algo) VALUES ('".$res['name']."', '".$symbol."', '".$res['workers']."', '".$res['shares']."', '".$gdata[$symbol][$res['algo']]['hashrate']."', '".$res['estimate']."', '".$gdata[$symbol][$res['algo']]['block24']."', '".$res['timesincelast']."', '".$pool_name."', '".$res['algo']."')<br><br>";

}


?>