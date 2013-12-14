<html>
<form method="POST">
<br>App Name: 
<input type=text name="appname" size=40 />
<br>AppId: <input type=text name="appid" size=40 />
<br>
<select name='network'>
<option value=ssa>supersonic</option>
<option value=aarki>aarki</option>

</select>
<br><input type=submit >
<br>
<?
require_once("/var/www/lib/functions.php");
if(isset($_POST)){
 $appid=$_POST['appid'];
 $offerId=$_POST['appname'];
 $network=$_POST['network'];
 $insert="insert ignore into extapps set offer_id='$offerId',network='$network',appid=$appid";
 echo $insert;
db::exec($insert);
}
