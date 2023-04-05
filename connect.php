<?php
$mysql_link = new mysqli('localhost', 'root', '', 'web_summer2021');

if (mysqli_connect_error()) 
{
    die('Connect Error (' . mysqli_connect_errno() . ') '. mysqli_connect_error());
}

$mysql_link->query ('SET CHARACTER SET utf8');
$mysql_link->query ('SET COLLATION_CONNECTION=utf8_general_ci');
?>
