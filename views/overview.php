<?php

use Plib\View;

if (!defined("CMSIMPLE_XH_VERSION")) {http_response_code(403); exit;}

/**
 * @var View $this
 * @var string $attribute
 * @var list<object{name:string,selected:string}> $attributes
 */
?>

<h1>Pdeditor – <?=$this->text("menu_main")?></h1>
<form method="get">
  <input type="hidden" name="selected" value="pdeditor">
  <input type="hidden" name="admin" value="plugin_main">
  <label>
    <span><?=$this->text("label_attribute")?></span>
    <select name="pdeditor_attr" id="pdeditor_attr">
<?foreach ($attributes as $attr):?>
      <option <?=$this->esc($attr->selected)?>><?=$this->esc($attr->name)?></option>
<?endforeach?>
    </select>
  </label>
  <button name="action" value="update"><?=$this->text("label_edit")?></button>
  <button name="action" value="delete"><?=$this->text("label_delete")?></button>
</form>
