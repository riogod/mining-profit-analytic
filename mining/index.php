<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4-4.0.0/jq-3.2.1/dt-1.10.16/datatables.min.css"/>


    <script type="text/javascript" src="https://cdn.datatables.net/v/bs4-4.0.0/jq-3.2.1/dt-1.10.16/datatables.min.js"></script>


</head>
<body>

<?php

$my_algo = array(
    'phi1612' => 705,
    'phi' => 705,
    'c11' => 518,
    'bitcore' => 603,
    'skein' => 21450, // 5634 1080TI 851.97265533846 монет (25.80536567864 USD / 0.0029733845671312 BTC)
    'tribus' => 2300,
    'neoscrypt' => 28.2, //28.2,
    'lyra2v2' => 14.28,
    'lyra2z' => 74.4,
    'x16r' => 430,
    'blake2s' => 161.16,
    'x17' => 441.6,
    'skunk' => 1194,
    'x11' => 547,
    'nist5' => 1700,
    'equihash' => 18300,
    'groestl' => 1596,
    'hsr' => 384,
    'timetravel' => 1062,
    'x11evo' => 498,
    'blake2s' => 149220,
    'hmq1725' => 177.6,
    'quark' => 1086,
    'keccakc' => 31800,
    'allium' => 159.6,
    'xevan' => 124.2


);



$mysqli = new mysqli("localhost", "root", "", "miningstat");
if ($mysqli->connect_errno) {
    printf("Не удалось подключиться: %s\n", $mysqli->connect_error);
    exit();
}



$start = microtime(true);

$coins_arr = array();



$get_data = $mysqli->query('SELECT 
	cpr.price_btc,
    cpr.price_usd,
    cpr.reward_per_b,
	pls.symbol, 
    pls.pool,    
    AVG(hashrate) AS hashrateAvg,
    AVG(pls.24h_blocks) AS 24h_blocks,
    cns.algo,
    cns.date_add,
    cns.coin_name
   
FROM 
	pools_stat pls,
    coin_prices cpr
    	LEFT JOIN
        	coins cns
        USING(symbol, algo)
WHERE 
	pls.symbol LIKE cpr.symbol
	AND 
	pls.algo LIKE cpr.algo
    AND
    cpr.reward_per_b IS NOT NULL 
 	AND 
 	pls.dateadd >= now() - interval 1 day 
GROUP by pls.pool, pls.symbol, cns.algo');



while ($row = $get_data->fetch_row()) {


    $coins_arr[$row[3]][$row[7]][$row[4]] = Array(
        'avgHash' => $row[5],
        'dateadd' => $row[8],
        'reward' => $row[2],
        'btc'   => $row[0],
        'usd'   => $row[1],
        '24h_blocks' => $row[6],
        'coin_name' =>$row[9]
    );


}



foreach ($coins_arr as $symb=>$algos)
{
    foreach ($algos as $coinalgo=>$pools)
    {
        foreach ($pools as $coinpool=>$value)
        {
        unset($err);
        unset($errno);

        unset($calc_rev);
            $myhash = $my_algo[$coinalgo]*1000*1000;

   //        echo '(('.$myhash.'/'.($value['avgHash']+$myhash).')*'.($value['24h_blocks']/24).'*'.$value['reward'].'<br>';

            if($myhash <= 0)
            { $errno[] = 1;$err .= '<font color=red>Нулевое значение для собственного хэшрейта!</font><br>';}

            if($myhash > $value['avgHash'])
            {   $errno[] = 2;$err .= '<font color=red>Свой хешрейт больше пула, расчет не верен!</font><br>';}
            if($value['24h_blocks'] <= 0)
            {  $errno[] = 3;$err .= '<font color=red>Нулевое значение для колличества блоков пула!</font><br>';}

            if($value['avgHash'] <= 0)
            {   $errno[] = 4;$err .= '<font color=red>Нулевое значение для хэшрейта пула!</font><br>';}

            if($value['reward'] <= 0)
            {  $errno[] = 5; $err .= '<font color=red>Нулевое значение для награды за блок!</font><br>';}
            if($value['usd'] <= 0 OR $value['btc'] <= 0)
            {  $errno[] = 6;$err .= '<font color=red>Нулевое значение цены монеты!</font><br>';}



            if(!isset($err))
            {


                $calc_rev = (($myhash) / ($value['avgHash']+$myhash)) * ($value['24h_blocks']/24) * $value['reward'];

            }



            if(!isset($coin_gdata[$symb][$coinalgo]['primary']))
            {
                $coin_gdata[$symb][$coinalgo] = array(
                    'coin_name' => $value['coin_name'],
                    'symbol' => $symb,
                    'algo' => $coinalgo,
                    'myhash' => $myhash,
                    'reward' =>  $value['reward'],
                    'price_btc' => $value['btc'],
                    'price_usd' => $value['usd'],
                    'primary' => array(
                        'pool' => $coinpool,
                        'block_24' => $value['24h_blocks'],
                        'avgPoolHash' => $value['avgHash'],
                        'profit_coins' => $calc_rev*24,
                        'profit_usd' => ($value['usd'] * $calc_rev)*24,
                        'profit_btc' => ($value['btc'] * $calc_rev)*24,
                        'techdata' => 'Blocks:'.$value['24h_blocks'].', PoolHash:'.$value['avgHash'].', Myhash: '.$myhash.', price:'.$value['usd'].', Reward:'.$value['reward'],
                        'err' => $errno
                    )

                );

            } else {

                if($coin_gdata[$symb][$coinalgo]['primary']['profit_usd'] < ($value['btc'] * $calc_rev)*24)
                {
                    $coin_gdata[$symb][$coinalgo]['alt'][$coin_gdata[$symb][$coinalgo]['primary']['pool']] = array(
                            'pool' => $coin_gdata[$symb][$coinalgo]['primary']['pool'],
                            'block_24' => $coin_gdata[$symb][$coinalgo]['primary']['block_24'],
                            'avgPoolHash' => $coin_gdata[$symb][$coinalgo]['primary']['avgPoolHash'],
                            'profit_coins' => $coin_gdata[$symb][$coinalgo]['primary']['profit_coins'],
                            'profit_usd' => $coin_gdata[$symb][$coinalgo]['primary']['profit_usd'],
                            'profit_btc' => $coin_gdata[$symb][$coinalgo]['primary']['profit_btc'],
                            'profit_btc' => $coin_gdata[$symb][$coinalgo]['primary']['techdata'],
                            'err' => $coin_gdata[$symb][$coinalgo]['primary']['err']
                    );
                    $coin_gdata[$symb][$coinalgo]['primary'] = array(
                        'pool' => $coinpool,
                        'block_24' => $value['24h_blocks'],
                        'avgPoolHash' => $value['avgHash'],
                        'profit_coins' => $calc_rev*24,
                        'profit_usd' => ($value['usd'] * $calc_rev)*24,
                        'profit_btc' => ($value['btc'] * $calc_rev)*24,
                        'techdata' => 'Blocks:'.$value['24h_blocks'].', PoolHash:'.$value['avgHash'].', Myhash: '.$myhash.', price:'.$value['usd'].', Reward:'.$value['reward'],
                        'err' => $errno
                    );
                } else {
                    $coin_gdata[$symb][$coinalgo]['alt'][$coinpool] = array(
                        'pool' => $coinpool,
                        'block_24' => $value['24h_blocks'],
                        'avgPoolHash' => $value['avgHash'],
                        'profit_coins' => $calc_rev*24,
                        'profit_usd' => ($value['usd'] * $calc_rev)*24,
                        'profit_btc' => ($value['btc'] * $calc_rev)*24,
                        'techdata' => 'Blocks:'.$value['24h_blocks'].', PoolHash:'.$value['avgHash'].', Myhash: '.$myhash.', price:'.$value['usd'].', Reward:'.$value['reward'],
                        'err' => $errno
                    );
                }


            }


        }

    }


}




echo '<table id="example" class="table table-striped table-bordered" style="width:100%">
    <thead>
    <tr>
        <th>Coin</th>
        <th>Symbol</th>
        <th>Pool</th>
        <th>Coin profit</th>
        <th>USD profit</th>
        <th>BTC profit</th>
        <th>Additional Pools</th>
        <th>err</th>
    </tr>
    </thead>
    <tbody>';




foreach ($coin_gdata as $symbol=>$coin_algos)
{
    foreach ($coin_algos as $algo=>$coin_data)
    {
        unset($errc);
        if($coin_data['primary']['err'] != '' AND count($coin_data['primary']['err']) > 0)
        {
            foreach ($coin_data['primary']['err'] as $error_coin)
            {

                switch ($error_coin) {
                    case 1:
                        $errc .= '&nbsp;<span class="badge badge-danger">MyHashRate=0</span>';break;
                    case 2:
                        $errc .= '&nbsp;<span class="badge badge-danger">MyHashRate > PoolHashRate</span>';break;
                    case 3:
                        $errc .= '&nbsp;<span class="badge badge-danger">0 blocks for 24h</span>';break;
                    case 4:
                        $errc .= '&nbsp;<span class="badge badge-danger">PoolHashRate=0</span>';break;
                    case 5:
                        $errc .= '&nbsp;<span class="badge badge-danger">Unknown reward</span>';break;
                    case 6:
                        $errc .= '&nbsp;<span class="badge badge-danger">Unknown Coin price</span>';break;
                    default:
                        $errc .= '';break;

                }
            }
        }
        if($coin_data['myhash']/1000/1000 > 1000)
        {
            $mshash = ($coin_data['myhash']/1000/1000/1000).' G/h';
        } else {
            $mshash = ($coin_data['myhash']/1000/1000).' M/h';
        }

        echo '<tr>
                <td>'.$coin_data['coin_name'].'<div class="hero-unit"> <span class="badge badge-primary">'.$algo.'</span>
        <span class="badge badge-success">MyHashRate: '.$mshash.'</span></div></td>
                <td>'.$symbol.'</td>
                <td>'.$coin_data['primary']['pool'].'</td>
                <td>'.$coin_data['primary']['profit_coins'].'</td>
                <td>'.$coin_data['primary']['profit_usd'].'</td>
                <td>'.$coin_data['primary']['profit_btc'].'</td>
                <td>'.$coin_data['primary']['techdata'].' </td>
                <td><div class="z-unit">'.$errc.'</div></td>
            </tr>';

    }

}









echo '    </tfoot>
</table>
';


$finish = microtime(true);

$delta = $finish - $start;

echo $delta . ' сек.';



?>

<script>

    $(document).ready(function() {
        $('#example').DataTable();
    } );
</script>
</body>
</html>
