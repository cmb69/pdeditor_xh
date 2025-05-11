<?php

namespace Pdeditor;

use Pdeditor\Infra\Contents;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use XH\PageDataRouter;
use XH\Pages;

class ModelTest extends TestCase
{
    /** @var Pages&Stub */
    private $pages;

    /** @var PageDataRouter&MockObject */
    private $pageData;

    /** @var Contents&MockObject */
    private $contents;

    public function setUp(): void
    {
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
        $this->pageData = $this->getMockBuilder(PageDataRouter::class)->disableOriginalConstructor()->getMock();
        $this->pageData->expects($this->any())->method("find_page")->willReturnMap([
            [0, ["url" => "Welcome"]],
        ]);
        $this->contents = $this->createMock(Contents::class);
    }

    private function sut(): Model
    {
        return new Model($this->pages, $this->pageData, $this->contents);
    }

    public function testTopLevelPages(): void
    {
        $this->assertEquals([0, 2], $this->sut()->toplevelPages());
    }

    public function testChildPages(): void
    {
        $this->assertEquals([1], $this->sut()->childPages(0));
    }

    public function testPageDataAttributes(): void
    {
        $this->pageData->expects($this->any())->method("storedFields")->willReturn(["bar", "foo"]);
        $actual = $this->sut()->pageDataAttributes();
        $this->assertEquals(["bar", "foo"], $actual);
    }

    public function testPageDataAttribute(): void
    {
        $actual = $this->sut()->pageDataAttribute(0, "url");
        $this->assertEquals("Welcome", $actual);
    }

    public function testDeletePageDataAttribute(): void
    {
        $attribute = "foo";
        $this->pageData->expects($this->once())->method("removeInterest")->with($this->equalTo($attribute));
        $this->sut()->deletePageDataAttribute($attribute);
    }
}
