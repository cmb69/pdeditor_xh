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
use Plib\Request;

class MainAdminController
{
    /** @var Model */
    private $model;

    /** @var CsrfProtector */
    private $csrfProtector;

    /** @var Views */
    private $views;

    public function __construct(Model $model, CsrfProtector $csrfProtector, Views $views)
    {
        $this->model = $model;
        $this->csrfProtector = $csrfProtector;
        $this->views = $views;
    }

    public function save(Request $request): string
    {
        if (isset($_POST['value'])) {
            if (!$this->csrfProtector->check($request->post("pdeditor_token"))) {
                return "not authorized"; // TODO i18n
            }
            $attribute = $request->get("pdeditor_attr");
            $values = $request->postArray("value");
            $this->model->updatePageData($attribute, $values);
        } else {
            $attribute = "";
        }
        $url = $request->url()->page("pdeditor")->with("admin", "plugin_main")
            ->with("action", "plugin_text")->with("pdeditor_attr", $attribute)->with("normal")
            ->absolute();
        header('Location: ' . $url);
        exit;
    }

    public function deleteAttribute(Request $request): string
    {
        if (!$this->csrfProtector->check($request->post("pdeditor_token"))) {
            return "not authorized"; // TODO i18n
        }
        $attribute = $request->get("pdeditor_attr");
        $this->model->deletePageDataAttribute($attribute);
        $url = $request->url()->page("pdeditor")->with("admin", "plugin_main")
            ->with("action", "plugin_text")->with("normal")->absolute();
        header('Location: ' . $url);
        exit;
    }

    public function editor(Request $request): string
    {
        global $hjs, $pth;

        $filename = $pth['folder']['plugins'] . 'pdeditor/pdeditor.js';
        $hjs .= '<script type="text/javascript" src="' . $filename . '"></script>';
        $attribute = $request->get("pdeditor_attr") ?? "url";
        $deleteUrl = '?&pdeditor&admin=plugin_main&action=delete&pdeditor_attr=';
        $action = '?&pdeditor&admin=plugin_main&action=save&pdeditor_attr=';
        return $this->views->administration($attribute, $deleteUrl, $action);
    }
}
