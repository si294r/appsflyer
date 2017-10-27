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

$config['cash_ios']['app_id'] = 'id1270598321';
$config['cash_ios']['folder_s3'] = 'cash_ios';
$config['cash_ios']['tablename'] = 'appsflyer_cash_ios_in_app_event_non_organic';
$config['cash_ios']['csv_prefix'] = 'event_cash_ios_';
$config['cash_ios']['api_version'] = 'v5';

$config['billionaire2_ios']['app_id'] = 'id1255102262';
$config['billionaire2_ios']['folder_s3'] = 'billionaire2_ios';
$config['billionaire2_ios']['tablename'] = 'appsflyer_billionaire2_ios_in_app_event_non_organic';
$config['billionaire2_ios']['csv_prefix'] = 'event_billionaire2_ios_';
$config['billionaire2_ios']['api_version'] = 'v5';

$config['cash_oid']['app_id'] = 'com.alegrium.cong2';
$config['cash_oid']['folder_s3'] = 'cash_oid';
$config['cash_oid']['tablename'] = 'appsflyer_cash_oid_in_app_event_non_organic';
$config['cash_oid']['csv_prefix'] = 'event_cash_oid_';
$config['cash_oid']['api_version'] = 'v5';
