<?php

namespace Pdeditor;

use ApprovalTests\Approvals;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Plib\CsrfProtector;
use Plib\FakeRequest;
use Plib\View;

class MainAdminControllerTest extends TestCase
{
    /** @var Model&MockObject */
    private $model;

    /** @var CsrfProtector&Stub */
    private $csrfProtector;

    /** @var View */
    private $view;

    public function setUp(): void
    {
        $this->model = $this->createMock(Model::class);
        $this->model->expects($this->any())
            ->method('pageDataAttributes')
            ->will($this->returnValue(array('url', 'description')));
        $this->model->expects($this->any())
            ->method('toplevelPages')
            ->will($this->returnValue(array(0, 2)));
        $this->model->expects($this->any())
            ->method("heading")
            ->willReturnMap([[0, 'Welcome'], [1, 'About'], [2, 'Contact']]);
        $this->csrfProtector = $this->createStub(CsrfProtector::class);
        $this->csrfProtector->method("token")->willReturn("123456789ABCDEF");
        $this->view = new View("./views/", XH_includeVar("./languages/en.php", "plugin_tx")["pdeditor"]);
    }

    private function sut(): MainAdminController
    {
        return new MainAdminController(
            $this->model,
            $this->csrfProtector,
            $this->view
        );
    }

    public function testShowsAdministrationByDefault(): void
    {
        $request = new FakeRequest([
            "url" => "http://example.com/?pdeditor&admin=plugin_main&action=plugin_text",
        ]);
        $response = $this->sut()($request);
        $this->assertSame("Pdeditor – Pagedata", $response->title());
        Approvals::verifyHtml($response->output());
    }

    public function testDeletingIsCsrfProtected(): void
    {
        $request = new FakeRequest([
            "url" => "http://example.com/?pdeditor&admin=plugin_main&action=delete&pdeditor_attr=unused",
        ]);
        $response = $this->sut()($request);
        $this->assertStringContainsString(
            "You are not authorized to perform this action!",
            $response->output()
        );
    }

    public function testDeletingRedirectsAfterDeletingPageData(): void
    {
        $this->csrfProtector->method("check")->willReturn(true);
        $this->model->expects($this->once())->method("deletePageDataAttribute")->with("unused");
        $request = new FakeRequest([
            "url" => "http://example.com/?pdeditor&admin=plugin_main&action=delete&pdeditor_attr=unused",
            "post" => ["value" => []],
        ]);
        $response = $this->sut()($request);
        $this->assertSame(
            "http://example.com/?pdeditor&admin=plugin_main&action=plugin_text&normal",
            $response->location()
        );
    }

    public function testSavingIsCsrfProtected(): void
    {
        $request = new FakeRequest([
            "url" => "http://example.com/?pdeditor&admin=plugin_main&action=save",
            "post" => ["value" => []],
        ]);
        $response = $this->sut()($request);
        $this->assertStringContainsString(
            "You are not authorized to perform this action!",
            $response->output()
        );
    }

    public function testSavingRedirectsAfterUpdatingPageData(): void
    {
        $this->csrfProtector->method("check")->willReturn(true);
        $this->model->expects($this->once())->method("updatePageData")->with("url", []);
        $request = new FakeRequest([
            "url" => "http://example.com/?pdeditor&admin=plugin_main&action=save&pdeditor_attr=url",
            "post" => ["value" => []],
        ]);
        $response = $this->sut()($request);
        $this->assertSame(
            "http://example.com/?pdeditor&admin=plugin_main&action=plugin_text&pdeditor_attr=url&normal",
            $response->location()
        );
    }
}
