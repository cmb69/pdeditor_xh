<?php

/**
 * Back-end functionality of Pdeditor_XH.
 *
 * PHP version 5
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
            $o .= $_Pdeditor->administration();
        }
        break;
    default:
        $o .= plugin_admin_common($action, $admin, $plugin);
    }
}

?>
