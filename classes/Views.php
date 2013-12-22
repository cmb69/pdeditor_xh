<?php

/**
 * The views class.
 *
 * PHP version 5
 *
 * @category  CMSimple_XH
 * @package   Pdeditor
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2011-2013 Christoph M. Becker <http://3-magi.net/>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @version   SVN: $Id$
 * @link      http://3-magi.net/?CMSimple_XH/Pdeditor_XH
 */

/**
 * The views class.
 *
 * @category CMSimple_XH
 * @package  Pdeditor
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Pdeditor_XH
 */
class Pdeditor_Views
{
    /**
     * Returns a string with special (X)HTML characters escaped as entities.
     *
     * @param string $string A string.
     *
     * @return string (X)HTML.
     *
     * @since 0.1.0
     *
     * @todo Make that independent of XH 1.6.
     */
    protected function hsc($string)
    {
        if (function_exists('XH_hsc')) {
            return XH_hsc($string);
        } else {
            return htmlspecialchars($string, ENT_COMPAT, 'UTF-8');
        }
    }

    /**
     * Returns a string with ETAGCs adjusted to the configured markup language.
     *
     * @param string $string A string.
     *
     * @return string (X)HTML.
     *
     * @global array The configuration of the core.
     *
     * @since 0.1.0
     */
    protected function xhtml($string)
    {
        global $cf;

        if (!$cf['xhtml']['endtags']) {
            $string = str_replace(' />', '>', $string);
        }
        return $string;
    }

    /**
     * Returns summarized GPLv3 license information.
     *
     * @return string (X)HTML.
     *
     * @since 0.1.0
     */
    protected function license()
    {
        return <<<EOT
<p class="pdeditor_license">This program is free software: you can redistribute it
and/or modify it under the terms of the GNU General Public License as published
by the Free Software Foundation, either version 3 of the License, or (at your
option) any later version.</p>
<p class="pdeditor_license">This program is distributed in the hope that it will be
useful, but <em>without any warranty</em>; without even the implied warranty of
<em>merchantability</em> or <em>fitness for a particular purpose</em>. See the
GNU General Public License for more details.</p>
<p class="pdeditor_license">You should have received a copy of the GNU General Public
License along with this program. If not, see <a
href="http://www.gnu.org/licenses/"> http://www.gnu.org/licenses/</a>.</p>

EOT;
    }

    /**
     * Returns the system check view.
     *
     * @param array $checks An associative array of system checks (label => state).
     *
     * @return string (X)HTML.
     *
     * @global array The paths of system files and folders.
     * @global array The localization of the plugins.
     *
     * @since 0.1.0
     */
    public function systemCheck($checks)
    {
        global $pth, $plugin_tx;

        $ptx = $plugin_tx['pdeditor'];
        $imageFolder = $pth['folder']['plugins'] . 'pdeditor/images/';
        $o = <<<EOT
<h4>$ptx[syscheck_title]</h4>
<ul class="pdeditor_system_check">

EOT;
        foreach ($checks as $check => $state) {
            $o .= <<<EOT
    <li><img src="$imageFolder$state.png" alt="$state" /> $check</li>

EOT;
        }
        $o .= <<<EOT
</ul>

EOT;
        return $this->xhtml($o);
    }

    /**
     * Returns the plugin about information view.
     *
     * @return string (X)HTML.
     */
    public function about()
    {
        $version = PDEDITOR_VERSION;
        $o = <<<EOT
<h1><a href="http://3-magi.net/?CMSimple_XH/Pdeditor_XH">Pdeditor_XH</a></h1>
<p>Version: $version</p>
<p>Copyright &copy; 2012-2013 <a href="http://3-magi.net">Christoph M. Becker</a></p>

EOT;
        $o .= $this->license();
        return $o;
    }

}

?>
