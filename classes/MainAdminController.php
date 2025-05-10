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
use Plib\Response;
use Plib\View;

class MainAdminController
{
    /** @var string */
    private $pluginFolder;

    /** @var Model */
    private $model;

    /** @var CsrfProtector */
    private $csrfProtector;

    /** @var View */
    private $view;

    public function __construct(
        string $pluginFolder,
        Model $model,
        CsrfProtector $csrfProtector,
        View $view
    ) {
        $this->pluginFolder = $pluginFolder;
        $this->model = $model;
        $this->csrfProtector = $csrfProtector;
        $this->view = $view;
    }

    public function __invoke(Request $request): Response
    {
        switch ($request->get("action")) {
            default:
                return $this->editor($request);
            case 'delete':
                return $this->deleteAttribute($request);
            case 'save':
                return $this->save($request);
        }
    }

    private function editor(Request $request): Response
    {
        $filename = $this->pluginFolder . "pdeditor.js";
        $hjs = '<script type="text/javascript" src="' . $filename . '"></script>';
        $attribute = $request->get("pdeditor_attr") ?? "url";
        $deleteUrl = $request->url()->page("pdeditor")->with("admin", "plugin_main")
            ->with("action", "delete")->with("pdeditor_attr", $attribute)->with("edit")->relative();
        $action = $request->url()->page("pdeditor")->with("admin", "plugin_main")
            ->with("action", "save")->with("pdeditor_attr", $attribute)->with("edit")->relative();
        return Response::create($this->administration($attribute, $deleteUrl, $action))
            ->withHjs($hjs);
    }

    private function administration(string $attribute, string $deleteUrl, string $action): string
    {
        $toplevelPages = $this->model->toplevelPages();
        $pageList = $this->pageList($toplevelPages, $attribute);
        return $this->view->render("admin", [
            "attribute" => $attribute,
            "attributes" => $this->attributeList(),
            "deleteUrl" => $deleteUrl,
            "deleteWarning" => addcslashes($this->view->plain("warning_delete"), "\n\r\'\"\\"),
            "action" => $action,
            "saveWarning" => addcslashes($this->view->plain("warning_save"), "\n\r\'\"\\"),
            "csrf_token" => $this->csrfProtector->token(),
            "pageList" => $pageList,
        ]);
    }

    private function attributeList(): string
    {
        $url = '?pdeditor&normal&admin=plugin_main&action=plugin_text'
            . '&pdeditor_attr=';
        $attributes = $this->model->pageDataAttributes();
        $items = '';
        foreach ($attributes as $attribute) {
            $items .= $this->attributeListItem($url, $attribute);
        }
        return $items;
    }

    private function attributeListItem(string $url, string $attribute): string
    {
        $url = $this->view->esc($url . $attribute);
        return <<<EOT
<li><a href="$url">$attribute</a></li>
EOT;
    }

    private function pageList(array $pages, string $attribute): string
    {
        if (empty($pages)) {
            return '';
        }
        $items = '';
        foreach ($pages as $i) {
            $items .= $this->pageListItem($attribute, $i);
        }
        return <<<EOT
<ul>
    $items
</ul>
EOT;
    }

    private function pageListItem(string $attribute, int $i): string
    {
        $heading = $this->model->heading($i);
        $pageDataAttribute = $this->model->pageDataAttribute($i, $attribute);
        if ($attribute == 'url' && !$this->model->isPagedataUrlUpToDate($i)) {
            $warning = $this->warningIcon();
        } else {
            $warning = '';
        }
        $value = $this->view->esc($pageDataAttribute);
        $subpages = $this->pageList($this->model->childPages($i), $attribute);
        return <<<EOT
<li>
$warning$heading<input type="text" name="value[]" value="$value" />$subpages
</li>
EOT;
    }

    private function warningIcon(): string
    {
        return "\xE2\x9A\xA0 ";
    }

    private function deleteAttribute(Request $request): Response
    {
        if (!$this->csrfProtector->check($request->post("pdeditor_token"))) {
            return Response::create("not authorized"); // TODO i18n
        }
        $attribute = $request->get("pdeditor_attr");
        $this->model->deletePageDataAttribute($attribute);
        $url = $request->url()->page("pdeditor")->with("admin", "plugin_main")
            ->with("action", "plugin_text")->with("normal");
            return Response::redirect($url->absolute());
    }

    private function save(Request $request): Response
    {
        if ($request->postArray("value") !== null) {
            if (!$this->csrfProtector->check($request->post("pdeditor_token"))) {
                return Response::create("not authorized"); // TODO i18n
            }
            $attribute = $request->get("pdeditor_attr");
            $values = $request->postArray("value");
            $this->model->updatePageData($attribute, $values);
        } else {
            $attribute = "";
        }
        $url = $request->url()->page("pdeditor")->with("admin", "plugin_main")
            ->with("action", "plugin_text")->with("pdeditor_attr", $attribute)->with("normal");
        return Response::redirect($url->absolute());
    }
}
