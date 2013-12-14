<html><body>
<head>
<script src=http://code.jquery.com/jquery-1.9.1.min.js></script>
<script type="text/javascript" src="http://mottie.github.com/tablesorter/js/jquery.tablesorter.min.js"></script>

</head>
<?php
require_once("/var/www/lib/functions.php");
$uid=intval($_GET['uid']);
echo rows2table(db::rows("select banned,idfa,mac,ipaddress,a.id,a.created as joined,locale,b.created as enteredbonus,deviceInfo,modified,ltv 
from appuser a join referral_bonuses b on a.id=b.joinerUid where b.created>date_sub(now(), interval 1 day) order by ipaddress"));
?>
<script>
$(document).ready(function(){
 $("table").tablesorter();
});
</script>
</body></html>
