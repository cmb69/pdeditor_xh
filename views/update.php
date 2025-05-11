<?php

use Plib\View;

if (!defined("CMSIMPLE_XH_VERSION")) {http_response_code(403); exit;}

/**
 * @var View $this
 * @var string $action
 * @var string $attribute
 * @var string $csrf_token
 * @var string $pageList
 * @var string $cancel
 */
?>

<section class="pdeditor_editor">
  <h1><?=$this->text("title_edit", $attribute)?></h1>
  <form class="pdeditor_attributes" action="<?=$this->esc($action)?>" method="post">
    <p class="xh_warning"><?=$this->text("warning_save", $attribute)?></p>
    <input type="hidden" name="pdeditor_token" value="<?=$this->esc($csrf_token)?>">
<?=$this->raw($pageList)?>
    <p class="pdeditor_buttons">
      <button name="pdeditor_do"><?=$this->text("label_save")?></button>
      <button name="pdeditor_do" formaction="<?=$this->esc($cancel)?>"><?=$this->text("label_cancel")?></button>
    </p>
  </form>
</section>
