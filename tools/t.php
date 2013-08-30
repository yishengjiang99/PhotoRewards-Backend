<?php

require_once('/var/www/html/pr/apns.php');
_apnshUser(2902,'dd*()','(()','ddd');
exit;
if($row=db::row("select * from appuser where id=200002")){
echo 'yes';
}
exit;
apnsUser(3756,"You discovered a bug and got a free giftcard!! Happy birthday!","");
exit;
$ch=curl_init();
for($i=1;$i<5;$i++){
        $url="http://www.xoxohth.com/main.php?forum_id=2&p=$i";
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_COOKIE, "mode=1");
        $ret=curl_exec($ch);

        $thread="/thread_id=(\d+)&(.*?)>(.*?)<\/a>/";
        preg_match_all($thread,$ret,$m);
        $ts=array_combine($m[1],$m[3]);
        foreach($ts as $tid=>$title){
                $title=addslashes($title);
                $sql="insert ignore into thread set tid=$tid, title='$title'";
                db::exec($sql);
                if(rand(1,23)==14) sleep(4);
                $o="";
                exec("php /var/www/tools/p.php $tid >/dev/null &");
                echo "\n pulling $tid \t $o";
        }
        echo "\ndone $url";
}


