<?php

//var_dump($argv);

sleep(60);

$current_dir = dirname(__FILE__);

include "$current_dir/../postgres-config.php";
include "$current_dir/../appsflyer_api_key.php";
//include "/var/www/redshift-config2.php";
//include "/var/www/appsflyer_api_key.php";

include 'config.php';
load_config();

if (isset($argv[2])) {
    $date = $argv[2];
} else {
    $date = date('Y-m-d', strtotime("-1 days"));
}
echo $date;

$url = "https://hq.appsflyer.com/export/{$app_id}/installs_report/{$api_version}?api_token=$api_token&from=$date&to=$date";

$dir = "/var/www/html/appsflyer";
$filename = "{$csv_prefix}$date.csv";

redownload:
exec("wget --no-check-certificate --verbose "
        . "--output-document={$GLOBALS['dir']}/$filename "
        . "\"$url\"");

if (!is_file("{$GLOBALS['dir']}/$filename")) {
    goto redownload;
}

exec("s3cmd put {$GLOBALS['dir']}/$filename s3://apps-flyer/{$folder_s3}/$filename");

$table_name = $tablename;

$pcmd = "psql --host=$rhost --port=$rport --username=$ruser --no-password --echo-all $rdatabase  -c \"DELETE FROM {$table_name} WHERE install_time::date = '$date';\"";
//echo $pcmd;
$output = array();
exec($pcmd, $output);
echo implode("\n", $output) . "\n\n";

$pcmd = "psql --host=$rhost --port=$rport --username=$ruser --no-password --echo-all $rdatabase  -c \"COPY {$table_name} FROM 's3://apps-flyer/{$folder_s3}/{$filename}' CREDENTIALS 'aws_access_key_id={$aws_access_key_id};aws_secret_access_key={$aws_secret_access_key}' DELIMITER ',' IGNOREHEADER 1 REMOVEQUOTES;\"";
//echo $pcmd;
$output = array();
exec($pcmd, $output);
echo implode("\n", $output) . "\n\n";

unlink("{$GLOBALS['dir']}/$filename");
