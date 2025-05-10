<?php

namespace Pdeditor;

use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use XH\PageDataRouter;
use XH\Pages;

class ModelTest extends TestCase
{
    /** @var Pages&Stub */
    private $pages;

    /** @var Model */
    private $subject;

    public function setUp(): void
    {
        global $pd_router;

        $this->setUpConfig();
        $this->setUpContents();

        $this->subject = new Model($this->pages, $pd_router);
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
        global $pd_router;

        $this->pages = $this->createStub(Pages::class);
        $this->pages->method("heading")->willReturnMap([
            [0, "Welcome"],
            [1, "About"],
            [2, "Contact"],
        ]);
        $this->pages->method("level")->willReturnMap([
            [0, 1],
            [1, 2],
            [2, 1],
        ]);
        $this->pages->method("toplevels")->willReturn([0, 2]);
        $this->pages->method("children")->willReturnMap([
            [0, false, [1]],
            [1, false, []],
            [2, false, []],
        ]);
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

        $pd_router->expects($this->any())
            ->method('storedFields')
            ->will($this->returnValue(array('bar', 'foo')));
        $expected = array('bar', 'foo');
        $actual = $this->subject->pageDataAttributes();
        $this->assertEquals($expected, $actual);
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

        $attribute = 'foo';
        $pd_router->expects($this->once())
            ->method('removeInterest')
            ->with($this->equalTo($attribute));
        $this->subject->deletePageDataAttribute($attribute);
    }
}
