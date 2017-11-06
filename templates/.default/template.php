<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(false);

?>
<form action="<?=POST_FORM_ACTION_URI?>" method="post" enctype="multipart/form-data">
<?=bitrix_sessid_post()?>
<input type="hidden" value="iblock_submit" name="iblock_submit">
<div>Добавление элемента</div>
	<?if (count($arResult["PROPERTY_LIST"]) > 0) {
		foreach ($arResult["PROPERTY_LIST"] as $key => $prop) {?>
			<div><?=(intval($prop) > 0)? $arResult["PROPERTY_LIST_FULL"][$prop]["NAME"]: GetMessage("IBLOCK_FIELD_".$prop);?></div>
			<?if (intval($prop) > 0){
				if ($arResult["PROPERTY_LIST_FULL"][$prop]["PROPERTY_TYPE"] == "S" && $arResult["PROPERTY_LIST_FULL"][$prop]["ROW_COUNT"] > "1")
						$arResult["PROPERTY_LIST_FULL"][$prop]["PROPERTY_TYPE"] = "T";
			} 
			if ($arResult["PROPERTY_LIST_FULL"][$prop]["USER_TYPE"]) {
				$INPUT_TYPE = $arResult["PROPERTY_LIST_FULL"][$prop]["USER_TYPE"];
			} else {
				$INPUT_TYPE = $arResult["PROPERTY_LIST_FULL"][$prop]["PROPERTY_TYPE"];
			}
			switch ($INPUT_TYPE):
								case "HTML":
								$LHE = new CHTMLEditor;
								$LHE->Show(array(
									'name' => "PROPERTY[".$prop."][0]",
									'id' => preg_replace("/[^a-z0-9]/i", '', "PROPERTY[".$prop."][0]"),
									'inputName' => "PROPERTY[".$prop."][0]",
									'content' => $arResult["ELEMENT"][$prop],
									'width' => '100%',
									'minBodyWidth' => 350,
									'normalBodyWidth' => 555,
									'height' => '200',
									'bAllowPhp' => false,
									'limitPhpAccess' => false,
									'autoResize' => true,
									'autoResizeOffset' => 40,
									'useFileDialogs' => false,
									'saveOnBlur' => true,
									'showTaskbars' => false,
									'showNodeNavi' => false,
									'askBeforeUnloadPage' => true,
									'bbCode' => false,
									'siteId' => SITE_ID,
									'controlsMap' => array(
										array('id' => 'Bold', 'compact' => true, 'sort' => 80),
										array('id' => 'Italic', 'compact' => true, 'sort' => 90),
										array('id' => 'Underline', 'compact' => true, 'sort' => 100),
										array('id' => 'Strikeout', 'compact' => true, 'sort' => 110),
										array('id' => 'RemoveFormat', 'compact' => true, 'sort' => 120),
										array('id' => 'Color', 'compact' => true, 'sort' => 130),
										array('id' => 'FontSelector', 'compact' => false, 'sort' => 135),
										array('id' => 'FontSize', 'compact' => false, 'sort' => 140),
										array('separator' => true, 'compact' => false, 'sort' => 145),
										array('id' => 'OrderedList', 'compact' => true, 'sort' => 150),
										array('id' => 'UnorderedList', 'compact' => true, 'sort' => 160),
										array('id' => 'AlignList', 'compact' => false, 'sort' => 190),
										array('separator' => true, 'compact' => false, 'sort' => 200),
										array('id' => 'InsertLink', 'compact' => true, 'sort' => 210),
										array('id' => 'InsertImage', 'compact' => false, 'sort' => 220),
										array('id' => 'InsertVideo', 'compact' => true, 'sort' => 230),
										array('id' => 'InsertTable', 'compact' => false, 'sort' => 250),
										array('separator' => true, 'compact' => false, 'sort' => 290),
										array('id' => 'Fullscreen', 'compact' => false, 'sort' => 310),
										array('id' => 'More', 'compact' => true, 'sort' => 400)
									),
								));
								break;
							case "T":
								?>
						<textarea cols="<?=$arResult["PROPERTY_LIST_FULL"][$prop]["COL_COUNT"]?>" rows="<?=$arResult["PROPERTY_LIST_FULL"][$prop]["ROW_COUNT"]?>" name="PROPERTY[<?=$prop?>]" style="<?=($arResult["PROPERTY_LIST_FULL"][$prop]["USER_TYPE_SETTINGS"]["height"]) ? "height:".$arResult["PROPERTY_LIST_FULL"][$prop]["USER_TYPE_SETTINGS"]["height"]: ''?>"></textarea>
								<?
								
							break;

							case "S":
							case "N":
								?>
								<input type="text" name="PROPERTY[<?=$prop?>]" size="<?=$arResult["PROPERTY_LIST_FULL"][$prop]["COL_COUNT"]; ?>" /><br /><?
							break;

							case "F":
									?>
						<input type="hidden" name="PROPERTY[<?=$prop?>]" value="" />
						<input type="file" size="<?=$arResult["PROPERTY_LIST_FULL"][$prop]["COL_COUNT"]?>"  name="PROPERTY_FILE_<?=$prop?>" /><br />
									<?
							break;
							case "L":

								if ($arResult["PROPERTY_LIST_FULL"][$prop]["LIST_TYPE"] == "C")
									$type = "radio";
								else
									$type = "dropdown";

								switch ($type):
									case "radio":
										foreach ($arResult["PROPERTY_LIST_FULL"][$prop]["ENUM"] as $key => $arEnum)
										{?>
							<input type="<?=$type?>" name="PROPERTY[<?=$prop?>]" value="<?=$key?>" id="property_<?=$key?>"><label for="property_<?=$key?>"><?=$arEnum["VALUE"]?></label>
											<?
										}
									break;

									case "dropdown":
									
									?>
							<select name="PROPERTY[<?=$prop?>]">
								<option value=""></option>
									<?
										foreach ($arResult["PROPERTY_LIST_FULL"][$prop]["ENUM"] as $key => $arEnum)
										{?>
											<option value="<?=$key?>"><?=$arEnum["VALUE"]?></option>
										<?}?>
							</select>
									<?
									break;

								endswitch;
							break;
						endswitch;?>
		<?}?>
		<?if (!empty($arResult["ERRORS"])){?>
			<?ShowError(implode("<br />", $arResult["ERRORS"]))?>
		<?}?>
		<?if (intval($arResult['SUCCESS']) > 0) {?>
			<div><?=GetMessage('SUCCESS');//Save?></div>
		<?}?>
		<input type="submit" name="iblock_submit" value="Сохранить" />
	<?}?>
</form>