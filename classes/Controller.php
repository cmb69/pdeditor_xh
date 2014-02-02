<?php

/**
 * The controller class.
 *
 * PHP version 5
 *
 * @category  CMSimple_XH
 * @package   Pdeditor
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2012-2014 Christoph M. Becker <http://3-magi.net/>
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
     */
    protected $model;

    /**
     * The views.
     *
     * @var Pdeditor_Views
     */
    protected $views;

    /**
     * Initializes a new instance.
     */
    public function __construct()
    {
        $this->model = new Pdeditor_Model();
        $this->views = new Pdeditor_Views($this->model);
        $this->dispatch();
    }

    /**
     * Returns the fully qualified URL of the CMSimple_XH installation folder.
     *
     * @return string
     *
     * @global string The script name.
     */
    protected function baseUrl()
    {
        global $sn;

        return 'http'
            . (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 's' : '')
            . '://' . $_SERVER['HTTP_HOST']
            . preg_replace('/index\.php$/', '', $sn);
    }

    /**
     * Dispatches on current request.
     *
     * @return void
     *
     * @global string Whether the administration of the plugin is requested.
     */
    protected function dispatch()
    {
        global $adm, $pdeditor;

        if ($adm && isset($pdeditor) && $pdeditor == 'true') {
            $this->administration();
        }
    }

    /**
     * Handles the plugin administration.
     *
     * @return void
     *
     * @global string The document fragment to use for the contents area.
     * @global string The value of the admin GP parameter.
     * @global string The value of the action GP parameter.
     */
    protected function administration()
    {
        global $o, $admin, $action;

        $o .= print_plugin_admin('on');
        switch ($admin) {
        case '':
            $o .= $this->info();
            break;
        case 'plugin_main':
            switch ($action) {
            case 'delete':
                $o .= $this->deleteAttribute();
                break;
            case 'save':
                $o .= $this->save();
                break;
            default:
                $o .= $this->editor();
            }
            break;
        default:
            $o .= plugin_admin_common($action, $admin, 'pdeditor');
        }
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

        $phpVersion = '5.0.0';
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
     *
     * @global array The localization of the core.
     */
    public function info()
    {
        global $plugin_tx;

        $ptx = $plugin_tx['pdeditor'];
        $o = '<h1>Pdeditor &ndash; ' . $ptx['info_heading'] . '</h1>'
            . $this->views->systemCheck($this->systemChecks())
            . $this->views->about();
        return $o;
    }

    /**
     * Saves the submitted page data and returns the main admin view.
     *
     * @return string (X)HTML.
     */
    public function save()
    {
        if (isset($_POST['value'])) {
            $attribute = stsl($_GET['pdeditor_attr']);
            $values = array_map('stsl', $_POST['value']);
            $this->model->updatePageData($attribute, $values);
            $url = $this->baseUrl()
                . '?&pdeditor&admin=plugin_main&action=plugin_text&pdeditor_attr='
                . $attribute;
            header('Location: ' . $url);
            exit;
        }
        return $this->editor();
    }

    /**
     * Deletes a page data attribute and returns the main admin view.
     *
     * @return string (X)HTML.
     */
    public function deleteAttribute()
    {
        $attribute = stsl($_GET['pdeditor_attr']);
        $this->model->deletePageDataAttribute($attribute);
        $url = $this->baseUrl()
            . '?&pdeditor&admin=plugin_main&action=plugin_text';
        header('Location: ' . $url);
        exit;
    }

    /**
     * Returns the main administration view.
     *
     * @return string (X)HTML.
     *
     * @global string The document fragment to insert into the head element.
     * @global array  The paths of system files and folders.
     */
    public function editor()
    {
        global $hjs, $pth;

        $filename = $pth['folder']['plugins'] . 'pdeditor/pdeditor.js';
        $hjs .= '<script type="text/javascript" src="' . $filename . '"></script>';
        $attribute = isset($_GET['pdeditor_attr'])
            ? $_GET['pdeditor_attr']
            : 'url';
        $deleteUrl = '?&pdeditor&admin=plugin_main&action=delete&pdeditor_attr=';
        $action = '?&pdeditor&admin=plugin_main&action=save&pdeditor_attr=';
        return $this->views->administration($attribute, $deleteUrl, $action);
    }
}

?>
