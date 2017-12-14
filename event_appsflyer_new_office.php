<?php

$current_dir = dirname(__FILE__);

include "$current_dir/../postgres-config.php";

include 'config.php';
load_config();

if (isset($argv[2])) {
//    $date = $argv[2];
    $startdate = $argv[2];
    if (isset($argv[3])) {
        $enddate = $argv[3];
        if ($enddate < $startdate) {
            $enddate = $startdate;
        }
    } else {
        $enddate = date('Y-m-d');
    }
} else {
//    $date = date('Y-m-d');
    $startdate = date('Y-m-d');
    $enddate = date('Y-m-d');
}

$obj_date = DateTime::createFromFormat('Y-m-d', $startdate);

while (true) {

    $date = $obj_date->format('Y-m-d');
    
    $filename = "{$csv_prefix}$date.csv";

    $ls_output = [];
    echo $date."...";
    exec("aws s3 ls s3://apps-flyer/{$folder_s3}/$filename", $ls_output);

    if (count($ls_output) > 0) {

        echo "download...";
        exec("aws s3 cp s3://apps-flyer/{$folder_s3}/$filename $current_dir/$filename");

        $table_name = $tablename;

        echo "delete...";
        $pcmd = "psql --host=$rhost --port=$rport --username=$ruser --no-password --echo-all $rdatabase  -c \"DELETE FROM {$table_name} WHERE install_time::date = '$date';\"";
        $output = array();
        exec($pcmd, $output);

        echo "insert...";
        $pcmd = "psql --host=$rhost --port=$rport --username=$ruser --no-password --echo-all $rdatabase  -c \"\\COPY {$table_name} FROM '{$current_dir}/{$filename}' DELIMITER ',' NULL '' QUOTE '\\\"' CSV HEADER ;\"";
        exec($pcmd, $output);

        echo "done".PHP_EOL;
        echo implode(PHP_EOL, $output).PHP_EOL.PHP_EOL;

        unlink("$current_dir/$filename");

    } else {
        echo "not found.".PHP_EOL.PHP_EOL;
    }

    if ($obj_date->format('Y-m-d') == $enddate) {
        break;
    } else {
        $obj_date->add(new DateInterval("P1D"));
    }
    
}

