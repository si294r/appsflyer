<?php

include 'config_organic.php';
load_config();

$current_dir = dirname(__FILE__);

include "$current_dir/../postgres-config.php";

if (isset($argv[2])) {
    $startdate = $argv[2];
    if (isset($argv[3])) {
        $enddate = $argv[3];
        if ($enddate < $startdate) {
            $enddate = $startdate;
        }
    } else {
        $enddate = date('Ymd');
    }
} else {
    $startdate = date('Ymd');
    $enddate = date('Ymd');
}

$obj_date = DateTime::createFromFormat('Ymd', $startdate);

while (true) {

    $tanggal = $obj_date->format('Ymd');

    echo $tanggal . "...";
    $ls_output = [];
    exec("aws s3 ls s3://apps-flyer/$folder_s3/$tanggal", $ls_output);

    if (count($ls_output) > 0) {
        echo "found".PHP_EOL;        
        foreach ($ls_output as $row) {

            $re = '/' . $tanggal . '.*/';
            $str = $row;
            preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);

            if (!isset($matches[0][0]))
                continue;
            $filename = $matches[0][0];

            echo $filename." download...";
            exec("aws s3 cp s3://apps-flyer/$folder_s3/$filename $current_dir/$filename");

            $table_name = $tablename;
            $output = array();

            echo "truncate...";
            $pcmd = "psql --host=$rhost --port=$rport --username=$ruser --no-password --echo-all $rdatabase  -c \"DELETE FROM {$table_name} WHERE import_date = '{$tanggal}' ; TRUNCATE TABLE {$table_name}_temp ;\"";
            exec($pcmd, $output);

            echo "copy...";
            //    $pcmd = "psql --host=$rhost --port=$rport --username=$ruser --no-password --echo-all $rdatabase  -c \"COPY {$table_name}_temp FROM 's3://apps-flyer/{$folder_s3}/{$tanggal}'  FORMAT AS JSON 'auto';\"";
            //    exec($pcmd, $output);

            echo "insert...";
            $pcmd = "psql --host=$rhost --port=$rport --username=$ruser --no-password --echo-all $rdatabase  -c \"INSERT INTO {$table_name} (SELECT '{$tanggal}', * FROM {$table_name}_temp) ;\"";
            exec($pcmd, $output);

            echo "done" . PHP_EOL;
            echo implode(PHP_EOL, $output) . PHP_EOL . PHP_EOL;

            unlink("$current_dir/$filename"); // cleanup
        }
    } else {
        echo "not found".PHP_EOL;
    }

    if ($obj_date->format('Ymd') == $enddate) {
        break;
    } else {
        $obj_date->add(new DateInterval("P1D"));
    }
}
