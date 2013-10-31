<?
require_once("/var/www/lib/functions.php");
echo "<table><tr><td valign=top>";
echo rows2table(db::rows("select concat('/admin/unban.php?uid=',rewarded_to_uid) as linkban, aes_decrypt(code,'supersimple'),rewarded_to_uid, date_redeemed, name,username, c.ltv,c.created from reward_codes a join rewards b on a.reward_id=b.id join appuser c on a.rewarded_to_uid=c.id where a.given_out=1 and banned=1 and c.id!=2 order by a.date_redeemed desc limit 1000"));
echo "</td><td>";
echo "</td></tr></table>";
