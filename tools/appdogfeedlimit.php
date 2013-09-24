
<?php
require_once("/var/www/lib/functions.php");

$on=file_get_contents("/var/www/appdog_on");
$apt=db::row("select sum(revenue)/100 as revenue, network from sponsored_app_installs where date(created)>=date(now()) and network='appdog'");
echo "\n".date('c')." rev: ".$apt['revenue'];
if($apt['revenue']>=995 && $on!='off'){
   file_put_contents("/var/www/appdog_on","off");
   email("fassil@appdog.com","AppDog feed reached ".$apt['revenue']." on photorewards, turned off","turning back on 9am tomorrow","yisheng@grepawk.com");
   email("yisheng@grepawk.com","AppDog feed reached ".$apt['revenue']." on photorewards, turned off","turning back on 9am tomorrow","yisheng@grepawk.com");
}
if($on=="off" && $apt['revenue']<995 && date('H')==9){
    file_put_contents("/var/www/appdog_on","on");
   email("fassil@appdog.com","AppDog feed back on photorewards","rt","support@grepawk.com");
   email("yisheng@grepawk.com","AppDog feed back  on photorewards","tr","support@grepawk.com");
}

