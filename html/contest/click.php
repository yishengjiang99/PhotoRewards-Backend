<?php
require_once("/var/www/lib/functions.php");
$go=urldecode($_GET['go']);
$subid=$_GET['subid'];
$st=explode("_",$subid);
$uid=intval($st[0]);
$offerID=$st[1];
if(!$go || $go==''){
  header("location: contest://");
  exit;
}
$sql="insert into contest_clicks set subid='$subid', uid=$uid, offer_id='$offerID',created=now(), url='$go'";
db::exec($sql);

?>

<html>
<head>
<meta name="apple-mobile-web-app-capable" content="yes" />
</head>
<body>
<script>
      window.location = '<?php echo $go; ?>';
</script>
</body></html>

