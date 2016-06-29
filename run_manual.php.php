<?php

for ($i=31; $i>=1; $i--) {
    $cmd = "php -f /var/www/html/appsflyer/daily_report_ios.php 2016-05-".str_pad($i, 2, "0", STR_PAD_LEFT);
//    echo $cmd."\n"; continue;
    exec($cmd); 
    sleep(60);
}

for ($i=31; $i>=1; $i--) {
    $cmd = "php -f /var/www/html/appsflyer/partners_daily_report_ios.php 2016-05-".str_pad($i, 2, "0", STR_PAD_LEFT);
//    echo $cmd."\n"; continue;
    exec($cmd); 
    sleep(60);
}

