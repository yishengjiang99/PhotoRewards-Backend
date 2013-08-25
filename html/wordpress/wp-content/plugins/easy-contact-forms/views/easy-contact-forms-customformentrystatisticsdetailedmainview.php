<?php
/**
 * @file
 *
 * 	EasyContactFormsCustomFormEntryStatistics detailedMain view html
 * 	template
 *
 * 	@see EasyContactFormsCustomFormEntryStatistics
 * 	::getDetailedMainView()
 */

/*  Copyright championforms.com, 2012-2013 | http://championforms.com  
 * -----------------------------------------------------------
 * Easy Contact Forms
 *
 * This product is distributed under terms of the GNU General Public License. http://www.gnu.org/licenses/gpl-2.0.txt.
 * 
 */

?>
  <div>
    <div id='divCustomFormEntryStatisticsFilter' class='ufo-filter' style='width:330px;z-index:100'>
      <div class='ufofilterbutton'>
        <?php echo EasyContactFormsIHTML::getButton(
          array(
            'label' => EasyContactFormsT::get('Filter'),
            'events' => " onclick='ufo.filter($obj->jsconfig);'",
            'iclass' => " class='icon_filter_pane' ",
            'bclass' => "button internalimage",
          )
        );?>
      </div>
      <div class='ufo-clear-both'></div>
      <div>
        <div>
          <div>
            <label for='<?php echo $obj->sId('PageName');?>'><?php echo EasyContactFormsT::get('PageName');?></label>
            <select id='<?php echo $obj->sId('PageName');?>' class='ufo-select ufo-filtersign'>
              <?php echo $obj->sList('string');?>
            </select>
            <input type='text' id='PageName' class='textinput ufo-text ufo-filtervalue' style='width:130px'>
          </div>
          <div>
            <label for='<?php echo $obj->sId('CustomForms');?>'><?php echo EasyContactFormsT::get('CustomForms');?></label>
            <select id='<?php echo $obj->sId('CustomForms');?>' class='ufo-select ufo-filtersign'>
              <?php echo $obj->sList('ref');?>
            </select>
            <select id='CustomForms' class='inputselect ufo-select ufo-filtervalue' style='width:130px'>
              <?php echo $obj->getListHTML($obj->CustomForms,NULL, FALSE); ?>
            </select>
          </div>
          <div>
            <label for='IncludeIntoReporting'><?php echo EasyContactFormsT::get('ShowHidden');?></label>
            <input type='checkbox' id='IncludeIntoReporting' value='off' class='ufo-cb checkbox ufo-filtervalue' onchange='this.value=(this.checked)?"on":"off"'>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div>
    <div class='viewtable'>
      <table class='vtable'>
        <tr>
          <th>
            <?php echo EasyContactFormsT::get('CustomForms');?>
          </th>
          <th>
            <?php echo EasyContactFormsT::get('PageName');?>
          </th>
          <th>
            <?php echo EasyContactFormsT::get('Impressions');?>
          </th>
          <th>
            <?php echo EasyContactFormsT::get('TotalEntries');?>
          </th>
          <th>
            <?php echo EasyContactFormsT::get('Conversion');?>
          </th>
          <th>
            <?php echo EasyContactFormsT::get('Empty');?>
          </th>
          <th>
            &nbsp;
          </th>
        </tr>
        <?php EasyContactFormsLayout::getRows(
          $resultset,
          'EasyContactFormsCustomFormEntryStatistics',
          $obj,
          'easy-contact-forms-customformentrystatisticsdetailedmainviewrow.php',
          'getCustomFormEntryStatisticsDetailedMainViewRow',
          $viewmap
        );?>
      </table>
    </div>
  </div>
