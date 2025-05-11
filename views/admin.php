<?php

use Plib\View;

if (!defined("CMSIMPLE_XH_VERSION")) {http_response_code(403); exit;}

/**
 * @var View $this
 * @var string $attribute
 * @var list<object{name:string,selected:string}> $attributes
 * @var string $action
 * @var string $saveWarning
 * @var string $csrf_token
 * @var string $pageList
 */
?>

<h1>Pdeditor – <?=$this->text("menu_main")?></h1>
<form method="get">
  <input type="hidden" name="selected" value="pdeditor">
  <input type="hidden" name="admin" value="plugin_main">
  <input type="hidden" name="action" value="plugin_text">
  <label>
    <span><?=$this->text("label_attribute")?></span>
    <select name="pdeditor_attr" id="pdeditor_attr" onchange='this.form.submit()'>
<?foreach ($attributes as $attr):?>
      <option <?=$this->esc($attr->selected)?>><?=$this->esc($attr->name)?></option>
<?endforeach?>
    </select>
  </label>
  <button><?=$this->text("label_show")?></button>
  <button name="action" value="delete"><?=$this->text("label_delete")?></button>
</form>
<form id="pdeditor_attributes" action="<?=$this->esc($action)?>" method="post" onsubmit="return window.confirm('<?=$this->esc($saveWarning)?>')">
  <input type="hidden" name="pdeditor_token" value="<?=$this->esc($csrf_token)?>">
  <input type="submit" class="submit" value="<?=$this->text("label_save")?>">
<?=$this->raw($pageList)?>
  <input type="submit" class="submit" value="<?=$this->text("label_save")?>">
</form>
