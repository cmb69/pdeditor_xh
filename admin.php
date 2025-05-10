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

use Pdeditor\Dic;
use Plib\Request;

if (!defined('CMSIMPLE_XH_VERSION')) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}

define('PDEDITOR_VERSION', '1.0');

/**
 * @var string $action
 * @var string $admin
 * @var string $o
 */

XH_registerStandardPluginMenuItems(true);
if (XH_wantsPluginAdministration('pdeditor')) {
    $o .= print_plugin_admin('on');
    switch ($admin) {
        case '':
            $o .= Dic::infoController()();
            break;
        case 'plugin_main':
            switch ($action) {
                case 'delete':
                    $o .= Dic::mainAdminController()->deleteAttribute(Request::current())();
                    break;
                case 'save':
                    $o .= Dic::mainAdminController()->save(Request::current())();
                    break;
                default:
                    $o .= Dic::mainAdminController()->editor(Request::current())();
            }
            break;
        default:
            $o .= plugin_admin_common();
    }
}
