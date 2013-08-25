<?
$config=array(
 "categories"=>explode(",", "#food,#love,#nature,#party,#swag,#yolo,#family,#beautiful,#ideas"),	
 "bidtiers"=>array(1,2,3,5,7,10,20),
 "myNumber"=>"tel://6508046836"
);
die(json_encode($config));
