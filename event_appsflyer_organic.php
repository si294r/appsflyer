<?php

include 'config_organic.php';
load_config();

if (isset($argv[2])) {
    $tanggal = $argv[2];
} else {
    $tanggal = date('Ymd', strtotime("-1 days"));
}
echo $tanggal;

$dir = "/var/www/html/appsflyer/$folder";

for ($i=0; $i<=23; $i++) {
    $filename = $tanggal. str_pad($i, 2, "0", STR_PAD_LEFT) .".{$txt_suffix}.txt";
    $cmd_s3 = "s3cmd put {$GLOBALS['dir']}/$filename s3://apps-flyer/$folder_s3/$filename";
    exec($cmd_s3);
//    echo $cmd_s3;
}

//die;
include "/var/www/redshift-config2.php";
$table_name = $tablename;

$pcmd = "psql --host=$rhost --port=$rport --username=$ruser --no-password --echo-all $rdatabase  -c \"TRUNCATE TABLE {$table_name}_temp ;\"";
//echo $pcmd;

$output = array();
exec($pcmd, $output);
echo implode("\n", $output) . "\n\n";

$pcmd = "psql --host=$rhost --port=$rport --username=$ruser --no-password --echo-all $rdatabase  -c \"COPY {$table_name}_temp FROM 's3://apps-flyer/{$folder_s3}/{$tanggal}' CREDENTIALS 'aws_access_key_id={$aws_access_key_id};aws_secret_access_key={$aws_secret_access_key}' FORMAT AS JSON 'auto';\"";
//echo $pcmd;

$output = array();
exec($pcmd, $output);
echo implode("\n", $output) . "\n\n";

$pcmd = "psql --host=$rhost --port=$rport --username=$ruser --no-password --echo-all $rdatabase  -c \"INSERT INTO {$table_name} (SELECT '{$tanggal}', * FROM {$table_name}_temp) ;\"";
//echo $pcmd;

$output = array();
exec($pcmd, $output);
echo implode("\n", $output) . "\n\n";
