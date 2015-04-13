<?if(!check_bitrix_sessid()) return;?>
<?
echo CAdminMessage::ShowNote("Installed");
?>
<form action="<?echo $APPLICATION->GetCurPage()?>">
	<input type="submit" name="" value="<?echo "Back" ?>">
<form>
