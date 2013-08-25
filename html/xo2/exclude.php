<?
error_reporting(0);
$a=trim($a);
$author=trim($author);
$exclude=array("pepe","My Prole Called Life","calm",".;..,..;,:;.;.,.,,;..;...:,.,.;,,;,........:..,.::",".,.,...,.,,..,:,,:,..;,:::,..,.,:,.,:....:.,:.::","beau",",.,,..,,.,.,..:,,:,...,:::,.,.,:,.,.:.,:.,:.,:,.",": : : :","To be fair","\" \" \" \"");
if(in_array($a,$exclude)){
 die('poast requested to be unsearchable');
}

if(in_array($author,$exclude)){
 die('poast requested to be unsearchable');
}


