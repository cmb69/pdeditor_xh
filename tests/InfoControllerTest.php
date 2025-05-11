<?php

namespace Pdeditor;

use ApprovalTests\Approvals;
use PHPUnit\Framework\TestCase;
use Plib\FakeSystemChecker;
use Plib\SystemChecker;
use Plib\View;

class InfoControllerTest extends TestCase
{
    /** @var SystemChecker */
    private $systemChecker;

    /** @var View */
    private $view;

    public function setUp(): void
    {
        $this->systemChecker = new FakeSystemChecker();
        $this->view = new View("./views/", XH_includeVar("./languages/en.php", "plugin_tx")["pdeditor"]);
    }

    private function sut(): InfoController
    {
        return new InfoController("./plugins/pdeditor/", $this->systemChecker, $this->view);
    }

    public function testRendersPluginInfo(): void
    {
        $response = $this->sut()();
        $this->assertSame("Pdeditor 2.1-dev", $response->title());
        Approvals::verifyHtml($response->output());
    }
}
