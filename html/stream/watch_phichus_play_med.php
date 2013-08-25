<html>
<body>

<?php
echo "tail -f phichus";
   ob_flush();
    flush();
 header( 'Content-type: text/html; charset=utf-8' );
$host="json999.com";
$port=1026;
$handle= @fsockopen($host, $port, &$errno, &$errstr, 30); 
while (!feof($handle)) {
 $line=fgets($handle);
 if($line!="") {
    echo "<br>".$line;
   if(rand(0,10)==1) echo "<script>window.scrollTo(0,document.body.scrollHeight)</script>";
	  
   ob_flush();
    flush();
 }
}
?>
