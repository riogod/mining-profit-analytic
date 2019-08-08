<?php

//$mysqli = new mysqli_connect('localhost', 'root', '');

$mysqli = new mysqli("localhost", "root", "", "miningstat");
if ($mysqli->connect_errno) {
    printf("Не удалось подключиться: %s\n", $mysqli->connect_error);
    exit();
}



/*
$mysqli = new mysqli("localhost", "root", "", "miningstat");

if (!$msq) {
    die('Ошибка соединения: ' . mysqli_error());
}


$db_selected = mysqli_select_db ($msq,'miningstat');
if (!$db_selected) {
    die ('Не удалось выбрать базу foo: ' . mysqli_error());
}
*/
include('func.php');


?>