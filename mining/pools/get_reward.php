<?php

//include('../inc/inc.php');


/*
$contents = [];
$files = scandir('./'); //присваиваем переменной массив с листингом директории
foreach($files as $file) //проходим по массиву
{
   if($file != "." or $file != ".." or $file != "_TMP")
   {
    echo '-'.$file.'-<br>';

   }


}
    if(!is_dir('./' . $file)) //если это файл, а не папка
        $contents[] = file_get_contents('./' . $file); //добавляем содержимое файла в массив


*/
if(!isset($_GET['counter']))
{
    $_GET['counter'] = 0;
}


$result = array();


$cdir = scandir('.');
foreach ($cdir as $key => $value)
{
    if (!in_array($value,array(".","..", "_TMP")))
    {
        if (is_dir($value))
        {
            if (file_exists('./'.$value.'/pool_reward.php')) {
                $result[] = $value;
            }

        }
    }
}
//echo  count($result);


if($_GET['counter'] < count($result)) {

    opendir($result[$_GET['counter']]);
include('./'.$result[$_GET['counter']].'/pool_reward.php');


    $fp = fopen('data.txt', 'a');

    fwrite($fp, 'Обработан файл '.$result[$_GET['counter']].' в '.date("H:i:s").'
    ');
    fclose($fp);



$counter = $_GET['counter']+1;
header('Location: /pools/get_reward.php?counter='.$counter);
} else {

    echo "END.";
}


?>