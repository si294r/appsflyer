<?php

function load_config() 
{
    global $argv, $app_id, $folder_s3, $tablename, $csv_prefix, $api_version, $config;

    $app_id      = $config[$argv[1]]['app_id'];
    $folder_s3    = $config[$argv[1]]['folder_s3'];
    $tablename = $config[$argv[1]]['tablename'];
    $csv_prefix = $config[$argv[1]]['csv_prefix'];
    $api_version = $config[$argv[1]]['api_version'];
    
}

$config['ipq_reborn_ios']['app_id'] = 'id1217089239';
$config['ipq_reborn_ios']['folder_s3'] = 'ipq_reborn_ios';
$config['ipq_reborn_ios']['tablename'] = 'appsflyer_ipq_reborn_ios_in_app_event_non_organic';
$config['ipq_reborn_ios']['csv_prefix'] = 'event_ipq_reborn_ios_';
$config['ipq_reborn_ios']['api_version'] = 'v5';


$config['ipq_reborn_oid']['app_id'] = 'com.alegrium.ipqr';
$config['ipq_reborn_oid']['folder_s3'] = 'ipq_reborn_oid';
$config['ipq_reborn_oid']['tablename'] = 'appsflyer_ipq_reborn_oid_in_app_event_non_organic';
$config['ipq_reborn_oid']['csv_prefix'] = 'event_ipq_reborn_oid_';
$config['ipq_reborn_oid']['api_version'] = 'v5';
