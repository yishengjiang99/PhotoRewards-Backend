<?php

function scrapGoogleHotTrendsAtom() {
    $page = file_get_contents(
        'http://www.google.com/trends/hottrends/atom/hourly');

    if ($page) {
        preg_match_all('(<a href=".+">(.*)</a>)siU', $page, $matches);
        return $matches[1];
    }
    
    return NULL;
}
?>
