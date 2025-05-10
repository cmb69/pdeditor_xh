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
        global $h, $cf, $tx, $plugin_tx;
        $h = array('Welcome', 'About', 'Contact');
        $cf = ["xhtml" => ["endtags" => ""]];
        $tx = ["action" => ["save" => ""]];
        $plugin_tx = ["pdeditor" => XH_includeVar("./languages/en.php", "plugin_tx")["pdeditor"]];
        $model = $this->getMockBuilder(Model::class)
            ->disableOriginalConstructor()
            ->getMock();
        $model->expects($this->any())
            ->method('pluginIconPath')
            ->will($this->returnValue('foo'));
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
        $checks = array('one' => 'ok');
        $actual = $this->views->systemCheck($checks);
        Approvals::verifyHtml($actual);
    }

    public function testAdministrationHasForm(): void
    {
        $actual = $this->views->administration('url', '', '');
        $this->assertStringContainsString("<form ", $actual);
    }

    /**
     * Checks for bug, where img tag missed < (reported by learnandcode)
     */
    public function testAdministrationShowsWarningIcon(): void
    {
        $actual = $this->views->administration('url', '', '');
        $this->assertStringContainsString("<img ", $actual);
    }
}
