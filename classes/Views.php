<?php

/**
 * The views class.
 *
 * PHP version 5
 *
 * @category  CMSimple_XH
 * @package   Pdeditor
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2012-2014 Christoph M. Becker <http://3-magi.net/>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link      http://3-magi.net/?CMSimple_XH/Pdeditor_XH
 */

/**
 * The views class.
 *
 * @category CMSimple_XH
 * @package  Pdeditor
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Pdeditor_XH
 */
class Pdeditor_Views
{
    /**
     * The model.
     *
     * @var Pdeditor_Model
     */
    protected $model;

    /**
     * Initializes a new instance.
     *
     * @param Pdeditor_Model $model The model.
     */
    public function __construct(Pdeditor_Model $model)
    {
        $this->model = $model;
    }

    /**
     * Returns a string with special (X)HTML characters escaped as entities.
     *
     * Uses a simplified fallback for CMSimple_XH < 1.6.
     *
     * @param string $string A string.
     *
     * @return string (X)HTML.
     */
    protected function hsc($string)
    {
        if (function_exists('XH_hsc')) {
            return XH_hsc($string);
        } else {
            return htmlspecialchars($string, ENT_COMPAT, 'UTF-8');
        }
    }

    /**
     * Returns a string with ETAGCs adjusted to the configured markup language.
     *
     * @param string $string A string.
     *
     * @return string (X)HTML.
     *
     * @global array The configuration of the core.
     */
    protected function xhtml($string)
    {
        global $cf;

        if (!$cf['xhtml']['endtags']) {
            $string = str_replace(' />', '>', $string);
        }
        return $string;
    }

    /**
     * Returns summarized GPLv3 license information.
     *
     * @return string XHTML.
     */
    protected function license()
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

    /**
     * Returns a single system check list item.
     *
     * @param string $check A label.
     * @param string $state A state.
     *
     * @return string XHTML.
     *
     * @global array The paths of system files and folders.
     */
    protected function systemCheckItem($check, $state)
    {
        global $pth;

        $imageFolder = $pth['folder']['plugins'] . 'pdeditor/images/';
        return <<<EOT
<li><img src="$imageFolder$state.png" alt="$state" /> $check</li>
EOT;
    }

    /**
     * Returns the system check view.
     *
     * @param array $checks An associative array of system checks (label => state).
     *
     * @return string (X)HTML.
     *
     * @global array The localization of the plugins.
     */
    public function systemCheck($checks)
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

    /**
     * Returns the plugin about information view.
     *
     * @return string (X)HTML.
     */
    public function about()
    {
        global $plugin_tx;

        $ptx = $plugin_tx['pdeditor'];
        $iconPath = $this->model->pluginIconPath();
        $version = PDEDITOR_VERSION;
        $license = $this->license();
        $o = <<<EOT
<h4>$ptx[info_about]</h4>
<img src="$iconPath" width="128" height="128" alt="Plugin Icon"
     style="float: left; margin-right: 16px" />
<p>Version: $version</p>
<p>Copyright &copy; 2012-2014 <a href="http://3-magi.net">Christoph M. Becker</a></p>
$license
EOT;
        return $this->xhtml($o);
    }

    /**
     * Returns an attribute list item.
     *
     * @param string $url       A partial URL.
     * @param string $attribute An attribute name.
     *
     * @return string XHTML.
     */
    protected function attributeListItem($url, $attribute)
    {
        $url = $this->hsc($url . $attribute);
        return <<<EOT
<li><a href="$url">$attribute</a></li>
EOT;
    }

    /**
     * Returns a list of attributes.
     *
     * @return string (X)HTML.
     */
    protected function attributeList()
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

    /**
     * Returns a warning icon.
     *
     * @return string XHTML.
     *
     * @global array The paths of system files and folders.
     * @global array The localization of the plugins.
     */
    protected function warningIcon()
    {
        global $pth, $plugin_tx;

        $ptx = $plugin_tx['pdeditor'];
        return <<<EOT
<img src="{$pth['folder']['plugins']}pdeditor/images/warn.png"
    alt="$ptx[message_headings]" title="$ptx[message_headings]" />
EOT;
    }

    /**
     * Returns a page list item.
     *
     * @param string $attribute An attribute name.
     * @param int    $i         A page index.
     *
     * @return string XHTML.
     *
     * @global array The headings of the pages.
     */
    protected function pageListItem($attribute, $i)
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

    /**
     * Returns the view of all $pages displaying the attribute $attr.
     *
     * @param array  $pages     An array of page indexes.
     * @param string $attribute A page data attribute name.
     *
     * @return string XHTML.
     *
     * @global array  The paths of system files and folders.
     * @global object The page data router.
     * @global array  The localization of the plugins.
     */
    protected function pageList($pages, $attribute)
    {
        global $pth, $pd_router, $plugin_tx;

        if (empty($pages)) {
            return '';
        }
        $ptx = $plugin_tx['pdeditor'];
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

    /**
     * Returns the main administration view.
     *
     * @param string $attribute An attribute name.
     * @param string $deleteUrl A URL.
     * @param string $action    A URL.
     *
     * @return string (X)HTML.
     *
     * @global array The localization of the core.
     * @global array The localization of the plugins.
     * @global object The CSRF protector.
     */
    public function administration($attribute, $deleteUrl, $action)
    {
        global $tx, $plugin_tx, $_XH_csrfProtection;

        $ptx = $plugin_tx['pdeditor'];
        $attributes = $this->attributeList($attribute);
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

?>
