<html>
<link rel="stylesheet" type="text/css" media="all" href="/css/base.css" />
<script type="text/javascript" src="/js/jquery.min.js"> </script>
<script>
(function( $ ){
 $.fn.aj=function(){
     return this.each(function(){
        binder=$(this).attr("binder") || "click";
        $(this).one(binder, function(e){ //binding only once
              var $div=$(this);
              url=$(this).attr("xhref");
              if($(this).val()) url+="&value="+encodeURI($(this).val());
              $div.append("<img src=/images/ajax-loader.gif>");
              $.get(url,function(ret){
                     if(ret){
                        $div.replaceWith("<span class=pinned>"+ret+"</span>");//.addClass("pinned");
                           }
                    });

        });
    });
 };
})( jQuery );
</script>
<body>
<?php
require_once("/var/www/lib/db.class.php");
$a=addslashes(urldecode($_GET['a']));
$tids=explode(",",$_GET['tids']);
$_tids=array();
foreach($tids as $t){
 $_tids[]=intval(trim($t));
}
echo "<table border=1 width=700px align=center>";
$cathtml="";
 $cat=explode(",","select a category,law school, law firms,how to get laid, news, politics,entertainment,philosophy,xoxo poasters,parodies,box-threading,accidental box-threading,rage-threading,wgwag, reminders about nowag");
  foreach($cat as $cc){
	$cc=trim($cc);
    $cathtml.="<option value='$cc'>$cc</option>";
   }
$threads=db::rows("select a.title, b.date, op, b.author,a.tid from thread a  join post b on a.op=b.pid where a.tid in (".implode(",", $_tids).") order by b.date desc");
foreach($threads as $tr){
   $row=$tr;
  $btn180="<a xhref=/xo2/btn180.php?tid=".$tr['tid']." class='btn aj'><span class='icon-star'></span>180</a>";
   $link="<a href=http://www.xoxohth.com/thread.php?thread_id=".$row['tid']."&mc=3&forum_id=2 target=_blank>".$row['title']."</a>";
  $_cathtml="<select binder='change' xhref=/xo2/category.php?tid=".$row['tid']." class='inline aj'>$cathtml</select>";
  echo "<tr style=background:#EDEFF4><td>$_cathtml</td><td>$link --- $btn180</td><td>".$tr['date']."</td><td>".$tr['author']."</td></tr>";
   $posts = db::rows("select tid, content, date from post where tid=".$tr['tid']." and author='$a'");
   foreach($posts as $p){
    echo "<tr><td>".$p['date']."</td><td colspan=3>".$p['content']."</td><tr>";
   }
}
echo "</table>";
?>
</body>
<script>
$(document).ready(function(){
  $(".aj").aj();
});
</script>
</html>
