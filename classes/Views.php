<?php

/**
 * The views class.
 *
 * PHP version 5
 *
 * @category  CMSimple_XH
 * @package   Pdeditor
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2012-2015 Christoph M. Becker <http://3-magi.net/>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link      http://3-magi.net/?CMSimple_XH/Pdeditor_XH
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
        if (function_exists('XH_hsc')) {
            return XH_hsc($string);
        } else {
            return htmlspecialchars($string, ENT_COMPAT, 'UTF-8');
        }
    }

    private function xhtml(string $string): string
    {
        global $cf;

        if (!$cf['xhtml']['endtags']) {
            $string = str_replace(' />', '>', $string);
        }
        return $string;
    }

    private function license(): string
    {
        return <<<EOT
<p class="pdeditor_license">This program is free software: you can redistribute it
and/or modify it under the terms of the GNU General Public License as published
by the Free Software Foundation, either version 3 of the License, or (at your
option) any later version.</p>
<p class="pdeditor_license">This program is distributed in the hope that it will be
useful, but <em>without any warranty</em>; without even the implied warranty of
<em>merchantability</em> or <em>fitness for a particular purpose</em>. See the
GNU General Public License for more details.</p>
<p class="pdeditor_license">You should have received a copy of the GNU General Public
License along with this program. If not, see <a
href="http://www.gnu.org/licenses/"> http://www.gnu.org/licenses/</a>.</p>
EOT;
    }

    private function systemCheckItem(string $check, string $state): string
    {
        global $pth;

        $imageFolder = $pth['folder']['plugins'] . 'pdeditor/images/';
        return <<<EOT
<li><img src="$imageFolder$state.png" alt="$state" /> $check</li>
EOT;
    }

    public function systemCheck(array $checks): string
    {
        global $plugin_tx;

        $ptx = $plugin_tx['pdeditor'];
        $items = array();
        foreach ($checks as $check => $state) {
            $items[] = $this->systemCheckItem($check, $state);
        }
        $items = implode('', $items);
        $o = <<<EOT
<h4>$ptx[syscheck_title]</h4>
<ul class="pdeditor_system_check">
    $items
</ul>
EOT;
        return $this->xhtml($o);
    }

    public function about(): string
    {
        global $plugin_tx;

        $ptx = $plugin_tx['pdeditor'];
        $iconPath = $this->model->pluginIconPath();
        $version = PDEDITOR_VERSION;
        $license = $this->license();
        $o = <<<EOT
<img src="$iconPath" class="pdeditor_logo" alt="$ptx[alt_logo]" />
<p>Version: $version</p>
<p>Copyright &copy; 2012-2015 <a href="http://3-magi.net">Christoph M. Becker</a></p>
$license
EOT;
        return $this->xhtml($o);
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
        return $this->xhtml($o);
    }

    private function warningIcon(): string
    {
        global $pth, $plugin_tx;

        $ptx = $plugin_tx['pdeditor'];
        return <<<EOT
<img src="{$pth['folder']['plugins']}pdeditor/images/warn.png"
    alt="$ptx[message_headings]" title="$ptx[message_headings]" />
EOT;
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
        return $this->xhtml($o);
    }
}
