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

namespace Pdeditor;

use Plib\CsrfProtector;
use Plib\SystemChecker;
use Plib\View;
use XH\Pages;

class Dic
{
    public const VERSION = "2.0-dev";

    public static function mainAdminController(): MainAdminController
    {
        global $pth;
        $csrfProtector = new CsrfProtector();
        return new MainAdminController(
            $pth["folder"]["plugins"] . "pdeditor/",
            self::model(),
            $csrfProtector,
            self::view()
        );
    }

    public static function infoController(): InfoController
    {
        global $pth;
        return new InfoController($pth["folder"]["plugins"] . "pdeditor/", new SystemChecker(), self::view());
    }

    private static function model(): Model
    {
        global $pd_router;
        return new Model(new Pages(), $pd_router);
    }

    private static function view(): View
    {
        global $pth, $plugin_tx;
        return new View($pth["folder"]["plugins"] . "pdeditor/views/", $plugin_tx["pdeditor"]);
    }
}
