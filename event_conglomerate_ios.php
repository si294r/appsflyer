<?php

//var_dump($argv);

sleep(60);

include "/var/www/redshift-config2.php";
include "/var/www/appsflyer_conglomerate_ios_key.php";

if (isset($argv[1])) {
    $date = $argv[1];
} else {
    $date = date('Y-m-d', strtotime("-1 days"));
}
echo $date;

$url = "https://hq.appsflyer.com/export/id1156008236/installs_report/v5?api_token=$api_token&from=$date&to=$date";

$dir = "/var/www/html/appsflyer";
$filename = "event_conglomerate_ios_$date.csv";

redownload:
exec("wget --no-check-certificate --verbose "
        . "--output-document={$GLOBALS['dir']}/$filename "
        . "\"$url\"");

if (!is_file("{$GLOBALS['dir']}/$filename")) {
    goto redownload;
}

exec("s3cmd put {$GLOBALS['dir']}/$filename s3://apps-flyer/conglomerate_ios/$filename");

$table_name = "appsflyer_conglomerate_ios_in_app_event_non_organic";

$pcmd = "psql --host=$rhost --port=$rport --username=$ruser --no-password --echo-all $rdatabase  -c \"DELETE FROM {$table_name} WHERE install_time::date = '$date';\"";
//echo $pcmd;
$output = array();
exec($pcmd, $output);
echo implode("\n", $output) . "\n\n";

$pcmd = "psql --host=$rhost --port=$rport --username=$ruser --no-password --echo-all $rdatabase  -c \"COPY {$table_name} FROM 's3://apps-flyer/conglomerate_ios/{$filename}' CREDENTIALS 'aws_access_key_id={$aws_access_key_id};aws_secret_access_key={$aws_secret_access_key}' DELIMITER ',' IGNOREHEADER 1 REMOVEQUOTES;\"";
//echo $pcmd;
$output = array();
exec($pcmd, $output);
echo implode("\n", $output) . "\n\n";

unlink("{$GLOBALS['dir']}/$filename");
