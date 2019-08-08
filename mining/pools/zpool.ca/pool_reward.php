<?php


set_time_limit(3600);
//$urli = 'https://zpool.ca/site/block_results?id=1575';
$urli = 'https://zpool.ca/site/mining_results?global_algo=nist5';
//$urli = 'https://zpool.ca/site/gomining?algo=c11';
$urli = 'https://zpool.ca/site/gomining';

include('init.php');

//$strCookie = 'PHPSESSID=qse6qusc14eol2n6ksf969mii5; path=/';


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
    $gdata[$pdk] = array(
        'reward' => $hsr[2][$key],
        'name' => $hsr[1][$key],
        'hashrate' => $hsr[4][$key],
        'algo' => $algo
    );

}




}




$tofind_arr = array();


foreach ($gdata as $skey => $res) {
//    echo '<br><br>'.$skey.':<br>----------------------<br>';



    INS_coin_prices($res['name'], $skey, $res['reward'], $res['algo'], $mysqli);
  //      $mysqli->query("INSERT INTO coin_prices (coin_name, symbol, reward_per_b, algo) VALUES ('".$res['name']."', '".$skey."', ".$res['reward'].", '".$res['algo']."' ) ON DUPLICATE KEY UPDATE reward_per_b=".$res['reward']);
 //       echo ' Coin Name: ' . $res['name'] . '('.$skey.') <br> Reward: ' . $res['reward'] . "<br>";

}








//print_r($gdata);

/*
https://zpool.ca/site/algo?algo=nist5&r=/




*/


?>