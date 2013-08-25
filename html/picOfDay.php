<?
$pics=array(
array("name"=>"NASA", "title"=>"First Shuttle Launch", 
      "url"=>"http://www.nasa.gov/images/content/741008main_columbia_sts1_full_full.jpg",
      "description"=>"A new era in space flight began on April 12, 1981, when Space Shuttle Columbia, or STS-1, soared into orbit from NASA's Kennedy Space Center in Florida. Astronaut John Young, a veteran of four previous spaceflights including a walk on the moon in 1972, commanded the mission. Navy test pilot Bob Crippen piloted the mission and would go on to command three future shuttle missions."),
array("name"=>"Live Science","url"=>"http://i.livescience.com/images/080613-rhea-stroll-01.jpg","title"=>"Father's Day for Daddy Rhea Bird",
"description"=>"The male rhea at the National Zoo will spend Father's Day as the proud papa of four new baby chicks"
),
);


die(json_encode($pics));

