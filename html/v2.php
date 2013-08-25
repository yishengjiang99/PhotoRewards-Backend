
    <!DOCTYPE html>   
    <html lang="en">   
    <head>   
    <meta charset="utf-8">   
    <title>Free Gift Cards</title>   
      	<script src="http://code.jquery.com/jquery-1.8.3.js"></script>
    <link href="/css/bootstrap.css" rel="stylesheet">   
<style></style>
    </head>  
    <body>  
 <a href='picrewards://' class=btn><h2>Back To PhotoRewards</h2></a>
<br><br>
<iframe src="//api.virool.com/widgets/5c0dbeeee932e5ad448fcdbc01121b3e?width=300&height=200&userId=<?= $_GET['sibid']?>" width="300" height="200" allowfullscreen marginwidth="0" marginheight="0" frameborder="0" scrolling="no"></iframe>
    <table id=offer class="table table-striped">
<tbody>
<tr><td colspan=3><h1>Watch Videos For Points</h1></td></tr>
</tbody>  
          </table>  
<script>
function loadOffers(ret){
$("tr").live("click", function(e) {
    location.href = $(this).attr("href");
    e.stopPropagation();
});

 $(ret.data.offers).each(function(i,offer){
  $("#offer tr:last").after("<tr href='player.php?url="+encodeURIComponent(offer.mobileUrl)+"'><td><img class=thumbnail src="+offer.offerImg+"></td><td><h2><a href=player.php?url="+encodeURIComponent(offer.mobileUrl)+">"+offer.title+"</a></h2></td><td><a class=btn><h2>"+offer.rewardText+"</h2></a></td></tr>");
 });
}
</script>
<script src='https://api.virool.com/api/v1/offers/5c0dbeeee932e5ad448fcdbc01121b3e/all.jsonp?userId=<?= $_GET['subid'] ?>&t=<?= time() ?>&callback=loadOffers'></script>
    </body>  
    </html>  


