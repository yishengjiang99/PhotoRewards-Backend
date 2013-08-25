<?php
require_once("/var/www/lib/functions.php");
echo rows2table(db::rows("select created,joinerUid from referral_bonuses where agentUid=3366"));
