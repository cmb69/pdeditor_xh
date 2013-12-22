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

require_once $pth['folder']['plugin_classes'] . 'Model.php';
require_once $pth['folder']['plugin_classes'] . 'Controller.php';
require_once $pth['folder']['plugin_classes'] . 'Views.php';


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
 * Returns the main administration view.
 *
 * @return string (X)HTML.
 *
 * @global string              The document fragment to insert into the head element.
 * @global array               The paths of system files and folders.
 * @global array               The page headings.
 * @global array               The page levels.
 * @global int                 The number of pages.
 * @global string              The script name.
 * @global array               The localization of the core.
 * @global object              The page data router.
 * @global array               The localization of the plugins.
 * @global Pdeditor_Controller The controller.
 */
function Pdeditor_adminMain()
{
    global $hjs, $pth, $h, $l, $cl, $sn, $tx, $pd_router, $plugin_tx, $_Pdeditor;

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
    $o .= $_Pdeditor->views->pageList($_Pdeditor->model->toplevelPages(), $attr)
        . tag(
            'input type="submit" class="submit" value="'
            . ucfirst($tx['action']['save']) . '"'
        ) . PHP_EOL
        . '</form>' . PHP_EOL
        . '</div>' . PHP_EOL;
    return $o;
}

$_Pdeditor = new Pdeditor_Controller();

/*
 * Handle the plugin administration.
 */
if (isset($pdeditor) && $pdeditor == 'true') {
    $o .= print_plugin_admin('on');
    switch ($admin) {
    case '':
        $o .= $_Pdeditor->info();
        break;
    case 'plugin_main':
        switch ($action) {
        case 'delete':
            $o .= $_Pdeditor->deleteAttribute();
            break;
        case 'save':
            $o .= $_Pdeditor->save();
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
