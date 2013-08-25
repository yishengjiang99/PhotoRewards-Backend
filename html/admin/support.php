<html>
<body>
<?php
require_once("/var/www/lib/functions.php");
$_GET=$_REQUEST;

if(isset($_GET['msgid'])){
  $msgid=intval($_GET['msgid']);
  db::exec("update inbox set readmsg=1 where id=$msgid");
  $msgrow=db::row("select * from inbox where id=$msgid");
  require_once("/var/www/html/pr/apns.php");
  $to=$msgrow['from_uid'];
  $from =$msgrow['to_uid'];
  $msg=stripslashes($_GET['msg']);
  $msg=urlencode($msg);
if($msg!=""){
  db::exec("insert into inbox set from_uid=$from, to_uid=$to, msg='$msg', created=now()");
  $fromname="";
  $user= db::row("select username from appuser where id=$from");
  $fromname=$user['username'];
  $amsg="$fromname sent you a message!";
  apnsUser($to,$amsg,"$fromname says '".urldecode($msg)."'");
 }
}
if(isset($_GET['thread'])){
 $user=intval($_GET['thread']);
echo  rows2table(db::rows("select * from inbox where (to_uid=$user and from_uid=2902) or (to_uid=2902 and from_uid=$user)"));
exit;
}
echo "<table width='700'><tr>";
echo "<td valign=top>
canned responses:
<pre>
Q: I don't get points/app won't let me upload picture
A: It takes a while for us to receive confirmation from the advertiser that you've tried the app.
Play it some more. We will msg you as soon as we hear back from them.

Q: How do I get my bonus code
A: click on 'invite friends' on the first tab

Q: Great App!!
A: Thanks! Please rate us 5-stars in the AppStore
Q: What's xp used for?
A: It makes your bonus code more powerful, and more points for you and your friend when they enter it

Q: When will you be adding new apps
A: Next Monday

Q: Did you get my msg?
A: If I missed them, could you email yisheng@grepawk.com

Q: Can I have some free points
A: No.
</pre>";
echo "</td>";
echo "<td width=70%><table>";
$rows=db::rows("select * from inbox where to_uid=2902 and msg!='' and created>date_sub(now(), interval 10 day) order by readmsg, id desc");
foreach($rows as $r){
 $read = $r['readmsg']==1 ? "Read" : "Unread";
 echo "<tr><td>($read)From ".$r['from_uid'].": <b><br><span style='width:300px'>".urldecode($r['msg'])."</span></b><br> ".$r['created']."  <a target=_blank href=/admin/support.php?thread=".$r['from_uid']." >Thread</a>";
 echo "<form method=POST><input type=hidden name='msgid' value='".$r['id']."'/><input name=msg type=text size=60 /><input type=submit value='reply or mark as read'></form></td></tr>";
}
echo "</table>";
echo "</td></tr></table>";
?>
</body>
</html>
