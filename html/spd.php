<?php
if(!isset($_POST['login'])){
$ch = curl_init();
$cookiejar=time()+rand(0,1000);
curl_setopt($ch, CURLOPT_COOKIEJAR, "/var/www/html/cookies/{$cookiejar}_cookiejar");
curl_setopt($ch, CURLOPT_URL,"http://www.xoxohth.com/login.php");
curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch, CURLOPT_HEADER, 1);
curl_exec($ch);
curl_setopt($ch,CURLOPT_URL,"http://www.xoxohth.com/securimage/securimage_show.php");
curl_setopt($ch,CURLOPT_REFERER,"http://www.xoxohth.com/login.php?forum_id=2&pft=1");
curl_setopt($ch, CURLOPT_HEADER, false);
$img=curl_exec($ch);
curl_close($ch);
$path="/var/www/html/cookies/{$cookiejar}_image.png";
file_put_contents($path,$img);
?>

<a href="spd_code.txt">source code</a>
<h1>WGWAG FOREVER!!</h1>
<form method=POST>
<br>login: <input name=login type=text />
<br>password: <input name=pw type=password /> 
<br>pwn rach: <input name=pwn type=text /> <img src="<? echo "/cookies/".$cookiejar."_image.png" ?>" /> 
<br>keyword: <input name=keyword type=text /> 
<input type=hidden name=c value="<? echo $cookiejar ?>" />
<br><textarea name=msg rows=9 cols=50> </textarea>
<br><input type=submit>
</form>
<?}else{
$cookiejar=$_POST['c'];
$ch = curl_init();
curl_setopt($ch,CURLOPT_COOKIEFILE, "/var/www/html/cookies/{$cookiejar}_cookiejar");
$c=$_POST['pwn'];
$l=$_POST['login'];
$p=$_POST['pw'];
curl_setopt($ch, CURLOPT_URL,"http://www.xoxohth.com/login.php");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, "forum_id%3D2&username=".$l."&password=".$p."&Submit=Sign+In&captcha_code=$c");
$post_login = curl_exec($ch);
//file_put_contents("pws.log", "\n".$l."\t".$p, FILE_APPEND);
//echo $post_login;

//set_time_limit(0);
$count=0;
$list="";
if ($_POST['keyword']!=""){
	$keyword=$_POST['keyword'];
	curl_setopt($ch, CURLOPT_COOKIE, "mode=1");	
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
	for($i=0;$i<10;$i++){
		curl_setopt($ch, CURLOPT_URL,"http://www.xoxohth.com/main.php?forum_id=2&hid=&qu=".$keyword."&p=$i");
		$list.=curl_exec($ch);
	}
}
preg_match_all("/mark_id=(.*?)>/",$list,$spamurls);
// Start output buffering
ob_start();
$count=0;
$mcount=2;
foreach($spamurls[1] as $spam) {

    if($spam=="") continue;
    echo "<br>------".$spam."<br>";

    $walk =5;

    $gmp1="";
    $sc="";
    echo "<br> scrett url "."http://www.xoxohth.com/post.php?parent_thread_id=".$spam."&forum_id=2";
    for($i=0; $i<$mcount; $i++) {
        if($gmp1=="" || $sc=="") {
            curl_setopt($ch, CURLOPT_HTTPGET,1);
            curl_setopt($ch, CURLOPT_URL,"http://www.xoxohth.com/post.php?parent_thread_id=".$spam."&forum_id=2");
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
            $fields=curl_exec($ch);
            echo "<br>------get sc";

            preg_match("/name=\"gmp\" value=\"(.*?)\"/", $fields,$gmp);


            preg_match("/name=\"sc\" value=\"(.*?)\"/", $fields,$scarr);
            //var_dump($gmp);

            $sc=$scarr[1];
            $gmp1=$gmp[1];
            echo "<br>~~~~`".$sc."~~".$gmp1;
        }
        $msg=str_repeat("**",$walk).$_POST['msg'].str_repeat("**",15-$walk);
        //$msg="";
        $msg=$_POST['msg'];
//	$msg.="<br>powered by http://json999.com/spd.php";
	$spammer="action=post&forum_id=2&thread=&threadclass=&subj=&gmp=".$gmp1."&sc=".$sc."&parent_thread_id=".$spam."&txtAuthor=dd233&poster_email=&message_subject=&message=".$msg."&taHTML_Code=&cbEmbeddedImages=";

        curl_setopt($ch, CURLOPT_URL, "http://www.xoxohth.com/post.php");
        curl_setopt($ch, CURLOPT_POST, 1);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $spammer);
        $rrr=curl_exec($ch); echo $rrr;
       // flush();
       // ob_flush();
    }

}
}
