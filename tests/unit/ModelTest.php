<?php

namespace Pdeditor;

use org\bovigo\vfs\vfsStreamWrapper;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use XH\PageDataRouter;

class ModelTest extends TestCase
{
    /** @var string  */
    private $pluginsFolder;

    /** @var Model */
    private $subject;

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

        $this->subject = new Model();
        uopz_set_return("uenc", fn ($url) => urlencode($url), true);
    }

    public function tearDown(): void
    {
        uopz_unset_return("uenc");
    }

    private function setUpConfig(): void
    {
        global $cf;

        $cf = array(
            'menu' => array('levelcatch' => '10')
        );
    }

    private function setUpContents(): void
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

    public function testPluginIconPath(): void
    {
        $expected = $this->pluginsFolder . 'pdeditor/pdeditor.png';
        $actual = $this->subject->pluginIconPath();
        $this->assertEquals($expected, $actual);
    }

    public function testIsPagedataUrlUpToDate(): void
    {
        $actual = $this->subject->isPagedataUrlUpToDate(0);
        $this->assertTrue($actual);
    }

    public function testTopLevelPages(): void
    {
        $expected = array(0, 2);
        $actual = $this->subject->toplevelPages();
        $this->assertEquals($expected, $actual);
    }

    public function testChildPages(): void
    {
        $expected = array(1);
        $actual = $this->subject->childPages(0);
        $this->assertEquals($expected, $actual);
    }

    public function testPageDataAttributes(): void
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

    public function testPageDataAttribute(): void
    {
        $expected = 'Welcome';
        $actual = $this->subject->pageDataAttribute(0, 'url');
        $this->assertEquals($expected, $actual);
    }

    public function testDeletePageDataAttribute(): void
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
