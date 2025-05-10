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

use XH\PageDataRouter;
use XH\Pages;

class Model
{
    /** @var Pages */
    private $pages;

    /** @var PageDataRouter */
    private $pageData;

    public function __construct(Pages $pages, PageDataRouter $pageData)
    {
        $this->pages = $pages;
        $this->pageData = $pageData;
    }

    public function isPagedataUrlUpToDate(int $index): bool
    {
        $pageData = $this->pageData->find_page($index);
        return $pageData['url'] == uenc($this->pages->heading($index));
    }

    public function toplevelPages(): array
    {
        return $this->pages->toplevels();
    }

    public function childPages(int $i): array
    {
        return $this->pages->children($i, false);
    }

    public function pageDataAttributes(): array
    {
        $attributes = $this->pageData->storedFields();
        natcasesort($attributes);
        return $attributes;
    }

    public function pageDataAttribute(int $index, string $attribute): string
    {
        $pageData = $this->pageData->find_page($index);
        return $pageData[$attribute];
    }

    public function updatePageData(string $attribute, array $values): void
    {
        $attributes = $this->pageDataAttributes();
        if (!in_array($attribute, $attributes)) {
            return;
        }
        $pageData = $this->pageData->find_all();
        foreach ($values as $index => $value) {
            $pageData[$index][$attribute] = $value;
        }
        $this->pageData->refresh($pageData);
    }

    public function deletePageDataAttribute(string $attribute): void
    {
        $this->pageData->removeInterest($attribute);
        XH_saveContents();
    }
}
