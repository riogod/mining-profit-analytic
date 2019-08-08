<?php

$mysqli = new mysqli("localhost", "root", "", "miningstat");
if ($mysqli->connect_errno) {
    printf("Не удалось подключиться: %s\n", $mysqli->connect_error);
    exit();
}


$url = "https://api.coinmarketcap.com/v1/ticker/bitcoin/";

$contents = file_get_contents($url);
$contents = utf8_encode($contents);
$results = json_decode($contents , true);
$usd_price = $results[0]['price_usd'];

/*
 SELECT
    MAX(coin_pricehistory.date),
    coin_pricehistory.symbol,
    coin_pricehistory.exchange,
    coin_pricehistory.ask,
    coin_pricehistory.bid,
    coin_pricehistory.last,
    coin_pricehistory.volume
FROM
    coin_pricehistory
GROUP BY
    coin_pricehistory.symbol,
    coin_pricehistory.exchange
 */

$get_data = $mysqli->query('SELECT 
    MAX(coin_pricehistory.date), 
    coin_pricehistory.symbol, 
    coin_pricehistory.exchange, 
    coin_pricehistory.ask
FROM 
    coin_pricehistory 
GROUP BY 
    coin_pricehistory.symbol, 
    coin_pricehistory.exchange
');






while ($row = $get_data->fetch_row()) {
    $skey = strtoupper($row[1]);
    $coins_arr[$skey][$row[2]]['ask'] = $row[3];

}
print(count($coins_arr));
foreach ($coins_arr as $coin_key=>$coin_data)
{
    if(count($coin_data) > 1) {
        $coinariprice = 0;
        foreach ($coin_data as $coinari)
        {
           if($coinari['ask'] > $coinariprice)
            {   $coinariprice = $coinari['ask']; }
        }

        $mysqli->query("UPDATE coin_prices SET price_btc = '".$coinariprice."', price_usd = '".($coinariprice*$usd_price)."' WHERE symbol LIKE '".$coin_key."'");
        if($mysqli->affected_rows <= 0)
        {
            $mysqli->query("INSERT INTO coin_prices (id, coin_name, symbol, price_btc, price_usd) VALUES (NULL, '".$coin_key."', '".$coin_key."', '".$coinariprice."', '".($coinariprice*$usd_price)."');");
        }



        unset($coinariprice);
    } else {

        $tmp_coindat = array_shift($coin_data);
        $mysqli->query("UPDATE coin_prices SET price_btc = '".$tmp_coindat['ask']."', price_usd = '".($tmp_coindat['ask']*$usd_price)."' WHERE symbol LIKE '".$coin_key."'");
        if($mysqli->affected_rows <= 0)
        {
            $mysqli->query("INSERT INTO coin_prices (id, coin_name, symbol, price_btc, price_usd) VALUES (NULL, '".$coin_key."', '".$coin_key."', '".$tmp_coindat['ask']."', '".($tmp_coindat['ask']*$usd_price)."');");
        }
    }


}










?>