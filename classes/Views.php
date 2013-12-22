<?php

/**
 * The views class.
 *
 * PHP version 5
 *
 * @category  CMSimple_XH
 * @package   Pdeditor
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2011-2013 Christoph M. Becker <http://3-magi.net/>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @version   SVN: $Id$
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
     * @param string $string A string.
     *
     * @return string (X)HTML.
     *
     * @todo Make that independent of XH 1.6.
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
     * @return string (X)HTML.
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
     * Returns the system check view.
     *
     * @param array $checks An associative array of system checks (label => state).
     *
     * @return string (X)HTML.
     *
     * @global array The paths of system files and folders.
     * @global array The localization of the plugins.
     */
    public function systemCheck($checks)
    {
        global $pth, $plugin_tx;

        $ptx = $plugin_tx['pdeditor'];
        $imageFolder = $pth['folder']['plugins'] . 'pdeditor/images/';
        $o = <<<EOT
<h4>$ptx[syscheck_title]</h4>
<ul class="pdeditor_system_check">

EOT;
        foreach ($checks as $check => $state) {
            $o .= <<<EOT
    <li><img src="$imageFolder$state.png" alt="$state" /> $check</li>

EOT;
        }
        $o .= <<<EOT
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
        $o = <<<EOT
<h4>$ptx[info_about]</h4>
<img src="$iconPath" width="128" height="128" alt="Plugin Icon"
     style="float: left; margin-right: 16px">
<p>Version: $version</p>
<p>Copyright &copy; 2012-2013 <a href="http://3-magi.net">Christoph M. Becker</a></p>

EOT;
        $o .= $this->license();
        return $o;
    }

    /**
     * Returns an attribute selectbox.
     *
     * @param string $default An attribute name.
     *
     * @return string (X)HTML.
     */
    protected function attributeSelect($default)
    {
        $url = '?pdeditor&normal&admin=plugin_main&action=plugin_text'
            . '&pdeditor_attr=';
        $o = '<select id="pdeditor_attr" onchange="pdeditor_selectAttr(\''
            . $this->hsc($url) . '\')">' . PHP_EOL;
        $attributes = $this->model->pageDataAttributes();
        foreach ($attributes as $attribute) {
            $sel = ($attribute == $default) ? ' selected="selected"' : '';
            $o .= '<option' . $sel . '>' . $attribute . '</option>' . PHP_EOL;
        }
        $o .= '</select>' . PHP_EOL;
        return $o;
    }

    /**
     * Returns the view of all $pages displaying the attribute $attr.
     *
     * @param array  $pages     An array of page indexes.
     * @param string $attribute A page data attribute name.
     *
     * @return string (X)HTML.
     *
     * @global array  The page headings.
     * @global array  The paths of system files and folders.
     * @global object The page data router.
     * @global array  The localization of the plugins.
     */
    protected function pageList($pages, $attribute)
    {
        global $h, $pth, $pd_router, $plugin_tx;

        if (empty($pages)) {
            return '';
        }
        $ptx = $plugin_tx['pdeditor'];
        $o = PHP_EOL . '<ul>' . PHP_EOL;
        foreach ($pages as $i) {
            $pageData = $pd_router->find_page($i);
            if ($attribute == 'url' && !$this->model->isPagedataUrlUpToDate($i)) {
                $warning = <<<EOT
img src="{$pth['folder']['plugins']}pdeditor/images/warn.png"
    alt="$ptx[message_headings]" title="$ptx[message_headings]" />

EOT;
            } else {
                $warning = '';
            }
            $value = $this->hsc($pageData[$attribute]);
            $subpages = $this->pageList($this->model->childPages($i), $attribute);
            $o .= <<<EOT
<li>
$warning$h[$i]<input type="text" name="value[]" value="$value" />$subpages
</li>

EOT;
        }
        $o .= '</ul>' . PHP_EOL;
        return $this->xhtml($o);
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
     */
    public function administration($attribute, $deleteUrl, $action)
    {
        global $tx, $plugin_tx;

        $ptx = $plugin_tx['pdeditor'];
        $select = $this->attributeSelect($attribute);
        $deleteUrl = $this->hsc($deleteUrl);
        $deleteWarning = addcslashes($ptx['warning_delete'], "\n\r\'\"\\");
        $action = $this->hsc($action);
        $saveWarning = addcslashes($ptx['warning_save'], "\n\r\'\"\\");
        $toplevelPages = $this->model->toplevelPages();
        $pageList = $this->pageList($toplevelPages, $attribute);
        $saveLabel = ucfirst($tx['action']['save']);
        $o = <<<EOT
<div id="pdeditor">
    <table class="edit" style="width:100%">
        <tr>
            <td>
                <strong>$ptx[label_attributes]</strong>$select
            </td>
            <td>
                <a href="$deleteUrl$attribute"
                   onclick="return confirm('$deleteWarning')">
                $ptx[label_delete]</a>
            </td>
        </tr>
    </table>
    <form action="$action$attribute" method="post" accept-charset="UTF-8"
          onsubmit="return confirm('$saveWarning')">
        <input type="submit" class="submit" value="$saveLabel" />
        $pageList
        <input type="submit" class="submit" value="$saveLabel" />
    </form>
</div>

EOT;
        return $this->xhtml($o);
    }
}

?>
