<?php
require_once("/var/www/lib/functions.php");
$sql="select a.*,concat('/admin/contestAwardSented.php?id=',a.id) as linksent,r.name,r.CashValue from contest_winner a join rewards r on a.reward_id=r.id";
$winners=db::rows($sql);
echo rows2table($winners);
