<?php

function load_config() 
{
    global $argv, $folder, $csv_suffix, $folder_s3, $tablename, $config;

    $folder      = $config[$argv[1]]['folder'];
    $csv_suffix = $config[$argv[1]]['csv_suffix'];
    $folder_s3   = $config[$argv[1]]['folder_s3'];
    $tablename = $config[$argv[1]]['tablename'];
    
}

$config['ipq_reborn_ios']['folder'] = 'ipq_reborn';
$config['ipq_reborn_ios']['csv_suffix'] = 'ios_in_app_event_organic';
$config['ipq_reborn_ios']['folder_s3'] = 'ipq_reborn_ios_organic';
$config['ipq_reborn_ios']['tablename'] = 'appsflyer_ipq_reborn_ios_in_app_event_organic';

$config['ipq_reborn_oid']['folder'] = 'ipq_reborn';
$config['ipq_reborn_oid']['csv_suffix'] = 'oid_in_app_event_organic';
$config['ipq_reborn_oid']['folder_s3'] = 'ipq_reborn_oid_organic';
$config['ipq_reborn_oid']['tablename'] = 'appsflyer_ipq_reborn_oid_in_app_event_organic';