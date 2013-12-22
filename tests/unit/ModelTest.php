<?php

/**
 * A test case for the model class.
 *
 * PHP version 5
 *
 * @category  CMSimple_XH
 * @package   Pdeditor
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2013 Christoph M. Becker <http://3-magi.net/>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @version   SVN: $Id$
 * @link      http://3-magi.net/?CMSimple_XH/Pdeditor_XH
 */

/**
 * The stream wrapper for mocking the real file system.
 */
require_once 'vfsStream/vfsStream.php';

/**
 * The class under test.
 */
require_once './classes/Model.php';

/**
 * A test case for the model class.
 *
 * @category CMSimple_XH
 * @package  Pdeditor
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Pdeditor_XH
 */
class ModelTest extends PHPUnit_Framework_TestCase
{
    /**
     * The path of the plugins folder.
     *
     * @var string
     */
    protected $pluginsFolder;

    /**
     * The model under test.
     *
     * @var Pdeditor_Model
     */
    protected $model;

    /**
     * Sets up the configuration of the test fixture.
     *
     * @return void
     *
     * @global array The configuration of the core.
     */
    protected function setUpConfig()
    {
        global $cf;

        $cf = array(
            'menu' => array('levelcatch' => '10')
        );
    }

    /**
     * Sets up the contents of the text fixture.
     *
     * @return void
     *
     * @global The levels of the pages.
     * @global The number of pages.
     */
    protected function setUpContents()
    {
        global $l, $cl;

        $l = array('1', '2', '1');
        $cl = count($l);
    }

    /**
     * Sets up the test fixture.
     *
     * @return void
     *
     * @global array The paths of system files and folder.
     */
    public function setUp()
    {
        global $pth;

        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot(new vfsStreamDirectory('test'));

        $this->pluginsFolder = vfsStream::url('test/plugins/');
        mkdir($this->pluginsFolder, 0777, true);
        $pth = array(
            'folder' => array('plugins' => $this->pluginsFolder)
        );

        $this->setUpConfig();
        $this->setUpContents();

        $this->model = new Pdeditor_Model();
    }

    /**
     * Tests ::pluginIconPath().
     *
     * @return void
     */
    public function testPluginIconPath()
    {
        $expected = $this->pluginsFolder . 'pdeditor/pdeditor.png';
        $actual = $this->model->pluginIconPath();
        $this->assertEquals($expected, $actual);
    }

    /**
     * Tests ::topLevelPages().
     *
     * @return void
     */
    public function testTopLevelPages()
    {
        $expected = array(0, 2);
        $actual = $this->model->toplevelPages();
        $this->assertEquals($expected, $actual);
    }

    /**
     * Tests ::childPages().
     *
     * @return void
     */
    public function testChildPages()
    {
        $expected = array(1);
        $actual = $this->model->childPages(0);
        $this->assertEquals($expected, $actual);
    }
}

?>
