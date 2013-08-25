<?php

include(dirname(__FILE__).'/../share/config.php');
include(dirname(__FILE__).'/../share/Messenger.php');
include(dirname(__FILE__).'/../share/MySqlUtil.php');
include(dirname(__FILE__).'/../share/scraper.php');

Messenger::log('Scraper started');

$mySqlUtil = new MySqlUtil();

$trends = scrapGoogleHotTrendsAtom();
if (empty($trends)) {
    Messenger::error('Could not scrap trends');
    exit;
}

$mySqlUtil->saveTrends($trends);

Messenger::log('Trends saved');
?>
