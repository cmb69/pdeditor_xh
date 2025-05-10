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
        return Response::create($this->administration($request, $attribute, $deleteUrl, $action))
            ->withHjs($hjs);
    }

    private function administration(Request $request, string $attribute, string $deleteUrl, string $action): string
    {
        return $this->view->render("admin", [
            "attribute" => $attribute,
            "attributes" => $this->attributeList($request),
            "deleteUrl" => $deleteUrl,
            "deleteWarning" => addcslashes($this->view->plain("warning_delete"), "\n\r\'\"\\"),
            "action" => $action,
            "saveWarning" => addcslashes($this->view->plain("warning_save"), "\n\r\'\"\\"),
            "csrf_token" => $this->csrfProtector->token(),
            "pageList" => $this->pageList($this->model->toplevelPages(), $attribute),
        ]);
    }

    /** @return list<object{name:string,url:string}> */
    private function attributeList(Request $request): array
    {
        $items = [];
        foreach ($this->model->pageDataAttributes() as $attribute) {
            $items[] = (object) [
                "name" => $attribute,
                "url" => $request->url()->page("pdeditor")->with("normal")->with("admin", "plugin_main")
                    ->with("action", "plugin_text")->with("pdeditor_attr", $attribute)->relative(),
            ];
        }
        return $items;
    }

    /** @param list<int> $pages */
    private function pageList(array $pages, string $attribute): string
    {
        if (empty($pages)) {
            return "";
        }
        $items = "";
        foreach ($pages as $i) {
            $items .= $this->pageListItem($attribute, $i);
        }
        return "<ul>\n  $items\n</ul>\n";
    }

    private function pageListItem(string $attribute, int $i): string
    {
        $heading = $this->model->heading($i);
        $pageDataAttribute = $this->model->pageDataAttribute($i, $attribute);
        $value = $this->view->esc($pageDataAttribute);
        $subpages = $this->pageList($this->model->childPages($i), $attribute);
        $subpages = $subpages ? "$subpages\n" : "";
        return "<li>\n  $heading<input type=\"text\" name=\"value[]\" value=\"$value\">\n"
            . "$subpages</li>\n";
    }

    private function deleteAttribute(Request $request): Response
    {
        if (!$this->csrfProtector->check($request->post("pdeditor_token"))) {
            return Response::create("not authorized"); // TODO i18n
        }
        $attribute = $request->get("pdeditor_attr") ?? "";
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
            $attribute = $request->get("pdeditor_attr") ?? "";
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
