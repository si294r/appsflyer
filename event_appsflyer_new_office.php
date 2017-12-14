<?php

$current_dir = dirname(__FILE__);

include "$current_dir/../postgres-config.php";

include 'config.php';
load_config();

if (isset($argv[2])) {
    $date = $argv[2];
} else {
    $date = date('Y-m-d', strtotime("-1 days"));
}
echo $date;

$filename = "{$csv_prefix}$date.csv";

$ls_output = [];
exec("aws s3 ls s3://apps-flyer/{$folder_s3}/$filename", $ls_output);

if (count($ls_output) > 0) {
    
    exec("aws s3 cp s3://apps-flyer/{$folder_s3}/$filename $current_dir/$filename");

    $table_name = $tablename;

    $pcmd = "psql --host=$rhost --port=$rport --username=$ruser --no-password --echo-all $rdatabase  -c \"DELETE FROM {$table_name} WHERE install_time::date = '$date';\"";
    $output = array();
    exec($pcmd, $output);

    $pcmd = "psql --host=$rhost --port=$rport --username=$ruser --no-password --echo-all $rdatabase  -c \"\\COPY {$table_name} FROM '{$current_dir}/{$filename}' DELIMITER ',' NULL '' QUOTE '\\\"' CSV HEADER ;\"";
    exec($pcmd, $output);
    
    echo implode("\n", $output) . "\n\n";

    unlink("$current_dir/$filename");
    
}

