<?php

/**
 * The testing bootstrap.
 *
 * PHP version 5
 *
 * @category  CMSimple_XH
 * @package   Pdeditor
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2015 Christoph M. Becker <http://3-magi.net>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link      http://3-magi.net/?CMSimple_XH/Pdeditor_XH
 */

require_once './vendor/autoload.php';
require_once '../../cmsimple/functions.php';
if (file_exists('../../cmsimple/classes/PageDataRouter.php')) {
    include_once '../../cmsimple/classes/PageDataRouter.php';
} else {
    include_once '../pluginloader/page_data/page_data_router.php';
}

require_once "./classes/Model.php";
require_once "./classes/Views.php";
require_once "./classes/Controller.php";

function XH_saveContents(): bool
{
    return true;
}
