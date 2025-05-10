<?php

namespace Pdeditor;

use PHPUnit\Framework\TestCase;
use XH\PageDataRouter;

class DicTest extends TestCase
{
    public function setUp(): void
    {
        global $pth, $plugin_tx, $c, $pd_router;
        $pth = ["folder" => ["plugins" => ""]];
        $plugin_tx = ["pdeditor" => []];
        $c = [];
        $pd_router = $this->createStub(PageDataRouter::class);
    }

    public function testMakesMainAdminController(): void
    {
        $this->assertInstanceOf(MainAdminController::class, Dic::mainAdminController());
    }

    public function testMakesInfoController(): void
    {
        $this->assertInstanceOf(InfoController::class, Dic::infoController());
    }
}
