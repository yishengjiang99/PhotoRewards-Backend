<?
require_once("/var/www/lib/functions.php");
echo rows2table(db::rows("select rewarded_to_uid, date_redeemed, name from reward_codes a join rewards b on a.reward_id=b.id where a.given_out=1 order by a.date_redeemed desc limit 100"));
