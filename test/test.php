
<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);

include "vendor/autoload.php";

$handler = new \obray\handlers\BaseHandler();

$context = new \obray\StreamContext();
$socketClient = new \obray\SocketClient('tcp', '10.5.2.77', 9100, $context);
$socketClient->connect($handler);