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

class InfoController
{
    /** @var Views */
    private $views;

    public function __construct(Views $views)
    {
        $this->views = $views;
    }

    public function __invoke(): string
    {
        return '<h1>Pdeditor ' . PDEDITOR_VERSION . '</h1>'
            . $this->views->systemCheck($this->systemChecks());
    }

    private function systemChecks(): array
    {
        global $pth, $plugin_tx;

        $phpVersion = '7.1.0';
        $ptx = $plugin_tx['pdeditor'];
        $checks = array();
        $checks[sprintf($ptx['syscheck_phpversion'], $phpVersion)]
            = version_compare(PHP_VERSION, $phpVersion) >= 0 ? 'success' : 'fail';
        foreach (array('pcre', 'spl') as $extension) {
            $checks[sprintf($ptx['syscheck_extension'], $extension)]
                = extension_loaded($extension) ? 'success' : 'fail';
        }
        $xhVersion = "1.7.0";
        $checks[sprintf($ptx['syscheck_xhversion'], $xhVersion)]
            = version_compare(CMSIMPLE_XH_VERSION, "CMSimple_XH $xhVersion") >= 0 ? 'success' : 'warning'; // @phpstan-ignore-line
        $folders = array();
        foreach (array('css/', 'languages/') as $folder) {
            $folders[] = $pth['folder']['plugins'] . 'pdeditor/' . $folder;
        }
        foreach ($folders as $folder) {
            $checks[sprintf($ptx['syscheck_writable'], $folder)]
                = is_writable($folder) ? 'success' : 'warning';
        }
        return $checks;
    }
}
