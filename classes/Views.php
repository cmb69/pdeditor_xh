<?php

/**
 * Copyright (c) Christoph M. Becker
 *
 * This file is part of Pdeditor_XH.
 *
 * Pdeditor_XH is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Pdeditor_XH is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Pdeditor_XH.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Pdeditor;

class Views
{
    /** @var Model */
    private $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    private function hsc(string $string): string
    {
        return XH_hsc($string);
    }

    private function attributeListItem(string $url, string $attribute): string
    {
        $url = $this->hsc($url . $attribute);
        return <<<EOT
<li><a href="$url">$attribute</a></li>
EOT;
    }

    private function attributeList(): string
    {
        $url = '?pdeditor&normal&admin=plugin_main&action=plugin_text'
            . '&pdeditor_attr=';
        $attributes = $this->model->pageDataAttributes();
        $items = '';
        foreach ($attributes as $attribute) {
            $items .= $this->attributeListItem($url, $attribute);
        }
        $o = <<<EOT
<ul id="pdeditor_attr">
    $items
</ul>
EOT;
        return $o;
    }

    private function warningIcon(): string
    {
        return "\xE2\x9A\xA0 ";
    }

    private function pageListItem(string $attribute, int $i): string
    {
        global $h;

        $pageDataAttribute = $this->model->pageDataAttribute($i, $attribute);
        if ($attribute == 'url' && !$this->model->isPagedataUrlUpToDate($i)) {
            $warning = $this->warningIcon();
        } else {
            $warning = '';
        }
        $value = $this->hsc($pageDataAttribute);
        $subpages = $this->pageList($this->model->childPages($i), $attribute);
        return <<<EOT
<li>
$warning$h[$i]<input type="text" name="value[]" value="$value" />$subpages
</li>
EOT;
    }

    private function pageList(array $pages, string $attribute): string
    {
        if (empty($pages)) {
            return '';
        }
        $items = '';
        foreach ($pages as $i) {
            $items .= $this->pageListItem($attribute, $i);
        }
        return <<<EOT
<ul>
    $items
</ul>
EOT;
    }

    public function administration(string $attribute, string $deleteUrl, string $action): string
    {
        global $tx, $plugin_tx, $_XH_csrfProtection;

        $ptx = $plugin_tx['pdeditor'];
        $attributes = $this->attributeList();
        $deleteUrl = $this->hsc($deleteUrl);
        $deleteWarning = addcslashes($ptx['warning_delete'], "\n\r\'\"\\");
        $action = $this->hsc($action);
        $saveWarning = addcslashes($ptx['warning_save'], "\n\r\'\"\\");
        $toplevelPages = $this->model->toplevelPages();
        $pageList = $this->pageList($toplevelPages, $attribute);
        $saveLabel = ucfirst($tx['action']['save']);
        $attributeLabel = sprintf($ptx['label_attribute'], $attribute);
        if (isset($_XH_csrfProtection)) {
            $tokenInput = $_XH_csrfProtection->tokenInput();
        } else {
            $tokenInput = '';
        }
        $o = <<<EOT
<h1>Pdeditor &ndash; $ptx[menu_main]</h1>
<h4 class="pdeditor_heading">$ptx[label_attributes]</h4>
$attributes
<h4 class="pdeditor_heading">$attributeLabel</h4>
<form id="pdeditor_delete" action="$deleteUrl$attribute&amp;edit" method="post"
      onsubmit="return window.confirm('$deleteWarning')">
    $tokenInput
    <button type="submit">$ptx[label_delete]</button>
</form>
<form id="pdeditor_attributes" action="$action$attribute&amp;edit" method="post"
      onsubmit="return window.confirm('$saveWarning')">
    $tokenInput
    <input type="submit" class="submit" value="$saveLabel" />
    $pageList
    <input type="submit" class="submit" value="$saveLabel" />
</form>
EOT;
        return $o;
    }
}
