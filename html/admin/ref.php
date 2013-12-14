<?php
require_once("/var/www/lib/functions.php");
$uid=intval($_GET['uid']);
echo rows2table(db::rows("select fbid,fbfriends,banned,idfa,mac,ipaddress,a.id,a.created,a.country,locale,b.created as enteredbonus,deviceInfo,modified,ltv from appuser a join referral_bonuses b on a.id=b.joinerUid where b.agentUid=$uid"));
