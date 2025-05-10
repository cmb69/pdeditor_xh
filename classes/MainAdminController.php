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

class MainAdminController
{
    /** @var Model */
    private $model;

    /** @var Views */
    private $views;

    public function __construct(Model $model, Views $views)
    {
        $this->model = $model;
        $this->views = $views;
    }

    private function baseUrl(): string
    {
        global $sn;

        return 'http'
            . (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 's' : '')
            . '://' . $_SERVER['HTTP_HOST']
            . preg_replace('/index\.php$/', '', $sn);
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
