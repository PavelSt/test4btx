<?
@ini_set('zlib.output_compression',0);
@ini_set('implicit_flush',1);
@ob_end_clean();
@set_time_limit(0);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/iblock/iblock.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/catalog/catalog.php");


function AddVendorValue($ibNum,$Value){
	//Find Vendor_ID
	$prop = CIBlockProperty::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"), Array("IBLOCK_ID"=>$ibNum, "CODE"=>"VENDOR"));
	$res = $prop->GetNext();
	if (isset($res["ID"])){
		$PropId = $res["ID"];
	}else{
		return false;
	}

	$findFlad = false;
	$findID = 0;
	$propEnum = CIBlockPropertyEnum::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"), Array("IBLOCK_ID"=>$ibNum, "CODE"=>"VENDOR"));
	while($enumFields = $propEnum->GetNext())
	{
		  if ($enumFields["VALUE"]==$Value){
			$findFlag = true;
			$findID = $enumFields["ID"];
		  }
	}
	

	if(!$findFlag){
		$ibpenum = new CIBlockPropertyEnum;
		$findID = $ibpenum->Add(Array('PROPERTY_ID'=>$PropId,'VALUE'=>$Value));
		unset($ibpenum);
	}
	return $findID;
}



$res = CIBlock::GetList(
    Array(), 
    Array(
        'TYPE'=>'catalog',
	'NAME'=>'Parser'
    ), true
);

$ar_res = $res->Fetch();
$ibNum = $ar_res['ID'];
if ($ar_res) echo "Found: ".$ar_res['NAME'].' [id='.$ibNum."]<br>";

require_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/csv_data.php");
$csvFile = new CCSVData('R', true);
$csvFile->LoadFile($_SERVER["DOCUMENT_ROOT"]."/upload/import/import.csv");

$total_lines=0;

while ($arRes = $csvFile->Fetch()) {
$total_lines++;
}

//Disable all items in catalog

if ($total_lines!=0){
	$arSelect = Array("ID", "IBLOCK_ID", "ACTIVE"); 
	$arFilter = Array("IBLOCK_ID"=>$ibNum,);
	$res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
	
	while($ob = $res->GetNextElement())
	{      
	   $el = new CIBlockElement;
           $arFields = $ob->GetFields();  
	   $id=$arFields["ID"];
	   $arFields=Array("ACTIVE"=>"N");
	   $el->Update($id,$arFields);
	} 
}



$csvFile->setPos(0);
print "<pre>";
print "Total lines: ".($total_lines)."\n";
$divider=(int)($total_lines/10);
$line=0;
$inc=0;

print "\n";
$csvFile->Fetch();
$stopIDs = array(); //to skip duplicate IDs from CSV 
ob_implicit_flush(1);

while ($arRes = $csvFile->Fetch()) {
	//process
	//find from all items
	$line++;
	if ($line%$divider==0) {
		$inc++;
		ob_end_flush();
		flush();
		ob_flush();
		print "processing line $line ($total_lines)\n";
		ob_start();
	}



	$currEx_id = $arRes[0];
	$arSelect = Array("ID", "IBLOCK_ID","*"); 
	$arFilter = Array("IBLOCK_ID"=>$ibNum,);
	$res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
	
	$findFlg=false;
	$id=0;


	while($ob = $res->GetNextElement())
	{               
           $arFields = $ob->GetFields();  
	   $id=$arFields["ID"];
	   $ex_id=$arFields["EXTERNAL_ID"];
	   if ($ex_id==$currEx_id) {
		$findFlg=true;
		if(!in_array($ex_id,$stopIDs)){$stopIDs[]=$currEx_id;}
	   break;}
	} 

	if ($findFlg){
	//process item update 
	   $findFlg=false;
		   $el = new CIBlockElement;
	           $arFields = $ob->GetFields();  
		   $id=$arFields["ID"];
		   $arFields=Array("ACTIVE"=>"Y",
		   "PROPERTY_VALUES" => array(
		      	   "VENDOR" =>AddVendorValue($ibNum,$arRes[2]), //Производитель - свойство
			   "MATERIAL" =>$arRes[3], //Материал - свойство
			   "QUANTITY" =>$arRes[4] //Кол-во - свойство
     			   )
	           );
		   $el->Update($id,$arFields);

		   CPrice::DeleteByProduct($id);


                   CCatalogProduct::Delete(array('ID'=>$id)); 	
	  	   CPrice::SetBasePrice($id,(float)$arRes[5],"RUB");
 	 	   CCatalogProduct::Add(array('ID'=>$id)); 	



		   unset($el);
	}else{
	//add item 
 	   if(!in_array($currEx_id,$stopIDs)){
		$arFields = array(
		   "ACTIVE" => "Y", 
		   "IBLOCK_ID" => $ibNum,
		   "IBLOCK_SECTION_ID" => false,
		   "NAME" => $arRes[1],
		   "CODE" => $arRes[0],
		   "EXTERNAL_ID" => $arRes[0],
		   "PROPERTY_VALUES" => array(
		   "VENDOR" =>AddVendorValue($ibNum,$arRes[2]), //Производитель - свойство
		   "MATERIAL" =>$arRes[3], //Материал - свойство
		   "QUANTITY" =>$arRes[4] //Кол-во - свойство
		   )
		);

		$rr=CIBlockPropertyEnum::GetList(
		 array("SORT"=>"ASC", "VALUE"=>"ASC"),
		 array()
		);
		var_dump($rr);exit();

                $oElement = new CIBlockElement();
		$idElement = $oElement->Add($arFields, false, false, true); 

		CCatalogProduct::Add(array('ID'=>$idElement)); 	
		CPrice::SetBasePrice($idElement,(float)$arRes[5],"RUB");
		unset($oElement);
		$stopIDs[]=$currEx_id;
            }
		
	}

}
print "processing line $total_lines ($total_lines)\n";
print "</pre>";

?>

