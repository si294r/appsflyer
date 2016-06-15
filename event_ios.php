<?php

//var_dump($argv);

include "/var/www/redshift-config.php";

if (isset($argv[1])) {
    $date = $argv[1];
} else {
    $date = date('Y-m-d', strtotime("-1 days"));
}
echo $date;

$url = "https://hq.appsflyer.com/export/id881342787/installs_report/v4?api_token=9b243088-f629-40e3-9f26-3c35f30e13b6&from=$date&to=$date";

$dir = "/var/www/html/appsflyer";
$filename = "event_ios_$date.csv";

redownload:
exec("wget --no-check-certificate --verbose "
        . "--output-document={$GLOBALS['dir']}/$filename "
        . "\"$url\"");

if (!is_file("{$GLOBALS['dir']}/$filename")) {
    goto redownload;
}

exec("s3cmd put {$GLOBALS['dir']}/$filename s3://apps-flyer/ios/$filename");

$table_name = "appsflyer_ios_in_app_event_non_organic_new";

$pcmd = "psql --host=$rhost --port=$rport --username=$ruser --no-password --echo-all $rdatabase  -c \"DELETE FROM {$table_name} WHERE install_time::date = '$date';\"";
//echo $pcmd;
$output = array();
exec($pcmd, $output);
echo implode("\n", $output) . "\n\n";

$pcmd = "psql --host=$rhost --port=$rport --username=$ruser --no-password --echo-all $rdatabase  -c \"COPY {$table_name} FROM 's3://apps-flyer/ios/{$filename}' CREDENTIALS 'aws_access_key_id={$aws_access_key_id};aws_secret_access_key={$aws_secret_access_key}' DELIMITER ',' IGNOREHEADER 1 REMOVEQUOTES;\"";
//echo $pcmd;
$output = array();
exec($pcmd, $output);
echo implode("\n", $output) . "\n\n";

unlink("{$GLOBALS['dir']}/$filename");
