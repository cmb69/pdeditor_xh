<?php

use Plib\View;

if (!defined("CMSIMPLE_XH_VERSION")) {http_response_code(403); exit;}

/**
 * @var View $this
 * @var string $url
 * @var string $attribute
 * @var string $csrf_token
 * @var string $cancel
 */
?>

<h1><?=$this->text("title_delete", $attribute)?></h1>
<p class="xh_warning"><?=$this->text("warning_delete", $attribute)?></p>
<form id="pdeditor_delete" action="<?=$this->esc($url)?>" method="post">
  <input type="hidden" name="pdeditor_token" value="<?=$this->esc($csrf_token)?>">
  <p>
    <button name="pdeditor_do"><?=$this->text("label_delete")?></button>
    <button name="pdeditor_do" formaction="<?=$this->esc($cancel)?>"><?=$this->text("label_cancel")?></button>
  </p>
</form>
