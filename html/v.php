
    <!DOCTYPE html>   
    <html lang="en">   
    <head>   
    <meta charset="utf-8">   
    <title>Free Gift Cards</title>   
      	<script src="http://code.jquery.com/jquery-1.8.3.js"></script>

    <link href="/css/bootstrap.css" rel="stylesheet">   
<style>.thumbnail{height:56px}</style>
    </head>  
    <body>  
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
  $("#offer tr:last").after("<tr href='player.php?url="+encodeURIComponent(offer.mobileUrl)+"'><td><img class=thumbnail src="+offer.offerImg+"></td><td><h2><a href=player.php?url="+encodeURIComponent(offer.mobileUrl)+">"+offer.title+"</a></h2></td><td><a class=btn>"+offer.rewardText+"</a></td></tr>");
 });
}
</script>
<script src='https://api.virool.com/api/v1/offers/5c0dbeeee932e5ad448fcdbc01121b3e/all?userId=<?= $_GET['subid'] ?>&t=<?= time() ?>&callback=loadOffers'></script>
    </body>  
    </html>  


