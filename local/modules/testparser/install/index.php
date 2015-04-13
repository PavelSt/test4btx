<?
global $MESS;

Class TestParser extends CModule
{
	var $MODULE_ID = "testparser";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;

	function compression()
	{
		$arModuleVersion = array();

		$path = str_replace("\\", "/", __FILE__);
		$path = substr($path, 0, strlen($path) - strlen("/index.php"));
		include($path."/version.php");

		if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion))
		{
			$this->MODULE_VERSION = $arModuleVersion["VERSION"];
			$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		}
		else
		{
			$this->MODULE_VERSION = COMPRESSION_VERSION;
			$this->MODULE_VERSION_DATE = COMPRESSION_VERSION_DATE;
		}

		$this->MODULE_NAME = "Test_Parser";
		$this->MODULE_DESCRIPTION = "Test Parser. Level 2";
	}

	function InstallDB($arParams = array())
	{
		RegisterModule("testparser");
		RegisterModuleDependences("main", "OnPageStart", "testparser", "TestParser", "OnPageStart", 1);
		RegisterModuleDependences("main", "OnAfterEpilog", "testparser", "TestParser", "OnAfterEpilog", 10000);

		return true;
	}

	function UnInstallDB($arParams = array())
	{
		UnRegisterModuleDependences("main", "OnPageStart", "testparser", "TestParser", "OnPageStart");
		UnRegisterModuleDependences("main", "OnAfterEpilog", "testparser", "TestParser", "OnAfterEpilog");
		UnRegisterModule("testparser");

		return true;
	}

	function InstallEvents()
	{
		return true;
	}

	function UnInstallEvents()
	{
		return true;
	}

	function InstallFiles($arParams = array())
	{
		return true;
	}

	function UnInstallFiles()
	{
		return true;
	}

	function DoInstall()
	{
		global $DOCUMENT_ROOT, $APPLICATION;
		$this->InstallDB();
		$APPLICATION->IncludeAdminFile("Installing ...", $DOCUMENT_ROOT."/local/modules/testparser/install/step.php");
	}

	function DoUninstall()
	{
		global $DOCUMENT_ROOT, $APPLICATION;
		$this->UnInstallDB();
		$APPLICATION->IncludeAdminFile("Removing...", $DOCUMENT_ROOT."/local/modules/testparser/install/unstep.php");
	}
}
?>