<html>
<head>
<script src=http://code.jquery.com/jquery-1.9.1.min.js></script>
<script type="text/javascript" src="http://mottie.github.com/tablesorter/js/jquery.tablesorter.min.js"></script>

</head>
<body>
<?php
require_once("/var/www/lib/functions.php");
$uid=intval($_GET['uid']);
echo rows2table(db::rows("select concat('/admin/unban.php?uid=',c.id) as linkunban, concat('/admin/user.php?uid=',c.id) as linkuid, note,email,mac,idfa,ipAddress,locale,country,created,modified,ltv,stars,xp from appuser c where c.banned=1 order by c.id desc limit 1000;"));
?>
</body>
<script>
$(document).ready(function(){
 $("table").tablesorter();
});
</script>
