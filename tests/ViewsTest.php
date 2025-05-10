<?php

namespace Pdeditor;

use PHPUnit\Framework\TestCase;
use Plib\CsrfProtector;

define('PDEDITOR_VERSION', '1.0');

class ViewsTest extends TestCase
{
    /** @var Views */
    private $views;

    public function setUp(): void
    {
        global $h, $plugin_tx;
        $h = array('Welcome', 'About', 'Contact');
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

        $csrfProtector = $this->createStub(CsrfProtector::class);
        $csrfProtector->method("token")->willReturn("123456789ABCDEF");
        $this->views = new Views($model, $csrfProtector);
    }

    public function testAdministrationHasForm(): void
    {
        $actual = $this->views->administration('url', '', '');
        $this->assertStringContainsString("<form ", $actual);
    }
}
