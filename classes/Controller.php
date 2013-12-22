<?php

/**
 * The controller class.
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
 * The controller class.
 *
 * @category CMSimple_XH
 * @package  Pdeditor
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Pdeditor_XH
 */
class Pdeditor_Controller
{
    /**
     * The model.
     *
     * @var Pdeditor_Model
     *
     * @todo Make protected.
     */
    public $model;

    /**
     * The views.
     *
     * @var Pdeditor_Views
     *
     * @todo Make protected.
     */
    public $views;

    /**
     * Initializes a new instance.
     */
    public function __construct()
    {
        $this->model = new Pdeditor_Model();
        $this->views = new Pdeditor_Views($this->model);

    }

    /**
     * Returns the system checks.
     *
     * @return array
     *
     * @global array The paths of system files and folders.
     * @global array The localization of the core.
     * @global array The localization of the plugins.
     */
    protected function systemChecks()
    {
        global $pth, $tx, $plugin_tx;

        $phpVersion = '4.3.10';
        $ptx = $plugin_tx['pdeditor'];
        $checks = array();
        $checks[sprintf($ptx['syscheck_phpversion'], $phpVersion)]
            = version_compare(PHP_VERSION, $phpVersion) >= 0 ? 'ok' : 'fail';
        foreach (array() as $extension) {
            $checks[sprintf($ptx['syscheck_extension'], $ext)]
                = extension_loaded($extension) ? 'ok' : 'fail';
        }
        $checks[$ptx['syscheck_magic_quotes']]
            = !get_magic_quotes_runtime() ? 'ok' : 'fail';
        $checks[$ptx['syscheck_encoding']]
            = strtoupper($tx['meta']['codepage']) == 'UTF-8' ? 'ok' : 'warn';
        $folders = array();
        foreach (array('css/', 'languages/') as $folder) {
            $folders[] = $pth['folder']['plugins'] . 'pdeditor/' . $folder;
        }
        foreach ($folders as $folder) {
            $checks[sprintf($ptx['syscheck_writable'], $folder)]
                = is_writable($folder) ? 'ok' : 'warn';
        }
        return $checks;
    }

    /**
     * Returns the plugin info view.
     *
     * @return string (X)HTML.
     */
    public function info()
    {
        return $this->views->about()
            . $this->views->systemCheck($this->systemChecks());
    }

    /**
     * Saves the submitted page data and returns the main admin view.
     *
     * @return string (X)HTML.
     *
     * @todo Redirect instead of return main admin view?
     */
    public function save()
    {
        if (isset($_POST['value'])) {
            $attribute = $_GET['pdeditor_attr']; // TODO: sanitize
            $values = array_map('stsl', $_POST['value']);
            $this->model->updatePageData($attribute, $values);
        }
        return Pdeditor_adminMain();
    }

    /**
     * Deletes a page data attribute and returns the main admin view.
     *
     * @return string (X)HTML.
     *
     * @todo Stick with redirect or return adminMain()?
     */
    function deleteAttribute()
    {
        $attribute = stsl($_GET['pdeditor_attr']); // TODO: sanitize
        $this->model->deletePageDataAttribute($attribute);
        header('Location: ?&pdeditor&admin=plugin_main&action=plugin_text');
        exit;
        return Pdeditor_adminMain();
    }
}

?>
