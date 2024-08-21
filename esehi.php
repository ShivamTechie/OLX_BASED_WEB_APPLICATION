<?php


$pass = "jasmeet123";
$has = password_hash($pass, PASSWORD_DEFAULT);
echo $has;
