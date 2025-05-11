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
    /** @var Model */
    private $model;

    /** @var CsrfProtector */
    private $csrfProtector;

    /** @var View */
    private $view;

    public function __construct(
        Model $model,
        CsrfProtector $csrfProtector,
        View $view
    ) {
        $this->model = $model;
        $this->csrfProtector = $csrfProtector;
        $this->view = $view;
    }

    public function __invoke(Request $request): Response
    {
        switch ($request->get("action")) {
            default:
                return $this->editor($request);
            case "delete":
                return $request->post("pdeditor_do") === null
                    ? $this->deleteAttribute($request)
                    : $this->doDeleteAttribute($request);
            case 'save':
                return $this->save($request);
        }
    }

    private function editor(Request $request): Response
    {
        if ($request->post("pdeditor_do") !== null) {
            return Response::redirect($request->url()->absolute());
        }
        $attribute = $request->get("pdeditor_attr") ?? "url";
        $action = $request->url()->page("pdeditor")->with("admin", "plugin_main")
            ->with("action", "save")->with("pdeditor_attr", $attribute)->with("edit")->relative();
        return Response::create($this->administration($attribute, $action))
            ->withTitle("Pdeditor – {$this->view->text("menu_main")}");
    }

    private function administration(string $attribute, string $action): string
    {
        return $this->view->render("admin", [
            "attribute" => $attribute,
            "attributes" => $this->attributeList($attribute),
            "action" => $action,
            "saveWarning" => addcslashes($this->view->plain("warning_save"), "\n\r\'\"\\"),
            "csrf_token" => $this->csrfProtector->token(),
            "pageList" => $this->pageList($this->model->toplevelPages(), $attribute),
        ]);
    }

    /** @return list<object{name:string,selected:string}> */
    private function attributeList(string $currentAttribute): array
    {
        $items = [];
        foreach ($this->model->pageDataAttributes() as $attribute) {
            $items[] = (object) [
                "name" => $attribute,
                "selected" => $attribute === $currentAttribute ? "selected" : "",
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
        return Response::create($this->view->render("delete_confirmation", [
            "url" => $request->url()->with("edit")->relative(),
            "attribute" => $request->get("pdeditor_attr") ?? "",
            "csrf_token" => $this->csrfProtector->token(),
            "cancel" => $request->url()->with("action", "plugin_text")->relative(),
        ]));
    }

    private function doDeleteAttribute(Request $request): Response
    {
        if (!$this->csrfProtector->check($request->post("pdeditor_token"))) {
            return Response::create($this->view->message("error", "error_unauthorized"));
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
                return Response::create($this->view->message("error", "error_unauthorized"));
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
