<?php

$mysqli = new mysqli("localhost", "root", "", "miningstat");
if ($mysqli->connect_errno) {
    printf("Не удалось подключиться: %s\n", $mysqli->connect_error);
    exit();
}

$url = "https://www.coinexchange.io/api/v1/getmarkets";

$contents = file_get_contents($url);
$contents = utf8_encode($contents);
$results_mar = json_decode($contents);


foreach ($results_mar->result as $res) {
    if($res->BaseCurrencyCode == 'BTC' AND $res->Active == true)
    {
        $market[$res->MarketID] = array(
            'symb' => $res->MarketAssetCode
        );
    }
}


$url = "https://www.coinexchange.io/api/v1/getmarketsummaries";

$contents = file_get_contents($url);
$contents = utf8_encode($contents);
$results = json_decode($contents);


foreach ($results->result as $res) {
    if(isset($market[$res->MarketID]))
    {
        $market[$res->MarketID]['LastPrice'] = $res->LastPrice;
        $market[$res->MarketID]['Volume'] = $res->BTCVolume;
        $market[$res->MarketID]['BidPrice'] = $res->BidPrice;
        $market[$res->MarketID]['AskPrice'] = $res->AskPrice;


    }
}












//print_r($market);

foreach ($market as $res) {

            $add .= " ('".strtoupper($res['symb'])."', ".$res['LastPrice'].", ".$res['Volume'].", ".$res['AskPrice'].", ".$res['BidPrice'].", 'coinexchange'),";


}
$mysqli->query("INSERT INTO coin_pricehistory (	symbol, last, volume, ask, bid, exchange) VALUES".substr($add,0,-1));

?>