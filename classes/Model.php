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

use Pdeditor\Infra\Contents;
use XH\PageDataRouter;
use XH\Pages;

class Model
{
    /** @var Pages */
    private $pages;

    /** @var PageDataRouter */
    private $pageData;

    /** @var Contents */
    private $contents;

    public function __construct(Pages $pages, PageDataRouter $pageData, Contents $contents)
    {
        $this->pages = $pages;
        $this->pageData = $pageData;
        $this->contents = $contents;
    }

    public function heading(int $index): string
    {
        return $this->pages->heading($index);
    }

    /** @return list<int> */
    public function toplevelPages(): array
    {
        return $this->pages->toplevels();
    }

    /** @return list<int> */
    public function childPages(int $i): array
    {
        return $this->pages->children($i, false);
    }

    /** @return list<string> */
    public function pageDataAttributes(): array
    {
        $attributes = $this->pageData->storedFields();
        natcasesort($attributes);
        return $attributes;
    }

    public function pageDataAttribute(int $index, string $attribute): string
    {
        $pageData = $this->pageData->find_page($index);
        return $pageData[$attribute] ?? "";
    }

    /** @param list<string> $values */
    public function updatePageData(string $attribute, array $values): bool
    {
        $attributes = $this->pageDataAttributes();
        if (!in_array($attribute, $attributes)) {
            return false;
        }
        $pageData = $this->pageData->find_all();
        foreach ($values as $index => $value) {
            $pageData[$index][$attribute] = $value;
        }
        return $this->pageData->refresh($pageData);
    }

    public function deletePageDataAttribute(string $attribute): bool
    {
        $this->pageData->removeInterest($attribute);
        return $this->contents->save();
    }

    public function mtime(): int
    {
        return $this->contents->mtime();
    }
}
