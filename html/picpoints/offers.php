<?php
$mac=$_GET['mac'];
$url= "http://ws.tapjoyads.com/get_offers?type=1&json=1&app_id=8d87c837-0d24-4c46-9d79-46696e042dc5&mac_address=$mac&publisher_user_id=9136384695362143010&device_type=iPhone&country_code=US&language=EN&max=5&os_version=6.1.3&device_ip=96.247.52.15&start=0";
$json=json_decode(file_get_contents($url),1);
$offers=$json['OfferArray'];
/*
  ["Amount"]=>
  string(2) "25"
  ["Cost"]=>
  string(4) "Free"
  ["Type"]=>
  string(3) "App"
  ["StoreID"]=>
  string(9) "367003839"
  ["Name"]=>
  string(50) "Booking.com Hotel reservations for 290,000+ hotels"
  ["IconURL"]=>
  string(115) "https://d21x2jbj16e06e.cloudfront.net/icons/57/2be84cac769bea3d362c4055d266b880899ce30edc584f3cedf37d2e1101b956.jpg"
  ["IconURLMedium"]=>
  string(116) "https://d21x2jbj16e06e.cloudfront.net/icons/114/2be84cac769bea3d362c4055d266b880899ce30edc584f3cedf37d2e1101b956.jpg"
  ["RedirectURL"]=>
  string(1576) "https://ws.tapjoyads.com/click/app?data=d7c9b6d7119e80b0afc455f2fa09acea940f97218142fcd9c54ffdf26e56d0ea63fd087621a7856023072aa829e224a916041815134ac4e28a45c6ee1b627876a04a84db37f006dffcc90ac474ba5fdfa1ea85b07f750ab008c4fc33ff105f591127c6708a815186f59f9016653241458455cc348f57c37badeb6e91a41fbe7940eabac1eedcc7f42344f871c69ec2dbcad2c89a54a29167564117e45f77f217390ec7200237d8bed70402805c31d2aff539c7da49ad7d610210327b22e0592ffcb6a78682919f121c87a2a2b4f6b6604a4cc7b08ef4f0ac721db5fa81a6da71419d0f0c5375c4db369b93783ac45c77dbeec21123eec5480a037c26bf514453d70fb41375b2d2fdcdbf915e692e01f2bcff719b1675f06aea995ade7d93c6585c88489e500c22457c9422f1a38985c066b370716fe0892c8b1d11792e71ca2da4be96e00a05477517c951a791eaf7d2deae5faa4b5f5a530922cff395d40980608e902355f24bd9dc58e6d8606f30a23758cee8e4d8c8a2fe9d08c55fba456470ef1e232641c908b2214e3779436548e437781e8e4590c83decd6df619d39a77d851c5d6dfce9b51a7bcd72df53fce0d6073dd563ace4c513995e2bb70a3015f724c6cb6ae147c5496c4e5e48be0162c3d2f8efbb521fea99559e0ea105b63d04a5c4b5ec064173cc4c8ede2cf71033aa01be775bd24604aee75641f5fd3049fe683f097d4252cbbafdc480d728e1ce6101143e8909eb0aa45e954197b463160705aacdc828b2e572e72f73d88ad9d969ecdb83003f23d20c74d919e3a0cb48f7ea53b401e4e6722cb1aa87e085231f8b517663e0fb6f342880f7ee1f02d35df0db8b626f00e8eaa394307d6e94f692c623ed46adc0f42f10fc50005d9059d1e9b52be58c9d0d1f9a5eb5beaff4fa338b0a23cb51ddbfe4e4edd1e2e66b5b3e0292d0f2fe49e8f357d72e6308d41c23301c33c5eecac8478b1b82c484e2b78cf7f379734be5f36e2aaece938dd3ad902fa5e8f17416106b43669c4ecd50482b325adc2b03c95368fd9cab39bccdb7c001f6d7432fdf3434d153086bb4511390"
*/
$ret=array();
foreach($offers as $offer){
 $o=array("pic"=>$offer['IconURLMedium'],"title"=>"Snap a Screenshot","description"=>"Snap a screenshot of the app ".$offer['Name'], "points"=>intval($offer['Amount'])/2);
 $ret[]=$o;
}
die(json_encode($ret));
