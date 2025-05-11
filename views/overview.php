<?php

use Plib\View;

if (!defined("CMSIMPLE_XH_VERSION")) {http_response_code(403); exit;}

/**
 * @var View $this
 * @var string $attribute
 * @var list<object{name:string,checked:string}> $attributes
 */
?>

<section class="pdeditor_overview">
  <h1><?=$this->text("title_attributes")?></h1>
  <form method="get">
    <input type="hidden" name="selected" value="pdeditor">
    <input type="hidden" name="admin" value="plugin_main">
    <ul>
<?foreach ($attributes as $attr):?>
      <li>
        <label>
          <input type="radio" name="pdeditor_attr" value="<?=$this->esc($attr->name)?>" <?=$this->esc($attr->checked)?>>
          <span><?=$this->esc($attr->name)?></span>
        </label>
      </li>
<?endforeach?>
    </ul>
    <p class="pdeditor_buttons">
      <button name="action" value="update"><?=$this->text("label_edit")?></button>
      <button name="action" value="delete"><?=$this->text("label_delete")?></button>
    </p>
  </form>
</section>
