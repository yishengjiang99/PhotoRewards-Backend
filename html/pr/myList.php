
<?php
require_once("/var/www/lib/functions.php");
$idfa=$_GET['idfa'];
$mac=$_GET['mac'];
$uid=$_GET['uid'];
$sql="select 'MyUploads' as OfferType, id as refId, title as Name, url as RedirectURL, category, cash_bid as Amount, description as Action, 1 as canUpload from PictureRequest where uid=$uid";
$mylist=db::rows($sql);
die(json_encode($mylist));
