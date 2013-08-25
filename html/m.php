<?php
$c=$_COOKIE;
if(!isset($c['serial']) && !isset($c['imei']) ){
 header("location: https://play.google.com/store/apps/details?id=com.ragnus.grepawk&feature=m.php");
 exit;
}

require_once("/var/www/lib/functions.php");
$serial=$c['serial'];
$user=db::row("select * from android_devices where serial='$serial'");
$imei=$c['imei'];
$androidId=$c['androidId'];
if(!$user){
 db::exec("insert into android_devices set imei='$imei', android_id='$androidId', serial='$serial'");
 $uid=db::lastId();
 $user=db::row("select * from android_devices where id=$uid");
}

$points=$user['points'];
$uid=$user['id'];

$udata=array();
if($c['imei']!="null"){
$edid=$c['imei'];
}else{
 $edid=$c['serial'];
}

$url="http://api.everbadge.com/offersapi/offers/json?api_key=9B8yxsmXx7xv7ujVFYJNf1373448697&os=android&country_code=US";
$arr=json_decode(file_get_contents($url),1);
$rdata=$arr['data']['offers'];
foreach($rdata as $d){
 $u=array();
 $u['url']=$d['offer_url']."&device_id=$edid&&aff_sub=$uid";
 $u['points']=doubleval($d['payout'])*100;
 $u['img']=$d['thumbnail_url'];
 $u['name']=$d['public_name'];
 $udata[]=$u;
}
?>

    <!DOCTYPE html>   
    <html lang="en">   
    <head>   
    <meta charset="utf-8">   
    <title>Free Gift Cards</title>   
    <link href="/css/bootstrap.css" rel="stylesheet">   
    </head>  
    <body>  
    <table class="table table-striped">  
            <tbody>  
	<tr><td></td><td><b>You have <?php echo $points ?> Points</b></td><td><a class=btn>Redeem</a></td></tr>
<?php foreach($udata as $u){ ?>
              <tr>  
                <td><img src='<?php echo $u['img']; ?>'></td>
                <td><?php echo $u['name']; ?></td>  
                <td><a class=btn href='<?= $u['url'] ?>'><?= $u['points'] ?> Points</a></td>
              </tr>  
<?php } ?>
            </tbody>  
          </table>  
    </body>  
    </html>  


