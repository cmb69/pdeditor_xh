<?php

/**
 * The model class.
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
 * The model class.
 *
 * @category CMSimple_XH
 * @package  Pdeditor
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Pdeditor_XH
 */
class Pdeditor_Model
{
    public function pluginIconPath()
    {
        global $pth;

        return $pth['folder']['plugins'] . 'pdeditor/pdeditor.png';
    }
}

?>
