<?php

namespace Pdeditor;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Plib\CsrfProtector;
use Plib\FakeRequest;

class MainAdminControllerTest extends TestCase
{
    /** @var Model&MockObject */
    private $model;

    /** @var CsrfProtector&Stub */
    private $csrfProtector;

    /** @var Views&MockObject */
    private $views;

    public function setUp(): void
    {
        $this->model = $this->createMock(Model::class);
        $this->csrfProtector = $this->createStub(CsrfProtector::class);
        $this->views = $this->createMock(Views::class);
    }

    private function sut(): MainAdminController
    {
        return new MainAdminController(
            "./plugins/pdeditor/",
            $this->model,
            $this->csrfProtector,
            $this->views
        );
    }

    public function testShowsAdministrationByDefault(): void
    {
        $this->views->expects($this->once())->method("administration");
        $request = new FakeRequest([
            "url" => "http://example.com/?pdeditor&admin=plugin_main&action=plugin_text",
        ]);
        $response = $this->sut()->editor($request);
        $this->assertSame(
            "<script type=\"text/javascript\" src=\"./plugins/pdeditor/pdeditor.js\"></script>",
            $response->hjs()
        );
    }

    public function testSavingIsCsrfProtected(): void
    {
        $request = new FakeRequest([
            "url" => "http://example.com/?pdeditor&admin=plugin_main&action=save",
            "post" => ["value" => []],
        ]);
        $response = $this->sut()->save($request);
        $this->assertSame("not authorized", $response->output());
    }

    public function testSavingRedirectsAfterUpdatingPageData(): void
    {
        $this->csrfProtector->method("check")->willReturn(true);
        $this->model->expects($this->once())->method("updatePageData")->with("url", []);
        $request = new FakeRequest([
            "url" => "http://example.com/?pdeditor&admin=plugin_main&action=save&pdeditor_attr=url",
            "post" => ["value" => []],
        ]);
        $response = $this->sut()->save($request);
        $this->assertSame(
            "http://example.com/?pdeditor&admin=plugin_main&action=plugin_text&pdeditor_attr=url&normal",
            $response->location()
        );
    }

    public function testDeletingIsCsrfProtected(): void
    {
        $request = new FakeRequest([
            "url" => "http://example.com/?pdeditor&admin=plugin_main&action=delete&pdeditor_attr=unused",
        ]);
        $response = $this->sut()->deleteAttribute($request);
        $this->assertSame("not authorized", $response->output());
    }

    public function testDeletingRedirectsAfterDeletingPageData(): void
    {
        $this->csrfProtector->method("check")->willReturn(true);
        $this->model->expects($this->once())->method("deletePageDataAttribute")->with("unused");
        $request = new FakeRequest([
            "url" => "http://example.com/?pdeditor&admin=plugin_main&action=save&pdeditor_attr=unused",
            "post" => ["value" => []],
        ]);
        $response = $this->sut()->deleteAttribute($request);
        $this->assertSame(
            "http://example.com/?pdeditor&admin=plugin_main&action=plugin_text&normal",
            $response->location()
        );
    }
}
