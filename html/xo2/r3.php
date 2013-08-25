<?php
        $url="http://www.xoxohth.com/main.php?forum_id=2";
 	$ch=curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_COOKIE, "mode=1");
        $ret=curl_exec($ch);
        $html=str_replace("src=imgs", "src=http://www.xoxohth.com/imgs",$ret);
	echo $html;
?>
<style>
[class^="icon-"],
[class*=" icon-"] {
  display: inline-block;
  width: 14px;
  height: 14px;
  *margin-right: .3em;
  line-height: 14px;
  vertical-align: text-top;
  background-image: url("/images/glyphicons-halflings.png");
  background-position: 14px 14px;
  background-repeat: no-repeat;
}
.icon-star {
  background-position: -120px 0;
}
.inline {display:inline}
.ml10 {margin-left:10px}
.btn {
  display: inline;
  margin-bottom: 0;
  margin-right: 20px;
  font-size: 13px;
  line-height: 18px;
  *line-height: 20px;
  color: #333333;
  text-align: center;
  text-shadow: 0 1px 1px rgba(255, 255, 255, 0.75);
  vertical-align: middle;
  cursor: pointer;
  background-color: #f5f5f5;
  *background-color: #e6e6e6;
  background-image: -ms-linear-gradient(top, #ffffff, #e6e6e6);
  background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#ffffff), to(#e6e6e6));
  background-image: -webkit-linear-gradient(top, #ffffff, #e6e6e6);
  background-image: -o-linear-gradient(top, #ffffff, #e6e6e6);
  background-image: linear-gradient(top, #ffffff, #e6e6e6);
  background-image: -moz-linear-gradient(top, #ffffff, #e6e6e6);
  background-repeat: repeat-x;
  border: 1px solid #cccccc;
  *border: 0;
  border-color: rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25);
  border-color: #e6e6e6 #e6e6e6 #bfbfbf;
  border-bottom-color: #b3b3b3;
  -webkit-border-radius: 4px;
     -moz-border-radius: 4px;
          border-radius: 4px;
  filter: progid:dximagetransform.microsoft.gradient(startColorstr='#ffffff', endColorstr='#e6e6e6', GradientType=0);
  filter: progid:dximagetransform.microsoft.gradient(enabled=false);
  *zoom: 1;
  -webkit-box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2), 0 1px 2px rgba(0, 0, 0, 0.05);
     -moz-box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2), 0 1px 2px rgba(0, 0, 0, 0.05);
          box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2), 0 1px 2px rgba(0, 0, 0, 0.05);
}

.btn:hover,
.btn:active,
.btn.active,
.btn.disabled,
.btn[disabled] {
  background-color: #e6e6e6;
  *background-color: #d9d9d9;
}
.threadrow:hover{background-color: #CC9999; color: black;}
</style>
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
              $div.html("<img src=/images/ajax-loader.gif>");
              $.get(url,function(ret){
                     if(ret){
			$div.replaceWith(ret);
                           }
                    });

        });
    });
 };
})( jQuery );
</script>
<script>

$(document).ready(function(){
  $('[href*="thread.php"]').click(function(e){
    var url = "http://www.xoxohth.com"+$(this).attr("href");
    e.preventDefault();
    $('html, body').animate({scrollTop:$(this).offset().top},500);
    var frames=$(this).parent().find(".iframe_container");
    if(frames.length!=0){
       frames.show();
      $(this).parent().find(".closer").show();
    }else{
      $(this).parent().append("<a class='inline btn closer ml10' onclick='$(this).hide().next().hide();return false;'>Close</a>").append("<div class=iframe_container style=position:relative;z-index:2;left:-300px;background-color:white;>"
		  +"	<iframe style='position:absolute;left:0px;height:600px;width:900px;z-index=3;background:white' src="+url+"></iframe></div>");
    }
    return false;
   });
  $('[style="word-break: break-all;"]').each(function(){
   $(this).parent().addClass("threadrow");
   $(this).next().next().append(addOns(this));
  });
  $(".aj").aj();
  $(document).click(function(e){
     $(".iframe_container").hide();
  });
});

function addOns(that){
  var categories_html="";
  var categories="select a category,law school, law firms,how to get laid, news, politics,entertainment,philosophy,xoxo poasters,parodies,box-threading,accidental box-threading,rage-threading,wgwag, reminders about nowag".split(/\s*\,\s*/);
 url=$(that).find("a").get(0).href;
 var tid=url.match(/thread_id=(\d+)/)[1];
 btn180="<a xhref=/xo/btn180.php?tid="+tid+" class='btn aj'><span class='icon-star'></span>180</a>";
 if(categories_html==""){
   for(i in categories){
    categories_html+="<option value='"+categories[i]+"'>"+categories[i]+"</option>";
   }
 }
 var category_select="<select binder='change' xhref=/xo/category.php?tid="+tid+" class='aj'>"+categories_html+"</select>";
 return "<span style=position:absolute;left:931px>"+ btn180+category_select+"</span>";
}
</script>
</html>


