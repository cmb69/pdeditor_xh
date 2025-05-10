<?php

/**
 * Back-end functionality of Pdeditor_XH.
 *
 * PHP version 5
 *
 * @category  CMSimple_XH
 * @package   Pdeditor
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2012-2015 Christoph M. Becker <http://3-magi.net>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link      http://3-magi.net/?CMSimple_XH/Pdeditor_XH
 */

use Pdeditor\Controller;

if (!defined('CMSIMPLE_XH_VERSION')) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}

define('PDEDITOR_VERSION', '1.0');

new Controller();
