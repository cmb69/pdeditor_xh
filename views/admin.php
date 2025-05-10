<?php

use Plib\View;

if (!defined("CMSIMPLE_XH_VERSION")) {http_response_code(403); exit;}

/**
 * @var View $this
 * @var string $attribute
 * @var string $attributes
 * @var string $deleteUrl
 * @var string $deleteWarning
 * @var string $action
 * @var string $saveWarning
 * @var string $csrf_token
 * @var string $pageList
 */
?>

<h1>Pdeditor – <?=$this->text("menu_main")?></h1>
<h4 class="pdeditor_heading"><?=$this->text("label_attributes")?></h4>
<ul id="pdeditor_attr">
<?=$this->raw($attributes)?>
</ul>
<h4 class="pdeditor_heading"><?=$this->text("label_attribute", $attribute)?></h4>
<form id="pdeditor_delete" action="<?=$this->esc($deleteUrl)?>" method="post" onsubmit="return window.confirm('<?=$this->esc($deleteWarning)?>')">
  <input type="hidden" name="pdeditor_token" value="<?=$this->esc($csrf_token)?>">
  <button type="submit"><?=$this->text("label_delete")?></button>
</form>
<form id="pdeditor_attributes" action="<?=$this->esc($action)?>" method="post" onsubmit="return window.confirm('<?=$this->esc($saveWarning)?>')">
  <input type="hidden" name="pdeditor_token" value="<?=$this->esc($csrf_token)?>">
  <input type="submit" class="submit" value="<?=$this->text("label_save")?>">
<?=$this->raw($pageList)?>
  <input type="submit" class="submit" value="<?=$this->text("label_save")?>">
</form>
