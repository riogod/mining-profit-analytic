<?php

$mysqli = new mysqli("localhost", "root", "", "miningstat");
if ($mysqli->connect_errno) {
    printf("Не удалось подключиться: %s\n", $mysqli->connect_error);
    exit();
}

$url = "https://coinlib.io/coin/RVN/";

$contents = file_get_contents($url);
$contents = utf8_encode($contents);
$results = json_decode($contents);


print($contents);





/*
foreach ($results->Markets as $res) {


//print_r($res);

    $result = $mysqli->query("SELECT id FROM coin_prices WHERE coin_name='".$res->Name."' ");
    $symb_arr = explode('/', $res->Label);

    if ($result->num_rows <= 0) {


          $mysqli->query("INSERT INTO coin_prices (id, coin_name, symbol, price_btc, price_usd, not_in) VALUES (NULL, '".$res->Name."', '".$symb_arr[0]."', '".$res->Price_btc."', '".$res->Price_usd."', NULL)");

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


        $mysqli->query("INSERT INTO coin_prices (id, coin_name, symbol, price_btc, price_usd, not_in) VALUES (NULL, '".$res->name."', '".$res->symbol."', '".$res->price_btc."', '".$res->price_usd."', NULL)");

    } else {

        $mysqli->query("UPDATE coin_prices SET price_btc = '".$res->price_btc."', price_usd = '".$res->price_usd."', not_in=NULL WHERE coin_prices.coin_name = '".$res->name."'");

    }
    unset($symb_arr);

}
*/

?>