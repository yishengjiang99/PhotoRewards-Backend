
<?php
error_reporting(E_ALL); ini_set('display_errors', '1');
/* Create a new imagick object and read in GIF */
$im = new Imagick("6c700f701401fed1aed7cd2a24a2ed9a.jpeg");
$im->thumbnailImage(320*0.7,0);
$im->writeImage("smaller.jpeg");
exit;
/* Resize all frames */
foreach ($im as $frame) {
    /* 50x50 frames */
    $frame->thumbnailImage(50, 0);

    /* Set the virtual canvas to correct size */
    $frame->setImagePage(50, 50, 0, 0);
}

/* Notice writeImages instead of writeImage */
$im->writeImages("example_small.gif", true);
?>

