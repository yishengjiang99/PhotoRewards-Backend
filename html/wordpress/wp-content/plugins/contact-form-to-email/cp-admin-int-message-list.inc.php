<?php

if ( !is_admin() )
{
    echo 'Direct access not allowed.';
    exit;
}

$this->item = $_GET["cal"];

global $wpdb;

$message = "";

if (isset($_GET['lu']) && $_GET['lu'] != '')
{
    $wpdb->query('UPDATE `'.$wpdb->prefix.$this->table_messages.'` SET paid='.$wpdb->escape($_GET["status"]).' WHERE id='.$_GET['lu']);           
    $message = "Item updated";        
}
else if (isset($_GET['ld']) && $_GET['ld'] != '')
{
    $wpdb->query('DELETE FROM `'.$wpdb->prefix.$this->table_messages.'` WHERE id='.$_GET['ld']);       
    $message = "Item deleted";
}

if ($this->item != 0)
    $myform = $wpdb->get_results( 'SELECT * FROM '.$wpdb->prefix.$this->table_items .' WHERE id='.$this->item);


$current_page = intval($_GET["p"]);
if (!$current_page) $current_page = 1;
$records_per_page = 50;                                                                                  

$cond = '';
if ($_GET["search"] != '') $cond .= " AND (data like '%".$wpdb->escape($_GET["search"])."%' OR posted_data LIKE '%".$wpdb->escape($_GET["search"])."%')";
if ($_GET["dfrom"] != '') $cond .= " AND (`time` >= '".$wpdb->escape($_GET["dfrom"])."')";
if ($_GET["dto"] != '') $cond .= " AND (`time` <= '".$wpdb->escape($_GET["dto"])." 23:59:59')";
if ($this->item != 0) $cond .= " AND formid=".$this->item;

$events = $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix.$this->table_messages." WHERE 1=1 ".$cond." ORDER BY `time` DESC" );
$total_pages = ceil(count($events) / $records_per_page);

if ($message) echo "<div id='setting-error-settings_updated' class='updated settings-error'><p><strong>".$message."</strong></p></div>";


?>
<script type="text/javascript">
 function cp_updateMessageItem(id,status)
 {    
    document.location = 'options-general.php?page=<?php echo $this->menu_parameter; ?>&cal=<?php echo $_GET["cal"]; ?>&list=1&status='+status+'&lu='+id+'&r='+Math.random( );   
 } 
 function cp_deleteMessageItem(id)
 {
    if (confirm('Are you sure that you want to delete this item?'))
    {        
        document.location = 'options-general.php?page=<?php echo $this->menu_parameter; ?>&cal=<?php echo $_GET["cal"]; ?>&list=1&ld='+id+'&r='+Math.random();
    }
 }
</script>
<div class="wrap">
<h2><?php echo $this->plugin_name; ?> - Message List</h2>

<input type="button" name="backbtn" value="Back to items list..." onclick="document.location='admin.php?page=<?php echo $this->menu_parameter; ?>';">


<div id="normal-sortables" class="meta-box-sortables">
 <hr />
 <h3>This message list is from: <?php if ($this->item != 0) echo $myform[0]->form_name; else echo 'All forms'; ?></h3>
</div>


<form action="admin.php" method="get">
 <input type="hidden" name="page" value="<?php echo $this->menu_parameter; ?>" />
 <input type="hidden" name="cal" value="<?php echo $this->item; ?>" />
 <input type="hidden" name="list" value="1" />
 <nobr>Search for: <input type="text" name="search" value="<?php echo esc_attr($_GET["search"]); ?>" /> &nbsp; &nbsp; &nbsp;</nobr> 
 <nobr>From: <input type="text" id="dfrom" name="dfrom" value="<?php echo esc_attr($_GET["dfrom"]); ?>" /> &nbsp; &nbsp; &nbsp; </nobr>
 <nobr>To: <input type="text" id="dto" name="dto" value="<?php echo esc_attr($_GET["dto"]); ?>" /> &nbsp; &nbsp; &nbsp; </nobr>
 <nobr>Item: <select id="cal" name="cal">
          <option value="0">[All Items]</option>
   <?php
    $myrows = $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix.$this->table_items );                                                                     
    foreach ($myrows as $item)  
         echo '<option value="'.$item->id.'"'.(intval($item->id)==intval($this->item)?" selected":"").'>'.$item->form_name.'</option>'; 
   ?>
    </select></nobr>
 <nobr><span class="submit"><input type="submit" name="ds" value="Filter" /></span> &nbsp; &nbsp; &nbsp; 
 <span class="submit"><input type="submit" name="<?php echo $this->prefix; ?>_csv" value="Export to CSV" /></span></nobr>
</form>

<br />
                             
<?php


echo paginate_links(  array(
    'base'         => 'admin.php?page=<?php echo $this->menu_parameter; ?>&cal='.$this->item.'&list=1%_%&dfrom='.urlencode($_GET["dfrom"]).'&dto='.urlencode($_GET["dto"]).'&search='.urlencode($_GET["search"]),
    'format'       => '&p=%#%',
    'total'        => $total_pages,
    'current'      => $current_page,
    'show_all'     => False,
    'end_size'     => 1,
    'mid_size'     => 2,
    'prev_next'    => True,
    'prev_text'    => __('&laquo; Previous'),
    'next_text'    => __('Next &raquo;'),
    'type'         => 'plain',
    'add_args'     => False
    ) );

?>

<div id="dex_printable_contents">
<table class="wp-list-table widefat fixed pages" cellspacing="0">
	<thead>
	<tr>
	  <th style="padding-left:7px;font-weight:bold;">Date</th>
	  <th style="padding-left:7px;font-weight:bold;">Email</th>
	  <th style="padding-left:7px;font-weight:bold;">Message</th>
	  <th style="padding-left:7px;font-weight:bold;"  class="cpnopr">Options</th>	
	</tr>
	</thead>
	<tbody id="the-list">
	 <?php for ($i=($current_page-1)*$records_per_page; $i<$current_page*$records_per_page; $i++) if (isset($events[$i])) { ?>
	  <tr class='<?php if (!($i%2)) { ?>alternate <?php } ?>author-self status-draft format-default iedit' valign="top">
		<td><?php echo substr($events[$i]->time,0,16); ?></td>
		<td><?php echo $events[$i]->notifyto; ?></td>
		<td><?php echo str_replace("\n","<br />",$events[$i]->data); ?></td>
		<td class="cpnopr">
		  <input type="button" name="caldelete_<?php echo $events[$i]->id; ?>" value="Delete" onclick="cp_deleteMessageItem(<?php echo $events[$i]->id; ?>);" />                             
		</td>
      </tr>
     <?php } ?>
	</tbody>
</table>
</div>

<p class="submit"><input type="button" name="pbutton" value="Print" onclick="do_dexapp_print();" /></p>

</div>


<script type="text/javascript">
 function do_dexapp_print()
 {
      w=window.open();
      w.document.write("<style>.cpnopr{display:none;};table{border:2px solid black;width:100%;}th{border-bottom:2px solid black;text-align:left}td{padding-left:10px;border-bottom:1px solid black;}</style>"+document.getElementById('dex_printable_contents').innerHTML);
      w.print();
      w.close();    
 }
 
 var $j = jQuery.noConflict();
 $j(function() {
 	$j("#dfrom").datepicker({     	                
                    dateFormat: 'yy-mm-dd'
                 });
 	$j("#dto").datepicker({     	                
                    dateFormat: 'yy-mm-dd'
                 });
 });
 
</script>














