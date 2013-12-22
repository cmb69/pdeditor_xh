<?php

/**
 * The model class.
 *
 * PHP version 5
 *
 * @category  CMSimple_XH
 * @package   Pdeditor
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2011-2013 Christoph M. Becker <http://3-magi.net/>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @version   SVN: $Id$
 * @link      http://3-magi.net/?CMSimple_XH/Pdeditor_XH
 */

/**
 * The model class.
 *
 * @category CMSimple_XH
 * @package  Pdeditor
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Pdeditor_XH
 */
class Pdeditor_Model
{
    /**
     * Returns the path of the plugin icon.
     *
     * @return string
     *
     * @global array The paths of system files and folders.
     */
    public function pluginIconPath()
    {
        global $pth;

        return $pth['folder']['plugins'] . 'pdeditor/pdeditor.png';
    }

    /**
     * Returns whether the URL stored in the page data is up-to-date.
     *
     * @param int $index The page index.
     *
     * @return bool
     *
     * @global array  The headings of the pages.
     * @global object The page data router.
     */
    public function isPagedataUrlUpToDate($index)
    {
        global $h, $pd_router;

        $pageData = $pd_router->find_page($index);
        return $pageData['url'] == uenc($h[$index]);
    }

    /**
     * Returns an array of indexes of the toplevel pages.
     *
     * @return array
     *
     * @global int   The number of pages.
     * @global array The page levels.
     */
    public function toplevelPages()
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

    /**
     * Returns an array of indexes of child pages of a page.
     *
     * @param int $i A page index.
     *
     * @return array
     *
     * @global int   The number of pages.
     * @global array The page levels.
     * @global arras The configuration of the core.
     */
    public function childPages($i)
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

    /**
     * @param string $attribute An attribute name.
     * @param array  $values    An array of new values.
     *
     * @return void
     *
     * @todo Check that attribute is registered!
     */
    public function updatePageData($attribute, $values)
    {
        global $pd_router;

        $pageData = $pd_router->find_all();
        foreach ($values as $index => $value) {
            $pageData[$index][$attribute] = $value;
        }
        $pd_router->model->refresh($pageData);
    }
}

?>
