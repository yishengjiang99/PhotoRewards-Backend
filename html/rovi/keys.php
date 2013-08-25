<?php
$listkey="qgx9ane7cfzjcfz3hjmp4xbd";
$secret="rcWWmBBY4J";

$t=time();
$sig=md5($listkey.$secret.$t);
$servicelist="http://api.rovicorp.com/TVlistings/v9/listings/services/postalcode/94085/info?locale=en-US&countrycode=US&apikey=$listkey&sig=$sig&locale=en-US&countrycode=US";
echo $servicelist;
echo file_get_contents($servicelist);

exit;
$url="http://api.rovicorp.com/TVlistings/v9/listings/gridschedule/360861/info?apikey=qgx9ane7cfzjcfz3hjmp4xbd&sig=sig&locale=en-US&duration=120";
/*
Key: 5kugq6wsjfs8vhbr6q3zz36r

Application:
    NextEpisode 
Key:
    5kugq6wsjfs8vhbr6q3zz36r 
Shared Secret:
    rcWWmBBY4J 


Metadata and Search API
Key: 9pfzd4v33bubp4k95gju6ngs

Application:
    NextEpisode 
Key:
    9pfzd4v33bubp4k95gju6ngs 
Shared Secret:
    x5ZN279xPw 
Status:
    active 
Registered:
    48 seconds ago 

Key Rate Limits
5	Calls per second
3,500	Calls per day

    View Report
    Delete Key

TV Listings API
Key: qgx9ane7cfzjcfz3hjmp4xbd

Application:
    NextEpisode 
Key:
    qgx9ane7cfzjcfz3hjmp4xbd 
Status:
    active 
Registered:
    46 seconds ago 

Key Rate Limits
5	

*/
