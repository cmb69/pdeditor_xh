<?php

/**
 * Back-end functionality of Pdeditor_XH.
 *
 * PHP versions 4 and 5
 *
 * @category  CMSimple_XH
 * @package   Pdeditor
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2012-2013 Christoph M. Becker <http://3-magi.net>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @version   SVN: $Id$
 * @link      http://3-magi.net/?CMSimple_XH/Pdeditor_XH
 */

/*
 * Prevent direct access.
 */

if (!defined('CMSIMPLE_XH_VERSION')) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}

/**
 * The version number of the plugin.
 */
define('PDEDITOR_VERSION', '@PDEDITOR_VERSION@');

/**
 * Returns the plugin about information view.
 *
 * @return string (X)HTML.
 */
function Pdeditor_version()
{
    $version = PDEDITOR_VERSION;
    return <<<EOT
<h1><a href="http://3-magi.net/?CMSimple_XH/Pdeditor_XH">Pdeditor_XH</a></h1>
<p>Version: $version</p>
<p>Copyright &copy; 2012-2013 <a href="http://3-magi.net">Christoph M. Becker</a></p>
<p style="text-align: justify">
    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.
</p>
<p style="text-align: justify">
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHAN&shy;TABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
</p>
<p style="text-align: justify">
    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <a href="http://www.gnu.org/licenses/">
    http://www.gnu.org/licenses/</a>.
</p>

EOT;
}

/**
 * Returns the system check view.
 *
 * @return string (X)HTML.
 *
 * @global array The paths of system files and folders.
 * @global array The localization of the core.
 * @global array The localization of the plugins.
 */
function Pdeditor_systemCheck()
{
    global $pth, $tx, $plugin_tx;

    $phpVersion = '4.3.10';
    $ptx = $plugin_tx['pdeditor'];
    $imgdir = $pth['folder']['plugins'] . 'pdeditor/images/';
    $ok = tag('img src="' . $imgdir . 'ok.png" alt="ok"');
    $warn = tag('img src="' . $imgdir . 'warn.png" alt="warning"');
    $fail = tag('img src="' . $imgdir . 'fail.png" alt="failure"');
    $o = tag('hr') . '<h4>' . $ptx['syscheck_title'] . '</h4>'
        . (version_compare(PHP_VERSION, $phpVersion) >= 0 ? $ok : $fail)
        . '&nbsp;&nbsp;' . sprintf($ptx['syscheck_phpversion'], $phpVersion)
        . tag('br') . tag('br')  . PHP_EOL;
    foreach (array() as $ext) {
        $o .= (extension_loaded($ext) ? $ok : $fail)
            . '&nbsp;&nbsp;' . sprintf($ptx['syscheck_extension'], $ext)
            . tag('br') . PHP_EOL;
    }
    $o .= (!get_magic_quotes_runtime() ? $ok : $fail)
        . '&nbsp;&nbsp;' . $ptx['syscheck_magic_quotes'] . tag('br') . PHP_EOL;
    $o .= (strtoupper($tx['meta']['codepage']) == 'UTF-8' ? $ok : $warn)
        . '&nbsp;&nbsp;' . $ptx['syscheck_encoding']
        . tag('br') . tag('br') . PHP_EOL;
    foreach (array('config/', 'css/', 'languages/') as $folder) {
        $folders[] = $pth['folder']['plugins'] . 'pdeditor/' . $folder;
    }
    foreach ($folders as $folder) {
        $o .= (is_writable($folder) ? $ok : $warn)
            . '&nbsp;&nbsp;' . sprintf($ptx['syscheck_writable'], $folder)
            . tag('br') . PHP_EOL;
    }
    return $o;
}

/**
 * Returns an attribute selectbox.
 *
 * @param string $default An attribute name.
 *
 * @return string (X)HTML.
 *
 * @global string The script name.
 * @global object The page data router.
 */
function Pdeditor_attrSelect($default)
{
    global $sn, $pd_router;

    $url = '?pdeditor&amp;normal&amp;admin=plugin_main&amp;action=plugin_text'
        . '&amp;pdeditor_attr=';
    $o = '<select id="pdeditor_attr" onchange="pdeditor_selectAttr(\''
        . $url . '\')">' . PHP_EOL;
    $attrs = $pd_router->model->params;
    natcasesort($attrs);
    foreach ($attrs as $attr) {
        $sel = ($attr == $default) ? ' selected="selected"' : '';
        $o .= '<option' . $sel . '>' . $attr . '</option>' . PHP_EOL;
    }
    $o .= '</select>' . PHP_EOL;
    return $o;
}

/**
 * Returns an array of indexes of the toplevel pages.
 *
 * @return array
 *
 * @global int   The number of pages.
 * @global array The page levels.
 */
function Pdeditor_toplevelPages()
{
    global $cl, $l;

    for ($i = 0; $i < $cl; $i++) {
        if ($l[$i] == 1) {
            $ta[] = $i;
        }
    }
    return $ta;
}

/**
 * Returns an array of indexes of child pages of a page.
 *
 * @param int $i A page index.
 *
 * @return array
 *
 * @global int   The number of pages.
 * @global array The page levels.
 * @global arras The configuration of the core.
 */
function Pdeditor_childPages($i)
{
    global $cl, $l, $cf;

    $ta = array();
    $lc = $cf['menu']['levelcatch'];
    for ($j = $i+1; $j < $cl && $l[$j] > $l[$i]; $j++) {
        if ($l[$j] <= $lc) {
            $ta[] = $j;
            $lc = $l[$j];
        }
    }
    return $ta;
}

/**
 * Returns the view of all $pages displaying the attribute $attr.
 *
 * @param array  $pages An array of page indexes.
 * @param string $attr  A page data attribute name.
 *
 * @return string (X)HTML.
 *
 * @global array  The page headings.
 * @global int    The number of pages.
 * @global array  The page levels.
 * @global array  The paths of system files and folders.
 * @global object The page data router.
 * @global array  The localization of the plugins.
 */
function Pdeditor_pageList($pages, $attr)
{
    global $h, $cl, $l, $pth, $pd_router, $plugin_tx;

    if (empty($pages)) {
        return '';
    }
    $ptx = $plugin_tx['pdeditor'];
    $warn = tag(
        'img src="' . $pth['folder']['plugins'] . 'pdeditor/images/warn.png"'
        . ' alt="' . $ptx['message_headings'] . '" title="'
        . $ptx['message_headings'] . '"'
    ) .' ';
    $o = PHP_EOL . '<ul>' . PHP_EOL;
    $pd = $pd_router->find_all();
    foreach ($pages as $i) {
        //$has_children = $i+1 < $cl && $l[$i+1] > $l[$i];
        $level = 'level' . $l[$i];
        $o .= '<li>'
            . ($attr == 'url' && uenc($h[$i]) != $pd[$i]['url'] ? $warn : '')
            . $h[$i]
            . tag(
                'input type="text" name="value[]" value="'
                . htmlspecialchars($pd[$i][$attr]) . '"'
            )
            . Pdeditor_pageList(Pdeditor_childPages($i), $attr)
            . '</li>' . PHP_EOL;
    }
    $o .= '</ul>' . PHP_EOL;
    return $o;
}

/**
 * Saves the posted page data and returns the main admin view.
 *
 * @return string (X)HTML.
 *
 * @global int    The number of pages.
 * @global object The page data router.
 */
function Pdeditor_adminSave()
{
    global $cl, $pd_router;

    if (isset($_POST['value'])) {
        $pd = $pd_router->find_all();
        foreach ($_POST['value'] as $id => $value) {
            $pd[$id][$_GET['pdeditor_attr']] = stsl($value);
        }
        $pd_router->model->refresh($pd);
    }
    return Pdeditor_adminMain();
}

/**
 * Deletes a page data attribute and returns the main admin view.
 *
 * @return string (X)HTML.
 *
 * @global object The page data router.
 *
 * @todo Stick with redirect or return adminMain()?
 */
function Pdeditor_deleteAttr()
{
    global $pd_router;

    $attr = stsl($_GET['pdeditor_attr']);
    $key = array_search($attr, $pd_router->model->params);
    if ($key !== false) {
        unset($pd_router->model->params[$key]);
    }
    for ($i = 0; $i < count($pd_router->model->data); $i++) {
        unset($pd_router->model->data[$i][$attr]);
    }
    unset($pd_router->model->temp_data[$attr]);
    $pd_router->model->save();
    header('Location: ?&pdeditor&admin=plugin_main&action=plugin_text');
    exit;
    return Pdeditor_adminMain();
}

/**
 * Returns the main administration view.
 *
 * @return string (X)HTML.
 *
 * @global string The document fragment to insert into the head element.
 * @global array  The paths of system files and folders.
 * @global array  The page headings.
 * @global array  The page levels.
 * @global int    The number of pages.
 * @global string The script name.
 * @global array  The localization of the core.
 * @global object The page data router.
 * @global array  The localization of the plugins.
 */
function Pdeditor_adminMain()
{
    global $hjs, $pth, $h, $l, $cl, $sn, $tx, $pd_router, $plugin_tx;

    $ptx = $plugin_tx['pdeditor'];
    $hjs .= '<script type="text/javascript" src="' . $pth['folder']['plugins']
        . 'pdeditor/pdeditor.js"></script>' . PHP_EOL;
    $attr = isset($_GET['pdeditor_attr']) ? $_GET['pdeditor_attr'] : 'url';
    $o = '';
    $o .= '<div id="pdeditor">' . PHP_EOL
        . '<table class="edit" style="width:100%">' . PHP_EOL
        . '<tr>' . PHP_EOL
        . '<td>' . PHP_EOL
        . '<strong>' . $ptx['label_attributes'] . '</strong> ' . PHP_EOL
        . Pdeditor_attrSelect($attr) . '</td>' . PHP_EOL
        . '<td><a href="?pdeditor&amp;admin=plugin_main&amp;action=delete'
        . '&amp;pdeditor_attr=' .$attr . '" onclick="return confirm(\''
        . addcslashes($ptx['warning_delete'], "\n\r\'\"\\") . '\')">'
        . $ptx['label_delete'] . '</a></td>' . PHP_EOL
        . '</tr>' . PHP_EOL . '</table>' . PHP_EOL
        . '<form action="?pdeditor&amp;admin=plugin_main&amp;action=save'
        . '&amp;pdeditor_attr=' .$attr . '" method="POST" accept-charset="UTF-8"'
        . ' onsubmit="return confirm(\''
        . addcslashes($ptx['warning_save'], "\n\r\'\"\\") . '\')">';
    $o .= Pdeditor_pageList(Pdeditor_toplevelPages(), $attr)
        . tag(
            'input type="submit" class="submit" value="'
            . ucfirst($tx['action']['save']) . '"'
        ) . PHP_EOL
        . '</form>' . PHP_EOL
        . '</div>' . PHP_EOL;
    return $o;
}

/*
 * Handle the plugin administration.
 */
if (isset($pdeditor) && $pdeditor == 'true') {
    $o .= print_plugin_admin('on');
    switch ($admin) {
    case '':
        $o .= Pdeditor_version() . Pdeditor_systemCheck();
        break;
    case 'plugin_main':
        switch ($action) {
        case 'delete':
            $o .= Pdeditor_deleteAttr();
            break;
        case 'save':
            $o .= Pdeditor_adminSave();
            break;
        default:
            $o .= Pdeditor_adminMain();
        }
        break;
    default:
        $o .= plugin_admin_common($action, $admin, $plugin);
    }
}

?>
