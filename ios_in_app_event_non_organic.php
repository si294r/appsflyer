<?php

$input = file_get_contents("php://input");

$filename = "./".date("YmdH").".ios_in_app_event_non_organic.txt";
$fp = fopen($filename, 'a');
fwrite($fp, $input . PHP_EOL);

echo "OK";