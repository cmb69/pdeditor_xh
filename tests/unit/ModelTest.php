<?php

/**
 * A test case for the model class.
 *
 * PHP version 5
 *
 * @category  CMSimple_XH
 * @package   Pdeditor
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2013-2015 Christoph M. Becker <http://3-magi.net/>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link      http://3-magi.net/?CMSimple_XH/Pdeditor_XH
 */

use org\bovigo\vfs\vfsStreamWrapper;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use XH\PageDataRouter;

/**
 * A test case for the model class.
 *
 * @category CMSimple_XH
 * @package  Pdeditor
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Pdeditor_XH
 */
class ModelTest extends TestCase
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
    protected $subject;

    /**
     * Sets up the test fixture.
     *
     * @return void
     *
     * @global array The paths of system files and folder.
     */
    public function setUp(): void
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

        $this->subject = new Pdeditor_Model();
        uopz_set_return("uenc", fn ($url) => urlencode($url), true);
    }

    public function tearDown(): void
    {
        uopz_unset_return("uenc");
    }

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
     * @global The page headings.
     * @global The levels of the pages.
     * @global The number of pages.
     * @global The page data router.
     */
    protected function setUpContents()
    {
        global $h, $l, $cl, $pd_router;

        $h = array('Welcome', 'About', 'Contact');
        $l = array('1', '2', '1');
        $cl = count($l);

        $pd_router = $this->getMockBuilder(PageDataRouter::class)
            ->disableOriginalConstructor()
            ->getMock();
        $map = array(
            array(0, array('url' => 'Welcome'))
        );
        $pd_router->expects($this->any())
            ->method('find_page')
            ->will($this->returnValueMap($map));
    }

    /**
     * Tests ::pluginIconPath().
     *
     * @return void
     */
    public function testPluginIconPath()
    {
        $expected = $this->pluginsFolder . 'pdeditor/pdeditor.png';
        $actual = $this->subject->pluginIconPath();
        $this->assertEquals($expected, $actual);
    }

    /**
     * Tests ::isPagedataUrlUpToDate().
     *
     * @return void
     */
    public function testIsPagedataUrlUpToDate()
    {
        $actual = $this->subject->isPagedataUrlUpToDate(0);
        $this->assertTrue($actual);
    }

    /**
     * Tests ::topLevelPages().
     *
     * @return void
     */
    public function testTopLevelPages()
    {
        $expected = array(0, 2);
        $actual = $this->subject->toplevelPages();
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
        $actual = $this->subject->childPages(0);
        $this->assertEquals($expected, $actual);
    }

    /**
     * Tests ::pageDataAttributes().
     *
     * @return void
     *
     * @global object The page data router.
     */
    public function testPageDataAttributes()
    {
        global $pd_router;

        if (method_exists($pd_router, 'storedFields')) {
            $pd_router->expects($this->any())
                ->method('storedFields')
                ->will($this->returnValue(array('bar', 'foo')));
            $expected = array('bar', 'foo');
            $actual = $this->subject->pageDataAttributes();
            $this->assertEquals($expected, $actual);
        } else {
            $this->markTestSkipped();
        }
    }

    /**
     * Tests ::pageDataAttribute().
     *
     * @return void
     */
    public function testPageDataAttribute()
    {
        $expected = 'Welcome';
        $actual = $this->subject->pageDataAttribute(0, 'url');
        $this->assertEquals($expected, $actual);
    }

    /**
     * Tests ::deletePageDataAttribute().
     *
     * @return void
     *
     * @global object The page data router.
     */
    public function testDeletePageDataAttribute()
    {
        global $pd_router;

        if (method_exists($pd_router, 'removeInterest')) {
            $attribute = 'foo';
            $pd_router->expects($this->once())
                ->method('removeInterest')
                ->with($this->equalTo($attribute));
            $this->subject->deletePageDataAttribute($attribute);
        } else {
            $this->markTestSkipped();
        }
    }
}

?>
