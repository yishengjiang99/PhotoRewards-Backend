<?
require_once("/var/www/lib/functions.php");
echo "<table><tr><td valign=top>";
echo "CARDS";
echo rows2table(db::rows("select concat('/admin/ban.php?uid=',rewarded_to_uid) as linkban, a.id, ipAddress,mac,country,rewarded_to_uid as uid, date_redeemed, name, c.ltv,c.created from reward_codes a join rewards b on a.reward_id=b.id join appuser c on a.rewarded_to_uid=c.id where a.given_out=1 order by a.date_redeemed desc limit 100"));
echo "</td><td>PAYPAL";
echo rows2table(db::rows("select  concat('/admin/ban.php?uid=',transfer_to_user_id) as linkban,transfer_to_user_id as uid,a.email,status,amount,b.created,b.xp,b.ltv,b.stars,a.created,b.created as joined from PaypalTransactions a join appuser b on a.transfer_to_user_id=b.id order by a.id desc limit 100"));
echo "</td></tr></table>";
