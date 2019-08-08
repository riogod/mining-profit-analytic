<?php

$mysqli = new mysqli("localhost", "root", "", "miningstat");
if ($mysqli->connect_errno) {
    printf("Не удалось подключиться: %s\n", $mysqli->connect_error);
    exit();
}


if(isset($_POST['reward']) or isset($_POST['no_reward']))
{

    if($_POST['no_reward'] == 1)
    {
        $str_add = ", no_reward = '1'";
    }
//echo "UPDATE coin_prices SET reward_per_b = '".$_POST['reward']."'".$str_add." WHERE coin_prices.id = ".$_POST['coinid']."<br>";
//UPDATE `coin_prices` SET `reward_per_b` = '25', `no_reward` = '1' WHERE `coin_prices`.`id` = 11
    $mysqli->query("UPDATE `coin_prices` SET `reward_per_b` = '".$_POST['reward']."'".$str_add." WHERE `coin_prices`.`id` = ".$_POST['coinid']);

}


$result = $mysqli->query("SELECT * FROM coin_prices, coins WHERE reward_per_b IS NULL AND no_reward IS NULL AND coin_prices.symbol = coins.symbol LIMIT 1");

$data = $result->fetch_array();
//print_r($data);
if(isset($data['symbol']))
{
echo $data[1].'-('.$data['symbol'].')';

echo '<form action="" method="post">
 <p>reward: <input type="text" name="reward" />&nbsp<input type="checkbox" name="no_reward" value="1"></p>
 <p><input type="hidden" name="coinid" value="'.$data[0].'" /></p>
 <p><input type="submit" /></p>
</form><br><br><a href="https://yandex.ru/search/?text='.$data['symbol'].' Coin mining pool" target="_blank">'.$data['symbol'].' Coin mining pool</a>
<br><a href="http://'.$data['pool_name'].'" target="_blank">'.$data['pool_name'].' </a> 
<br>MPOS EXPLORER<a href="http://'.$data['pool_name'].'/explorer/'.$data['symbol'].'" target="_blank">'.$data['pool_name'].'/explorer/'.$data['symbol'].'</a>';
} else {
    echo 'No coins';
}

?>