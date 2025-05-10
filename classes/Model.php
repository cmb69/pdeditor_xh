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

class Model
{
    public function pluginIconPath(): string
    {
        global $pth;

        return $pth['folder']['plugins'] . 'pdeditor/pdeditor.png';
    }

    public function isPagedataUrlUpToDate(int $index): bool
    {
        global $h, $pd_router;

        $pageData = $pd_router->find_page($index);
        return $pageData['url'] == uenc($h[$index]);
    }

    public function toplevelPages(): array
    {
        global $cl, $l;

        $toplevels = array();
        for ($i = 0; $i < $cl; $i++) {
            if ($l[$i] == 1) {
                $toplevels[] = $i;
            }
        }
        return $toplevels;
    }

    public function childPages(int $i): array
    {
        global $cl, $l, $cf;

        $children = array();
        $level = $cf['menu']['levelcatch'];
        for ($j = $i + 1; $j < $cl && $l[$j] > $l[$i]; $j++) {
            if ($l[$j] <= $level) {
                $children[] = $j;
                $level = $l[$j];
            }
        }
        return $children;
    }

    public function pageDataAttributes(): array
    {
        global $pd_router;

        if (method_exists($pd_router, 'storedFields')) {
            $attributes = $pd_router->storedFields();
        } else {
            $attributes = $pd_router->model->params;
        }
        natcasesort($attributes);
        return $attributes;
    }

    public function pageDataAttribute(int $index, string $attribute): string
    {
        global $pd_router;

        $pageData = $pd_router->find_page($index);
        return $pageData[$attribute];
    }

    public function updatePageData(string $attribute, array $values): void
    {
        global $pd_router;

        $attributes = $this->pageDataAttributes();
        if (!in_array($attribute, $attributes)) {
            return;
        }
        $pageData = $pd_router->find_all();
        foreach ($values as $index => $value) {
            $pageData[$index][$attribute] = $value;
        }
        $pd_router->model->refresh($pageData);
    }

    public function deletePageDataAttribute(string $attribute): void
    {
        global $pd_router;

        if (method_exists($pd_router, 'removeInterest')) {
            $pd_router->removeInterest($attribute);
            XH_saveContents();
        } else {
            $key = array_search($attribute, $pd_router->model->params);
            if ($key !== false) {
                unset($pd_router->model->params[$key]);
            }
            for ($i = 0; $i < count($pd_router->model->data); $i++) {
                unset($pd_router->model->data[$i][$attribute]);
            }
            unset($pd_router->model->temp_data[$attribute]);
            $pd_router->model->save();
        }
    }
}
