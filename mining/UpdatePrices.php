<?php

$mysqli = new mysqli("localhost", "root", "", "miningstat");
if ($mysqli->connect_errno) {
    printf("Не удалось подключиться: %s\n", $mysqli->connect_error);
    exit();
}

$url = "https://www.worldcoinindex.com/apiservice/json?key=x5psVn3YRzi0i3KTDbVZWCKc5vQuvX";

$contents = file_get_contents($url);
$contents = utf8_encode($contents);
$results = json_decode($contents);


//print_r($results->Markets);

foreach ($results->Markets as $res) {


//print_r($res);

    $result = $mysqli->query("SELECT id FROM coin_prices WHERE coin_name='".$res->Name."' ");
    $symb_arr = explode('/', $res->Label);

    if ($result->num_rows <= 0) {


 //         $mysqli->query("INSERT INTO coin_prices (id, coin_name, symbol, price_btc, price_usd, not_in) VALUES (NULL, '".$res->Name."', '".$symb_arr[0]."', '".$res->Price_btc."', '".$res->Price_usd."', NULL)");

    } else {

        $mysqli->query("UPDATE coin_prices SET price_btc = '".$res->Price_btc."', price_usd = '".$res->Price_usd."', not_in=NULL WHERE coin_prices.coin_name = '".$res->Name."'");
//        echo "UPDATE coin_prices SET price_btc = '".$res->Price_btc."', price_usd = '".$res->Price_usd."', not_in=NULL WHERE coin_prices.coin_name = ".$res->Name."<br>";
    }
    unset($symb_arr);

}



$url = "https://api.coinmarketcap.com/v1/ticker/?limit=10000";

$contents = file_get_contents($url);
$contents = utf8_encode($contents);
$results = json_decode($contents);




foreach ($results as $res) {


//print_r($res);

    $result = $mysqli->query("SELECT id FROM coin_prices WHERE coin_name='".$res->name."' ");
;

    if ($result->num_rows <= 0) {


  //      $mysqli->query("INSERT INTO coin_prices (id, coin_name, symbol, price_btc, price_usd, not_in) VALUES (NULL, '".$res->name."', '".$res->symbol."', '".$res->price_btc."', '".$res->price_usd."', NULL)");

    } else {

        $mysqli->query("UPDATE coin_prices SET price_btc = '".$res->price_btc."', price_usd = '".$res->price_usd."', not_in=NULL WHERE coin_prices.coin_name = '".$res->name."'");

    }
    unset($symb_arr);

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
    a.date, 
    a.symbol, 
    a.exchange, 
    a.bid
FROM 
    coin_pricehistory AS a
WHERE
	a.date = (
                            SELECT MAX(date)
                            FROM coin_pricehistory AS b
                            WHERE a.symbol = b.symbol
                            AND a.exchange = b.exchange
						)
GROUP BY 
    a.symbol, 
    a.exchange
');






while ($row = $get_data->fetch_row()) {
    $skey = strtoupper($row[1]);
    $coins_arr[$skey][$row[2]]['bid'] = $row[3];

}

foreach ($coins_arr as $coin_key=>$coin_data)
{
    if(count($coin_data) > 1) {
        $coinariprice = 0;
        foreach ($coin_data as $coinari)
        {
            if($coinari['bid'] > $coinariprice)
            {   $coinariprice = $coinari['bid']; }
        }

       $mysqli->query("UPDATE coin_prices SET price_btc = '".$coinariprice."', price_usd = '".($coinariprice*$usd_price)."' WHERE symbol LIKE '".$coin_key."'");

        if($mysqli->affected_rows <= 0)
        {
  //          $mysqli->query("INSERT INTO coin_prices (id, coin_name, symbol, price_btc, price_usd) VALUES (NULL, '".$coin_key."', '".$coin_key."', '".$coinariprice."', '".($coinariprice*$usd_price)."')");

        }



        unset($coinariprice);
    } else {

        $tmp_coindat = array_shift($coin_data);
        $mysqli->query("UPDATE coin_prices SET price_btc = '".$tmp_coindat['bid']."', price_usd = '".($tmp_coindat['bid']*$usd_price)."' WHERE symbol LIKE '".$coin_key."'");
        if($mysqli->affected_rows <= 0)
        {
//            $mysqli->query("INSERT INTO coin_prices (id, coin_name, symbol, price_btc, price_usd) VALUES (NULL, '".$coin_key."', '".$coin_key."', '".$tmp_coindat['bid']."', '".($tmp_coindat['bid']*$usd_price)."');");
        }
    }


}

?>