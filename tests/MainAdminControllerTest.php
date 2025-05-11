<?php

namespace Pdeditor;

use ApprovalTests\Approvals;
use Pdeditor\Infra\Contents;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Plib\CsrfProtector;
use Plib\FakeRequest;
use Plib\View;
use XH\PageDataRouter;
use XH\Pages;

class MainAdminControllerTest extends TestCase
{
    /** @var Pages&Stub */
    private $pages;

    /** @var PageDataRouter&MockObject */
    private $pageData;

    /** @var Contents&MockObject */
    private $contents;

    /** @var CsrfProtector&Stub */
    private $csrfProtector;

    /** @var View */
    private $view;

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
        $this->pageData->expects($this->any())->method("storedFields")->willReturn(["url", "description"]);
        $this->pageData->expects($this->any())->method("find_all")->willReturn([
            ["url" => "Welcome", "description" => ""],
            ["url" => "About", "description" => ""],
            ["url" => "Contact", "description" => ""],
        ]);
        $this->pageData->expects($this->any())->method("find_page")->willReturnMap([
            [0, ["url" => "Welcome", "description" => ""]],
            [1, ["url" => "About", "description" => ""]],
            [2, ["url" => "Contact", "description" => ""]],
        ]);
        $this->contents = $this->createMock(Contents::class);
        $this->contents->expects($this->any())->method("mtime")->willReturn(strtotime("2025-05-11T11:52:27+00:00"));
        $this->csrfProtector = $this->createStub(CsrfProtector::class);
        $this->csrfProtector->method("token")->willReturn("123456789ABCDEF");
        $this->view = new View("./views/", XH_includeVar("./languages/en.php", "plugin_tx")["pdeditor"]);
    }

    private function sut(): MainAdminController
    {
        return new MainAdminController(
            new Model($this->pages, $this->pageData, $this->contents),
            $this->csrfProtector,
            $this->view
        );
    }

    public function testOverviewRedirectsOnPost(): void
    {
        $request = new FakeRequest([
            "url" => "http://example.com/?pdeditor&admin=plugin_main&action=plugin_text",
            "post" => ["pdeditor_do" => ""],
        ]);
        $response = $this->sut()($request);
        $this->assertSame("http://example.com/?pdeditor&admin=plugin_main&action=plugin_text", $response->location());
    }

    public function testShowsOverview(): void
    {
        $request = new FakeRequest([
            "url" => "http://example.com/?pdeditor&admin=plugin_main&action=plugin_text",
        ]);
        $response = $this->sut()($request);
        $this->assertSame("Page data attributes", $response->title());
        Approvals::verifyHtml($response->output());
    }

    public function testShowsEditor(): void
    {
        $request = new FakeRequest([
            "url" => "http://example.com/?pdeditor&admin=plugin_main&action=update&pdeditor_attr=url",
        ]);
        $response = $this->sut()($request);
        $this->assertSame("Edit 'url' attribute", $response->title());
        Approvals::verifyHtml($response->output());
    }

    public function testSavingIsCsrfProtected(): void
    {
        $request = new FakeRequest([
            "url" => "http://example.com/?pdeditor&admin=plugin_main&action=update",
            "post" => ["pdeditor_do" => "", "value" => []],
        ]);
        $response = $this->sut()($request);
        $this->assertStringContainsString(
            "You are not authorized to perform this action!",
            $response->output()
        );
    }

    public function testSavingReportsInvalidRequest(): void
    {
        $this->csrfProtector->method("check")->willReturn(true);
        $request = new FakeRequest([
            "url" => "http://example.com/?pdeditor&admin=plugin_main&action=update",
            "post" => ["pdeditor_do" => "", "value" => []],
        ]);
        $response = $this->sut()($request);
        $this->assertStringContainsString(
            "This request cannot be processed!",
            $response->output()
        );
    }

    public function testSavingReportsConflict(): void
    {
        $this->csrfProtector->method("check")->willReturn(true);
        $request = new FakeRequest([
            "url" => "http://example.com/?pdeditor&admin=plugin_main&action=update&pdeditor_attr=url",
            "post" => [
                "pdeditor_do" => "",
                "pdeditor_mtime" => (string) strtotime("2025-05-11T11:52:26+00:00"),
                "value" => []
            ],
        ]);
        $response = $this->sut()($request);
        $this->assertStringContainsString(
            "The content file has been modified in the meantime!",
            $response->output()
        );
    }

    public function testSavingReportsFailureToUpdateNonExistingAttribute(): void
    {
        $this->csrfProtector->method("check")->willReturn(true);
        $this->pageData->expects($this->never())->method("refresh");
        $request = new FakeRequest([
            "url" => "http://example.com/?pdeditor&admin=plugin_main&action=update&pdeditor_attr=nope",
            "post" => [
                "pdeditor_do" => "",
                "pdeditor_mtime" => (string) strtotime("2025-05-11T11:52:27+00:00"),
                "value" => []
            ],
        ]);
        $response = $this->sut()($request);
        $this->assertStringContainsString(
            "The 'nope' attribute could not be updated!",
            $response->output()
        );
    }

    public function testSavingReportsFailureToSave(): void
    {
        $this->csrfProtector->method("check")->willReturn(true);
        $this->pageData->expects($this->once())->method("refresh")->willReturn(false);
        $request = new FakeRequest([
            "url" => "http://example.com/?pdeditor&admin=plugin_main&action=update&pdeditor_attr=url",
            "post" => [
                "pdeditor_do" => "",
                "pdeditor_mtime" => (string) strtotime("2025-05-11T11:52:27+00:00"),
                "value" => []
            ],
        ]);
        $response = $this->sut()($request);
        $this->assertStringContainsString(
            "The 'url' attribute could not be updated!",
            $response->output()
        );
    }

    public function testSavingRedirectsAfterUpdatingPageData(): void
    {
        $this->csrfProtector->method("check")->willReturn(true);
        $this->pageData->expects($this->once())->method("refresh")->with([
            ["url" => "one", "description" => ""],
            ["url" => "two", "description" => ""],
            ["url" => "three", "description" => ""],
        ])->willReturn(true);
        $request = new FakeRequest([
            "url" => "http://example.com/?pdeditor&admin=plugin_main&action=update&pdeditor_attr=url",
            "post" => [
                "pdeditor_do" => "",
                "pdeditor_mtime" => (string) strtotime("2025-05-11T11:52:27+00:00"),
                "value" => ["one", "two", "three"],
            ],
        ]);
        $response = $this->sut()($request);
        $this->assertSame(
            "http://example.com/?pdeditor&admin=plugin_main&action=plugin_text&pdeditor_attr=url&normal",
            $response->location()
        );
    }

    public function testShowsDeleteConfirmation(): void
    {
        $request = new FakeRequest([
            "url" => "http://example.com/?pdeditor&admin=plugin_main&action=delete&pdeditor_attr=unused",
        ]);
        $response = $this->sut()($request);
        $this->assertSame("Delete 'unused' attribute", $response->title());
        Approvals::verifyHtml($response->output());
    }

    public function testDeletingIsCsrfProtected(): void
    {
        $request = new FakeRequest([
            "url" => "http://example.com/?pdeditor&admin=plugin_main&action=delete&pdeditor_attr=unused",
            "post" => ["pdeditor_do" => ""],
        ]);
        $response = $this->sut()($request);
        $this->assertStringContainsString(
            "You are not authorized to perform this action!",
            $response->output()
        );
    }

    public function testDeletingReportsInvalidRequest(): void
    {
        $this->csrfProtector->method("check")->willReturn(true);
        $request = new FakeRequest([
            "url" => "http://example.com/?pdeditor&admin=plugin_main&action=delete",
            "post" => ["pdeditor_do" => ""],
        ]);
        $response = $this->sut()($request);
        $this->assertStringContainsString(
            "This request cannot be processed!",
            $response->output()
        );
    }

    public function testDeletingReportsFailureToDelete(): void
    {
        $this->csrfProtector->method("check")->willReturn(true);
        $this->contents->expects($this->once())->method("save")->willReturn(false);
        $request = new FakeRequest([
            "url" => "http://example.com/?pdeditor&admin=plugin_main&action=delete&pdeditor_attr=unused",
            "post" => ["pdeditor_do" => ""],
        ]);
        $response = $this->sut()($request);
        $this->assertStringContainsString(
            "The 'unused' attribute could not be deleted!",
            $response->output()
        );
    }

    public function testDeletingRedirectsAfterDeletingPageData(): void
    {
        $this->csrfProtector->method("check")->willReturn(true);
        $this->pageData->expects($this->once())->method("removeInterest")->with("unused");
        $this->contents->expects($this->once())->method("save")->willReturn(true);
        $request = new FakeRequest([
            "url" => "http://example.com/?pdeditor&admin=plugin_main&action=delete&pdeditor_attr=unused",
            "post" => ["pdeditor_do" => ""],
        ]);
        $response = $this->sut()($request);
        $this->assertSame(
            "http://example.com/?pdeditor&admin=plugin_main&action=plugin_text&pdeditor_attr=unused&normal",
            $response->location()
        );
    }
}
