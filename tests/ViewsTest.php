<?php

namespace Pdeditor;

use ApprovalTests\Approvals;
use PHPUnit\Framework\TestCase;

define('PDEDITOR_VERSION', '1.0');

class ViewsTest extends TestCase
{
    /** @var Views */
    private $views;

    public function setUp(): void
    {
        global $h, $tx, $plugin_tx;
        $h = array('Welcome', 'About', 'Contact');
        $tx = ["action" => ["save" => ""]];
        $plugin_tx = ["pdeditor" => XH_includeVar("./languages/en.php", "plugin_tx")["pdeditor"]];
        $model = $this->getMockBuilder(Model::class)
            ->disableOriginalConstructor()
            ->getMock();
        $model->expects($this->any())
            ->method('pageDataAttributes')
            ->will($this->returnValue(array('url', 'description')));
        $model->expects($this->any())
            ->method('toplevelPages')
            ->will($this->returnValue(array(0, 2)));

        $this->views = new Views($model);
    }

    public function testSystemCheckHasDesiredStructure(): void
    {
        $checks = array('one' => 'success');
        $actual = $this->views->systemCheck($checks);
        Approvals::verifyHtml($actual);
    }

    public function testAdministrationHasForm(): void
    {
        $actual = $this->views->administration('url', '', '');
        $this->assertStringContainsString("<form ", $actual);
    }
}
