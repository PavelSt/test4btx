<?
AddEventHandler('main', 'OnAdminTabControlBegin', 'MyOnAdminTabControlBegin');
function MyOnAdminTabControlBegin(&$form)
{

   if($GLOBALS['APPLICATION']->GetCurPage()=='/bitrix/admin/iblock_data_import.php')
   {                                                                               
      $form->tabs[] = array('DIV' => 'csv_import_div', 'TAB' => 'Import CSV', 'TITLE' => 'Import test CSV', 'CONTENT'=>
         '<tr>
	<td>
	    <input type=button value="Import Test CSV" onClick="document.getElementById(\'iFrame\').src=\'/local/parser.php\';">
            <p>
	    <iframe id=iFrame style="height:300px;border:0;width:800px;" src=\'/local/blank.php\'></iframe>
	</td>
	  </tr>'
      );
   }elseif($GLOBALS['APPLICATION']->GetCurPage()=='/bitrix/admin/iblock_data_import.php')
   {

   }
}


?>