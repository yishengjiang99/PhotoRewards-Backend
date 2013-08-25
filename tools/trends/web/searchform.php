<?php
$kval = "";
$dval = "";
$tval = "";

if (isset($_GET['keyword'])) {
    $kval = $_GET['keyword'];
}

if (isset($_GET['date'])) {
    $dval = $_GET['date'];
}

if (isset($_GET['total'])) {
    $tval = $_GET['total'];
}
?>

<form action="search.php" method="get">
    Keyword <input type="text" name="keyword" class="input" value="<?php echo($kval) ?>">
    Date <input type="text" name="date" class="input" value="<?php echo($dval) ?>">
    Total <input type="text" name="total" class="input" value="<?php echo($tval) ?>">
    <input type="Submit" value="Search" class="button">
</form>
