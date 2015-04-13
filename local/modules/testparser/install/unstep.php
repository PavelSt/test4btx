<?if(!check_bitrix_sessid()) return;?>
<?
echo CAdminMessage::ShowNote("Removed");
?>
<form action="<?echo $APPLICATION->GetCurPage()?>">
	<input type="submit" name="" value="<?echo "Back"?>">
<form>
