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

<h1>Pdeditor – <?=$this->text("menu_main")?></h1>
<form id="pdeditor_attributes" action="<?=$this->esc($action)?>" method="post">
  <p class="xh_warning"><?=$this->text("warning_save", $attribute)?></p>
  <input type="hidden" name="pdeditor_token" value="<?=$this->esc($csrf_token)?>">
<?=$this->raw($pageList)?>
  <button name="pdeditor_do"><?=$this->text("label_save")?></button>
  <button name="pdeditor_do" formaction="<?=$this->esc($cancel)?>"><?=$this->text("label_cancel")?></button>
</form>
