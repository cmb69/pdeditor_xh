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
                return $this->overview($request);
            case "delete":
                return $this->deleteAttribute($request);
            case "update":
                return $this->update($request);
        }
    }

    private function overview(Request $request): Response
    {
        if ($request->post("pdeditor_do") !== null) {
            return Response::redirect($request->url()->absolute());
        }
        $attribute = $request->get("pdeditor_attr") ?? "url";
        return Response::create($this->view->render("overview", [
            "attribute" => $attribute,
            "attributes" => $this->attributeList($attribute),
        ]))->withTitle($this->view->text("title_attributes"));
    }

    /** @return list<object{name:string,checked:string}> */
    private function attributeList(string $currentAttribute): array
    {
        $items = [];
        foreach ($this->model->pageDataAttributes() as $attribute) {
            $items[] = (object) [
                "name" => $attribute,
                "checked" => $attribute === $currentAttribute ? "checked" : "",
            ];
        }
        return $items;
    }

    private function update(Request $request): Response
    {
        if ($request->post("pdeditor_do") !== null) {
            return $this->doUpdate($request);
        }
        $attribute = $request->get("pdeditor_attr") ?? "url";
        return Response::create($this->view->render("update", [
            "action" => $request->url()->with("edit")->relative(),
            "attribute" => $attribute,
            "csrf_token" => $this->csrfProtector->token(),
            "pageList" => $this->pageList($this->model->toplevelPages(), $attribute),
            "cancel" => $request->url()->with("action", "plugin_text")->relative(),
            "mtime" => $this->model->mtime(),
        ]))->withTitle($this->view->text("title_edit", $attribute));
    }

    /** @param list<int> $pages */
    private function pageList(array $pages, string $attribute, string $indent = "    "): string
    {
        if (empty($pages)) {
            return "";
        }
        $items = "";
        foreach ($pages as $i) {
            $items .= $this->pageListItem($attribute, $i, $indent . "  ");
        }
        return ($indent === "    " ? "" : $indent) . "<ul>\n$items$indent</ul>" . ($indent === "    " ? "\n" : "");
    }

    private function pageListItem(string $attribute, int $i, string $indent): string
    {
        $heading = $this->model->heading($i);
        $pageDataAttribute = $this->model->pageDataAttribute($i, $attribute);
        $value = $this->view->esc($pageDataAttribute);
        $subpages = $this->pageList($this->model->childPages($i), $attribute, $indent . "  ");
        $subpages = $subpages ? "$subpages\n" : "";
        return "$indent<li>\n$indent  $heading<input type=\"text\" name=\"value[]\" value=\"$value\">\n"
            . "$subpages$indent</li>\n";
    }

    private function doUpdate(Request $request): Response
    {
        if (!$this->csrfProtector->check($request->post("pdeditor_token"))) {
            return Response::create($this->view->message("fail", "error_unauthorized"));
        }
        if ($request->get("pdeditor_attr") === null || $request->postArray("value") === null) {
            return Response::create($this->view->message("fail", "error_bad_request"));
        }
        if ($request->post("pdeditor_mtime") < $this->model->mtime()) {
            return Response::create($this->view->message("fail", "error_conflict"));
        }
        $attribute = $request->get("pdeditor_attr");
        $values = $request->postArray("value");
        if (!$this->model->updatePageData($attribute, $values)) {
            return Response::create($this->view->message("fail", "error_update", $attribute));
        }
        $url = $request->url()->with("action", "plugin_text")->with("pdeditor_attr", $attribute)
            ->without("edit")->with("normal");
        return Response::redirect($url->absolute());
    }

    private function deleteAttribute(Request $request): Response
    {
        if ($request->post("pdeditor_do") !== null) {
            return $this->doDeleteAttribute($request);
        }
        $attribute = $request->get("pdeditor_attr") ?? "";
        return Response::create($this->view->render("delete_confirmation", [
            "url" => $request->url()->with("edit")->relative(),
            "attribute" => $attribute,
            "csrf_token" => $this->csrfProtector->token(),
            "cancel" => $request->url()->with("action", "plugin_text")->relative(),
        ]))->withTitle($this->view->text("title_delete", $attribute));
    }

    private function doDeleteAttribute(Request $request): Response
    {
        if (!$this->csrfProtector->check($request->post("pdeditor_token"))) {
            return Response::create($this->view->message("fail", "error_unauthorized"));
        }
        if ($request->get("pdeditor_attr") === null) {
            return Response::create($this->view->message("fail", "error_bad_request"));
        }
        $attribute = $request->get("pdeditor_attr");
        if (!$this->model->deletePageDataAttribute($attribute)) {
            return Response::create($this->view->message("fail", "error_delete", $attribute));
        }
        $url = $request->url()->with("action", "plugin_text")->without("edit")->with("normal");
        return Response::redirect($url->absolute());
    }
}
