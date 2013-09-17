<?php
date_default_timezone_set("UTC");
echo date("Y-m-d H:i:s");
require_once("/var/www/lib/functions.php");
echo rows2table(db::rows("select a.uid,appid,uploaded_picture,a.created,floor(a.Amount/10) as userpay,a.revenue,a.network,b.name from sponsored_app_installs a join apps b on a.appid=b.id where network!='santa' order by created desc limit 100"));
