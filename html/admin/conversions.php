<?php
require_once("/var/www/lib/functions.php");
echo rows2table(db::rows("select a.*,b.name from sponsored_app_installs a join apps b on a.appid=b.id where network!='santa' order by created desc limit 100"));
