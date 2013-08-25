<?php
session_start();

include('header.php');
include('searchform.php');
include('../share/config.php');
include('../share/MySqlUtil.php');

if (isset($_GET['keyword']) && isset($_GET['date']) && isset($_GET['total'])) {

    $mySqlUtil = new MySqlUtil();

    $keyword = $_GET['keyword'];
    $date = $_GET['date'];
    $total = $_GET['total'];

    if (isset($_SESSION['sort'])) {
        if (isset($_GET['sort']) && isset($_GET['col'])) {
            
            $_SESSION['sort'] = $mySqlUtil->secureVar($_GET['sort'], 'str');
            if ($_SESSION['sort'] == 'asc') {
                $_SESSION['sort'] = 'desc';
            } else {
                $_SESSION['sort'] = 'asc';
            }

            $_SESSION['col'] = $mySqlUtil->secureVar($_GET['col'], 'str');
            if ($_SESSION['col'] != 'keyword' && $_SESSION['col'] != 'date' &&
                $_SESSION['col'] != 'total') {
                $_SESSION['col'] = 'keyword';
            }
        }
    } else {
        $_SESSION['sort'] = 'asc';
        $_SESSION['col'] = 'keyword';
    }

    $trends = $mySqlUtil->searchTrends($keyword, $date, $total,
        $_SESSION['sort'], $_SESSION['col']);

    if (!empty($trends)) { ?>
        <table cellpadding="2" cellspacing="1" border="0" align="center" width="65%">
        <tr class="caption">
        <?php $url = 'search.php?keyword=' . $keyword . '&date=' . $date .
                '&total=' . $total . '&sort=' . $_SESSION['sort'] . '&col='; ?>
        <th><a href="<?php echo($url . 'keyword') ?>">Keyword</a></th>
        <th widht="100px"><a href="<?php echo($url . 'date') ?>">Date</a></th>
        <th widht="100px"><a href="<?php echo($url . 'total') ?>">Total</a></th>
        </tr>
        <?php
        foreach ($trends as $trend) {
            echo('<tr><td>' . $trend['keyword'] . '</td><td align="center">' .
                $trend['date'] . '</td><td align="center">' . $trend['total'] .
                    '</td></tr>');
        }
        ?></table><?php
    } else {
        ?><p><strong>Not founded</strong></p><?php
    }    
}
?>

<?php include('footer.php'); ?>