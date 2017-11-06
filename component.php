<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/** @var CBitrixComponent $this */
/** @var array $arParams */
/** @var array $arResult */
/** @var string $componentPath */
/** @var string $componentName */
/** @var string $componentTemplate */
/** @global CDatabase $DB */
/** @global CUser $USER */
/** @global CMain $APPLICATION */
$this->setFrameMode(false);
if(!CModule::IncludeModule("iblock"))
{
	ShowError(GetMessage("CC_BIEAF_IBLOCK_MODULE_NOT_INSTALLED"));
	return;
}
/*-----Основные свойства------*/
$arResult["PROPERTY_LIST"] = array();
	$arResult["PROPERTY_LIST_FULL"] = array(
		"NAME" => array(
			"PROPERTY_TYPE" => "S",
			"MULTIPLE" => "N",
			"COL_COUNT" => "30",
		),

		"PREVIEW_TEXT" => array(
			"PROPERTY_TYPE" => "HTML",
			"MULTIPLE" => "N",
			"ROW_COUNT" => "12",
			"COL_COUNT" => "30",
		),
		"PREVIEW_PICTURE" => array(
			"PROPERTY_TYPE" => "F",
			"FILE_TYPE" => "jpg, gif, bmp, png, jpeg",
			"MULTIPLE" => "N",
		),
		"DETAIL_TEXT" => array(
			"PROPERTY_TYPE" => "HTML",
			"MULTIPLE" => "N",
			"ROW_COUNT" => "5",
			"COL_COUNT" => "30",
		),
		"DETAIL_PICTURE" => array(
			"PROPERTY_TYPE" => "F",
			"FILE_TYPE" => "jpg, gif, bmp, png, jpeg",
			"MULTIPLE" => "N",
		),
	);

/*------Добавление элементов в список-----*/
foreach ($arResult["PROPERTY_LIST_FULL"] as $key => $arr)
{
	if (in_array($key, $arParams["FIELD_CODE"])) $arResult["PROPERTY_LIST"][] = $key;
}

if ($arParams["IBLOCK_ID"]) {
	/*------Пользовательские свойства-----*/
	$rsIBLockPropertyList = CIBlockProperty::GetList(array("sort"=>"asc", "name"=>"asc"), array("ACTIVE"=>"Y", "IBLOCK_ID"=>$arParams["IBLOCK_ID"]));
		while ($arProperty = $rsIBLockPropertyList->GetNext())
		{
			if ($arProperty["PROPERTY_TYPE"] == "L")
			{
				$rsPropertyEnum = CIBlockProperty::GetPropertyEnum($arProperty["ID"]);
				$arProperty["ENUM"] = array();
				while ($arPropertyEnum = $rsPropertyEnum->GetNext())
				{
					$arProperty["ENUM"][$arPropertyEnum["ID"]] = $arPropertyEnum;
				}
			}

			if (in_array($arProperty["CODE"], $arParams["PROPERTY_CODE"]))
				$arResult["PROPERTY_LIST"][] = $arProperty["ID"];

			$arResult["PROPERTY_LIST_FULL"][$arProperty["ID"]] = $arProperty;

		}
	/*-----Добавление элемента----*/
	$IMAGE_WIDHT = 500;
	$IMAGE_HEIGHT = 500;
	if ($arParams["IMAGE_WIDHT"]) {
		$IMAGE_WIDHT = $arParams["IMAGE_WIDHT"];
	}
	if ($arParams["IMAGE_HEIGHT"]) {
		$IMAGE_HEIGHT = $arParams["IMAGE_HEIGHT"];
	}
	$i= 0;
	if (check_bitrix_sessid() && (!empty($_REQUEST["iblock_submit"]))) {
			$arProp = $_REQUEST["PROPERTY"];
			$arUpdateVal = array();
			foreach ($arResult["PROPERTY_LIST"] as $prop)
			{
				$arPropVal = $arProp[$prop];

				if (intval($prop) > 0)
				{
					if ($arResult["PROPERTY_LIST_FULL"][$prop]["PROPERTY_TYPE"] != "F")
					{
						if ($arResult["PROPERTY_LIST_FULL"][$prop]["PROPERTY_TYPE"] != "L") {
							if (is_array($arPropVal["VALUE"])) {
									$PROP[$prop]["TYPE"] = $arPropVal["VALUE"]["TYPE"];
									$PROP[$prop]["TEXT"] = $arPropVal["VALUE"]["TEXT"];
								} else {
									$PROP[$prop] = $arPropVal;
								}
							} else {
								$PROP[$prop] = $arPropVal;
							}	
					}
					else
					{
						if ($_FILES["PROPERTY_FILE_".$prop]) {
							$file[$i] = CFile::SaveFile($_FILES["PROPERTY_FILE_".$prop]);
							$arWaterMark = Array(
					            array(
					                "name" => "watermark",
					                "position" => "bottomright", // Положение
					                "type" => "image",
					                "size" => "real",
					                "file" => $_SERVER["DOCUMENT_ROOT"].'/upload/11.jpg', // Путь к картинке
					                "fill" => "exact",
					            )
					        );
							$water = CFile::ResizeImageGet(
					            $file[$i],
					            array("width" => $arParams["IMAGE_WIDHT"], "height" => $arParams["IMAGE_HEIGHT"]),
					            BX_RESIZE_IMAGE_PROPORTIONAL,
					            true,
					            $arWaterMark
					        );
					        $PROP[$prop] = CFile::MakeFileArray($water['src']);

						}
					}
				} else {
					if($arResult["PROPERTY_LIST_FULL"][$prop]["PROPERTY_TYPE"] == "F")
						{
							$file[$i] = CFile::SaveFile($_FILES["PROPERTY_FILE_".$prop]);
							$arWaterMark = Array(
					            array(
					                "name" => "watermark",
					                "position" => "bottomright", // Положение
					                "type" => "image",
					                "size" => "real",
					                "file" => $_SERVER["DOCUMENT_ROOT"].'/upload/11.jpg', // Путь к картинке
					                "fill" => "exact",
					            )
					        );
							$water = CFile::ResizeImageGet(
					            $file[$i],
					            array("width" => $arParams["IMAGE_WIDHT"], "height" => $arParams["IMAGE_HEIGHT"]),
					            BX_RESIZE_IMAGE_PROPORTIONAL,
					            true,
					            $arWaterMark
					        );
							$arUpdateVal[$prop] = CFile::MakeFileArray($water['src']);
						}
						elseif($arResult["PROPERTY_LIST_FULL"][$prop]["PROPERTY_TYPE"] == "HTML")
						{
							$arUpdateVal[$prop] = $arProp[$prop][0];
						}
						else
						{
							$arUpdateVal[$prop] = $arProp[$prop];
						}
				}
				$i++;
			}
			$name = 'Элемент';
				if ($arUpdateVal["NAME"]) {
					$name = $arUpdateVal["NAME"];
				}

				$arAddElement = Array(
				  "IBLOCK_ID"      => $arParams["IBLOCK_ID"],
				  "PROPERTY_VALUES"=> $PROP,
				  "NAME"           => $name,
				  "CODE" 		   => CUtil::translit($name),
				  "ACTIVE"         => "Y",            // активен
				  "PREVIEW_TEXT"   => $arUpdateVal["PREVIEW_TEXT"],
				  "DETAIL_TEXT"    => $arUpdateVal["DETAIL_TEXT"],
				  "DETAIL_PICTURE" => $arUpdateVal['DETAIL_PICTURE'],
				  "PREVIEW_PICTURE" => $arUpdateVal['PREVIEW_PICTURE'],
				  );
				$el = new CIBlockElement();
				if (!$arResult["SUCCESS"] = $el->Add($arAddElement))
					{
						$arResult["ERRORS"][] = $el->LAST_ERROR;
					} 
				/*--Удаляем картинки--*/
					foreach ($file as $key => $f) {
						CFile::Delete($f);
					}
	}
} else {
	die(GetMessage("NOT_IBLOCK_ID"));
}
$this->IncludeComponentTemplate();
?>