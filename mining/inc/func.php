<?php


function COIN_check_if_exit($coin_name, $coin_pool, $algo, $symbol , $mysqli)
{
    //print("SELECT id FROM coins WHERE coin_name='".$coin_name."' AND pool_name='".$coin_pool."' AND algo='".$algo."'<br>");
    $result = $mysqli->query("SELECT id FROM coins WHERE symbol='".$symbol."' AND pool_name='".$coin_pool."' AND algo='".$algo."'");


    if (strpos($symbol, '-') !== false) // именно через жесткое сравнение
    {
        $symbol = strstr($symbol, '-', true);
    }
//print_r($result);
    if ($result->num_rows <= 0) {
        $mysqli->query("INSERT INTO coins (coin_name, pool_name, algo, symbol) VALUES ('".$coin_name."', '".$coin_pool."', '".$algo."', '".$symbol."')");
        //print("INSERT INTO coins (coin_name, pool_name, algo, symbol) VALUES ('".$coin_name."', '".$coin_pool."', '".$algo."', '".$symbol."')<br>");
        if($mysqli->affected_rows <= 0)
        {
            return "Error(coins): VALUES ('".$coin_name."', '".$coin_pool."', '".$algo."', '".$symbol."')".PHP_EOL;
        } else {
            return true;
        }
    }


}

function find_cur_symbol($cur_name, $mysqli)
{
 //   echo "SELECT symbol FROM coin_prices WHERE coin_name LIKE '".$cur_name."' LIMIT 1";
    $result = $mysqli->query("SELECT symbol FROM coin_prices WHERE coin_name LIKE '".$cur_name."' or coin_name LIKE '".$cur_name."coin' LIMIT 1");
    if ($result->num_rows <= 0) {
    return false;
    } else {
    $row = $result->fetch_array(MYSQLI_NUM);

    return $row[0];
    }
}

function find_cur_name($cur_symb, $mysqli)
{
    //   echo "SELECT symbol FROM coin_prices WHERE coin_name LIKE '".$cur_name."' LIMIT 1";
    $result = $mysqli->query("SELECT coin_name FROM coins WHERE symbol LIKE '".$cur_symb."' LIMIT 1");
    if ($result->num_rows <= 0) {
        return false;
    } else {
        $row = $result->fetch_array(MYSQLI_NUM);

        return $row[0];
    }
}

function check_cur_symbol($cur_symbol, $mysqli)
{
    //   echo "SELECT symbol FROM coin_prices WHERE coin_name LIKE '".$cur_name."' LIMIT 1";
    $result = $mysqli->query("SELECT id FROM coin_prices WHERE symbol = '".$cur_symbol."'  LIMIT 1");
    if ($result->num_rows <= 0) {
        return false;
    } else {

        return true;
    }
}

function update_cur_reward($cur_name, $mysqli)
{
    //   echo "SELECT symbol FROM coin_prices WHERE coin_name LIKE '".$cur_name."' LIMIT 1";
    $result = $mysqli->query("SELECT symbol FROM coin_prices WHERE coin_name LIKE '".$cur_name."' or coin_name LIKE '".$cur_name."coin' LIMIT 1");
    if ($result->num_rows <= 0) {
        return false;
    } else {
        $row = $result->fetch_array(MYSQLI_NUM);

        return $row[0];
    }
}

function get_mpos_data($coin_name, $symbkey, $cur_block, $not_in="NULL", $mysqli)
{

    if (strpos($symbol, '-') !== false)
    {
        $symbol = strstr($symbol, '-', true);
    }

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, $agent);
    curl_setopt($ch, CURLOPT_URL, $cur_block);


    $data = curl_exec($ch);
    curl_close($ch);



//echo $cur_block.'<br>';
    preg_match('/<\/span><\/td><td>[0-9]*<\/td><td>(.*)<\/td><td>Generat/', $data, $matches);

    if($matches[1] > 0) {
        $mysqli->query("INSERT INTO coin_prices (coin_name, symbol, reward_per_b, not_in) VALUES ('".$coin_name."', '".$symbkey."', ".$matches[1].", ".$not_in.") ON DUPLICATE KEY UPDATE reward_per_b=".$matches[1]);
//        print("INSERT INTO coin_prices (coin_name, symbol, reward_per_b, not_in) VALUES ('".$coin_name."', '".$symbkey."', ".$matches[1].", ".$not_in.") ON DUPLICATE KEY UPDATE reward_per_b=".$matches[1]."<br>");


        return $matches[1];
    } else {
        return false;
    }

}


function INS_coin_prices($coin_name, $symbol, $reward_per_b, $algo, $mysqli)
{

    $result = $mysqli->query("SELECT id FROM coin_prices WHERE symbol LIKE '".$symbol."' AND algo LIKE '".$algo."'");
    if ($result->num_rows <= 0) {
        $mysqli->query("INSERT INTO coin_prices (coin_name, symbol, reward_per_b, algo) VALUES ('" . $coin_name . "', '" . $symbol . "', " . $reward_per_b . ", '" . $algo . "')" );
        return true;
    } else {
        $mysqli->query("UPDATE coin_prices SET reward_per_b = ".$reward_per_b."  WHERE symbol LIKE '".$symbol."' AND algo LIKE '".$algo."'");

        return false;
    }


}


?>