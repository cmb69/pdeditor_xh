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

use Pdeditor\InfoController;
use Pdeditor\MainAdminControllerController;
use Plib\SystemChecker;
use Plib\View;

if (!defined('CMSIMPLE_XH_VERSION')) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}

define('PDEDITOR_VERSION', '1.0');

/**
 * @var string $action
 * @var string $admin
 * @var string $o
 * @var array<string,array<string,string>> $plugin_tx
 * @var array{folder:array<string,string>,file:array<string,string>} $pth
 */

XH_registerStandardPluginMenuItems(true);
if (XH_wantsPluginAdministration('pdeditor')) {
    $o .= print_plugin_admin('on');
    switch ($admin) {
        case '':
            $temp = new View($pth["folder"]["plugins"] . "pdeditor/views/", $plugin_tx["pdeditor"]);
            $o .= (new InfoController($pth["folder"]["plugins"] . "pdeditor/", new SystemChecker(), $temp))();
            break;
        case 'plugin_main':
            switch ($action) {
                case 'delete':
                    $o .= (new MainAdminControllerController())->deleteAttribute();
                    break;
                case 'save':
                    $o .= (new MainAdminControllerController())->save();
                    break;
                default:
                    $o .= (new MainAdminControllerController())->editor();
            }
            break;
        default:
            $o .= plugin_admin_common();
    }
}
