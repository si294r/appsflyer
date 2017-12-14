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
            $pcmd = "psql --host=$rhost --port=$rport --username=$ruser --no-password --echo-all $rdatabase  -c \"\\COPY temp_json FROM '$current_dir/$filename'; \"";
            exec($pcmd, $output);
            echo "insert...";
            exec("psql --host=$rhost --port=$rport --username=$ruser --no-password --echo-all $rdatabase  -c \"
insert into {$table_name}_temp
select 
   values->>'fb_adgroup_id' as fb_adgroup_id,
   values->>'click_time_selected_timezone' as click_time_selected_timezone,
   values->>'download_time_selected_timezone' as download_time_selected_timezone,
   values->>'device_type' as device_type,
   values->>'download_time' as download_time,
   values->>'af_keywords' as af_keywords,
   values->>'attributed_touch_time' as attributed_touch_time,
   values->>'click_time' as click_time,
   values->>'agency' as agency,
   values->>'ip' as ip,
   values->>'cost_per_install' as cost_per_install,
   values->>'fb_campaign_id' as fb_campaign_id,
   values->>'is_retargeting' as is_retargeting,
   values->>'app_name' as app_name,
   values->>'re_targeting_conversion_type' as re_targeting_conversion_type,
   values->>'city' as city,
   values->>'af_sub1' as af_sub1,
   values->>'idfv' as idfv,
   values->>'af_sub2' as af_sub2,
   values->>'event_value' as event_value,
   values->>'cost_in_selected_currency' as cost_in_selected_currency,
   values->>'af_sub3' as af_sub3,
   values->>'fb_adset_name' as fb_adset_name,
   values->>'af_sub4' as af_sub4,
   values->>'customer_user_id' as customer_user_id,
   values->>'mac' as mac,
   values->>'af_sub5' as af_sub5,
   values->>'install_time_selected_timezone' as install_time_selected_timezone,
   values->>'campaign' as campaign,
   values->>'event_name' as event_name,
   values->>'event_time_selected_timezone' as event_time_selected_timezone,
   values->>'currency' as currency,
   values->>'install_time' as install_time,
   values->>'fb_adgroup_name' as fb_adgroup_name,
   values->>'attributed_touch_type' as attributed_touch_type,
   values->>'event_time' as event_time,
   values->>'platform' as platform,
   values->>'sdk_version' as sdk_version,
   values->>'appsflyer_device_id' as appsflyer_device_id,
   values->>'device_name' as device_name,
   values->>'selected_currency' as selected_currency,
   values->>'wifi' as wifi,
   values->>'media_source' as media_source,
   values->>'country_code' as country_code,
   values->>'http_referrer' as http_referrer,
   values->>'idfa' as idfa,
   values->>'fb_campaign_name' as fb_campaign_name,
   values->>'bundle_id' as bundle_id,
   values->>'click_url' as click_url,
   values->>'language' as language,
   values->>'app_id' as app_id,
   values->>'app_version' as app_version,
   values->>'attribution_type' as attribution_type,
   values->>'af_siteid' as af_siteid,
   values->>'os_version' as os_version,
   values->>'fb_adset_id' as fb_adset_id,
   values->>'event_type' as event_type
from   
(
select values::json from temp_json
) a;\"", $output);
            echo "insert2...";
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
