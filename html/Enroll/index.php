<?
$data=rawurldecode($_GET['data']);
$data=$_GET['data'];
file_put_contents("/var/www/html/Enroll/data".time().".txt",rawurldecode($_GET['data']));
header("location: /xo/index.php");
