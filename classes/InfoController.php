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

use Plib\SystemChecker;
use Plib\View;

class InfoController
{
    /** @var string */
    private $pluginFolder;

    /** @var SystemChecker */
    private $systemChecker;

    /** @var View */
    private $view;

    public function __construct(string $pluginFolder, SystemChecker $systemChecker, View $view)
    {
        $this->pluginFolder = $pluginFolder;
        $this->systemChecker = $systemChecker;
        $this->view = $view;
    }

    public function __invoke(): string
    {
        return "<h1>Pdeditor {$this->view->esc(Dic::VERSION)}</h1>\n"
            . "<h2>{$this->view->text("syscheck_title")}</h2>\n"
            . $this->systemChecks();
    }

    private function systemChecks(): string
    {
        $phpVersion = '7.1.0';
        $checks = [];
        $state = $this->systemChecker->checkVersion(PHP_VERSION, $phpVersion);
        $checks[] = $this->view->message(
            $state ? "success" : "fail",
            "syscheck_phpversion",
            $phpVersion,
            $state ? $this->view->plain("syscheck_good") : $this->view->plain("syscheck_bad")
        );
        foreach (array('pcre', 'spl') as $extension) {
            $state = $this->systemChecker->checkExtension($extension);
            $checks[] = $this->view->message(
                $state ? "success" : "fail",
                "syscheck_extension",
                $extension,
                $state ? $this->view->plain("syscheck_good") : $this->view->plain("syscheck_bad")
            );
        }
        $xhVersion = "1.7.0";
        $state = $this->systemChecker->checkVersion(CMSIMPLE_XH_VERSION, "CMSimple_XH $xhVersion");
        $checks[] = $this->view->message(
            $state ? "success" : "fail",
            "syscheck_xhversion",
            $xhVersion,
            $state ? $this->view->plain("syscheck_good") : $this->view->plain("syscheck_bad")
        );
        $folders = array();
        foreach (array('css/', 'languages/') as $folder) {
            $folders[] = $this->pluginFolder . $folder;
        }
        foreach ($folders as $folder) {
            $state = $this->systemChecker->checkWritability($folder);
            $checks[] = $this->view->message(
                $state ? "success" : "warning",
                "syscheck_writable",
                $folder,
                $state ? $this->view->plain("syscheck_good") : $this->view->plain("syscheck_bad")
            );
        }
        return implode("", $checks);
    }
}
