<?php

/**
 * The controller class.
 *
 * PHP version 5
 *
 * @category  CMSimple_XH
 * @package   Pdeditor
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2012-2015 Christoph M. Becker <http://3-magi.net/>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link      http://3-magi.net/?CMSimple_XH/Pdeditor_XH
 */

namespace Pdeditor;

use XH\Pages;

class Controller
{
    /** @var Model */
    private $model;

    /** @var Views */
    private $views;

    public function __construct()
    {
        global $pd_router;
        $this->model = new Model(new Pages(), $pd_router);
        $this->views = new Views($this->model);
        $this->dispatch();
    }

    private function baseUrl(): string
    {
        global $sn;

        return 'http'
            . (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 's' : '')
            . '://' . $_SERVER['HTTP_HOST']
            . preg_replace('/index\.php$/', '', $sn);
    }

    private function dispatch(): void
    {
        global $adm;

        if ($adm) {
            XH_registerStandardPluginMenuItems(true);
            if ($this->isAdministrationRequested()) {
                $this->administration();
            }
        }
    }

    private function isAdministrationRequested(): bool
    {
        global $pdeditor;

        return XH_wantsPluginAdministration('pdeditor');
    }

    private function administration(): void
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
                $o .= plugin_admin_common();
        }
    }

    private function systemChecks(): array
    {
        global $pth, $tx, $plugin_tx;

        $phpVersion = '7.1.0';
        $ptx = $plugin_tx['pdeditor'];
        $checks = array();
        $checks[sprintf($ptx['syscheck_phpversion'], $phpVersion)]
            = version_compare(PHP_VERSION, $phpVersion) >= 0 ? 'ok' : 'fail';
        foreach (array('pcre', 'spl') as $extension) {
            $checks[sprintf($ptx['syscheck_extension'], $extension)]
                = extension_loaded($extension) ? 'ok' : 'fail';
        }
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

    public function info(): string
    {
        global $plugin_tx;

        $ptx = $plugin_tx['pdeditor'];
        $o = '<h1>Pdeditor &ndash; ' . $ptx['info_heading'] . '</h1>'
            . $this->views->about()
            . $this->views->systemCheck($this->systemChecks());
        return $o;
    }

    public function save(): string
    {
        global $_XH_csrfProtection;

        if (isset($_POST['value'])) {
            if (isset($_XH_csrfProtection)) {
                $_XH_csrfProtection->check();
            }
            $attribute = $_GET['pdeditor_attr'];
            $values = $_POST['value'];
            $this->model->updatePageData($attribute, $values);
        } else {
            $attribute = "";
        }
        $url = $this->baseUrl()
            . '?&pdeditor&admin=plugin_main&action=plugin_text&pdeditor_attr='
            . $attribute . '&normal';
        header('Location: ' . $url);
        exit;
    }

    public function deleteAttribute(): string
    {
        global $_XH_csrfProtection;

        if (isset($_XH_csrfProtection)) {
            $_XH_csrfProtection->check();
        }
        $attribute = $_GET['pdeditor_attr'];
        $this->model->deletePageDataAttribute($attribute);
        $url = $this->baseUrl()
            . '?&pdeditor&admin=plugin_main&action=plugin_text&normal';
        header('Location: ' . $url);
        exit;
    }

    public function editor(): string
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
