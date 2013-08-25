<?php
require_once("/var/www/lib/functions.php");
$offerid=intval($_GET['offer_id']);
$subid=$_GET['uid'];
$st=explode(",",$subid);
$uid=intval($st[0]);
$points=doubleval($_GET['amount'])*200;
$url="http://json999.com/cb.php?transactionID=$offerid&network=everbadge&subid=$subid&amount=".$_GET['amount'];
        exec("curl '$url' > /dev/null 2>&1 &");
echo "1";
exit;
