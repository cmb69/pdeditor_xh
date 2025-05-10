<?php

/**
 * The model class.
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

    public function pluginIconPath(): string
    {
        global $pth;

        return $pth['folder']['plugins'] . 'pdeditor/pdeditor.png';
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
