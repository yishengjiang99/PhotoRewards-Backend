<?php
header("location: http://www.grepawk.com");
exit;
$ua = strtolower($_SERVER['HTTP_USER_AGENT']);
if(stripos($ua,'android') !== false) {
	header('Location: m.php');
	exit();
}
require_once("/var/www/lib/google.php");
?>
<html>
    <head>
        <title>Email Alerts</title>
        <meta content="sign up for email alerts on discussion board conversations, amazing Groupon Deals, Craigslist gems, " name="description"/>
        <meta content="deals, email alerts, discussion board, deals, craigslist, twitter feeds" name="keywords"/>
        <link href="http://code.jquery.com/ui/1.10.0/themes/base/jquery-ui.css" rel="stylesheet"/>
        <link href="/css/base.css" rel="stylesheet"/>
        <script src="http://code.jquery.com/jquery-1.8.3.js"></script>
        <script src="http://code.jquery.com/ui/1.10.0/jquery-ui.js"></script>
<script src='https://cdn.firebase.com/v0/firebase.js'></script>	
    </head>
    <body style="background:white;font-size:300%">
<a href="http://c.mobpartner.mobi/?s=659021&a=478"><img src="http://r.mobpartner.mobi?s=659021&a=478" /></a>

<a href="http://www.amazon.com/gp/product/B004SJ3BCI/ref=as_li_qf_sp_asin_tl?ie=UTF8&camp=1789&creative=9325&creativeASIN=B004SJ3BCI&linkCode=as2&tag=grepawk07-20">Angry Birds Free</a><img src="http://ir-na.amazon-adsystem.com/e/ir?t=grepawk07-20&l=as2&o=1&a=B004SJ3BCI" width="1" height="1" border="0" alt="" style="border:none !important; margin:0px !important;" />
<br>
<a href="http://click.linksynergy.com/fs-bin/click?id=gY1qEy7McgU&offerid=146261.10005963&type=3&subid=0" >Give iTunes Gifts</a><IMG border=0 width=1 height=1 src="http://ad.linksynergy.com/fs-bin/show?id=gY1qEy7McgU&bids=146261.10005963&type=3&subid=0" >
    <script>
    </script>
        <div style="width:200px;height:auto;margin:auto 0;">
            <input id="xoxosearch" size=50 type=text placeholder="search for posts threads pumos authors"/>
        </div>
        <table><tr><td width=600px valign=top>
	<div id=result>
	</div></td><td width=300px valign=top>
	<div id=moresearch></div></td></tr></table>
    </body>
    <script>
        $(document).ready(function () {
            $.ui.autocomplete.prototype._renderItem = function (ul, item) {
                return $("<li></li>").addClass("ui-menu-item").data( "item.autocomplete" , item).append("<a>"+item.label+"</a>").appendTo(ul);
            }
        $("#xoxosearch").autocomplete({
            source: function (request, response) {
                var items = [];
                $.getJSON("http://search.json999.com/xo/threads/_search?size=10&q=" + request.term, function (ret) {
                    items = $.merge(items, ret.hits.hits);
                    total=ret.hits.total;
                    items.push({_type:"more",_source:{display:"More Threads on <b>"+request.term+"</b> ("+total+" Total)",query:"http://search.json999.com/xo/posts,threads/_search?size=1000&q="+request.term}});
                    $.getJSON("http://search.json999.com/xo/posts/_search?size=50&q=" + request.term, function (ret) {
                        items = $.merge(items, ret.hits.hits);
                        total=ret.hits.total;
                   	response($.map(items, function (data) {
                        	item = data._source;
				var out;
                        	if (data._type == "threads") {
					out={ label: "Thread:<b>" + item.author + "</b>:'" + item.title + "' <br>" + new Date(item.date * 1000).toLocaleString() 
					             + " <c class='author text' auth='"+item.author+"'>More from "+item.author+"</c>",
                                	      value: item.title, id:data._id,
                                	      href:"http://www.xoxohth.com/thread.php?thread_id="+data._id
                                	    };
 	                         } else if (data._type == "posts") {
                        	        out={label: "POST: <b>" + item.author + "</b>:'" + item.content + "' (" + new Date(item.date * 1000).toLocaleString()
                        	        + "<c class='author text' auth='"+item.author+"'>More from '"+item.author+"'</c>",
                                	     value:"http://www.xoxohth.com/thread.php?thread_id="+item.tid, id:data._id, 
					     href:"http://www.xoxohth.com/thread.php?thread_id="+item.tid
					  }
	                         }else if(data._type=="more"){
	                                out={label:data._source.display,action:"loadmore",query:data._source.query}
	                         }
				return out;
                    	    }));
			});
             	});
           },
           select:function(event,ui){
                    if(ui.item.action && ui.item.action=="loadmore"){
                       loadQ(ui.item.query,$("#moresearch"));
                    }else{
                              str="<a href="+ui.item.href+">"+ui.item.href+"</a>";
                    $("#result").html(str);
//                     $("#result").html("<div class=iframe_container style=position:absolute;z-index:-1;background-color:white;>"+
  //                                                      "<iframe style='position:absolute;left:0px;height:600px;width:600px;z-index=-3;background:white' src="+ui.item.href+"></iframe></div>");
                   }
           }
        });
        $(document).on("click",".author",function(e){
                e.preventDefault();
                q="http://search.json999.com/xo/threads,posts/_search";
                _author=$(this).attr("auth");
                data=JSON.stringify({query:{match:{author:_author}},size:100});
                loadQ(q, $("#moresearch"),data);
                return;
        });
       function loadQ(url,divid,data){
        divid.html("its cranking please wait");
                       $.ajax({url:url,type:"POST",crossdomain:true,data:data,dataType:'json',
                               success:function(_ret){
                               var hits=_ret.hits.hits;
                               html = "<br>"+_ret.hits.total+" found";
                               $.each(hits,function(i,data){
                               item=data._source;
                               if (data._type == "threads") {
                                                                             href="http://www.xoxohth.com/thread.php?thread_id="+data._id
                                 html+= "<br>Thread:<b>" + item.author + "</b>:'" + item.title + "'<br>(" + new Date(item.date * 1000).toLocaleString() + ") <a href="+href+">link</a>";
                               } else if (data._type == "posts") {
                                                                                                              href="http://www.xoxohth.com/thread.php?thread_id="+item.tid
                                  html+="<br>POST: <b>" + item.author + "</b>:'" + item.content + "'<br>(" + new Date(item.date * 1000).toLocaleString() + ") <a href="+href+">link</a>";
                               }
                                });
                                divid.html(html);
                              }
                       });
                       }
});
    </script>
</html>
