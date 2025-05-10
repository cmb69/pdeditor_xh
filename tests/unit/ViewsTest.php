<?php

/**
 * A test case for the views class.
 *
 * PHP version 5
 *
 * @category  CMSimple_XH
 * @package   Pdeditor
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2013-2015 Christoph M. Becker <http://3-magi.net/>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link      http://3-magi.net/?CMSimple_XH/Pdeditor_XH
 */

use ApprovalTests\Approvals;
use PHPUnit\Framework\TestCase;

/**
 * The version number of the plugin.
 */
define('PDEDITOR_VERSION', '1.0');

/**
 * A test case for the views class.
 *
 * @category CMSimple_XH
 * @package  Pdeditor
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Pdeditor_XH
 */
class ViewsTest extends TestCase
{
    /**
     * The views.
     *
     * @var Pdeditor_Views
     */
    protected $views;

    /**
     * Sets up the test fixture.
     *
     * @return void
     */
    public function setUp(): void
    {
        global $cf, $tx, $plugin_tx;
        $cf = ["xhtml" => ["endtags" => ""]];
        $tx = ["action" => ["save" => ""]];
        $plugin_tx = ["pdeditor" => XH_includeVar("./languages/en.php", "plugin_tx")["pdeditor"]];
        $model = $this->getMockBuilder('Pdeditor_Model')
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

        $this->views = new Pdeditor_Views($model);
    }

    /**
     * Tests whether ::about() shows the version.
     *
     * @return void
     */
    public function testAboutShowsVersion()
    {
        $actual = $this->views->about();
        $this->assertStringContainsString("<p>Version: 1.0</p>", $actual);
    }

    /**
     * Tests whether ::systemCheck() shows the desired structure.
     *
     * @return void
     */
    public function testSystemCheckHasDesiredStructure()
    {
        $checks = array('one' => 'ok');
        $actual = $this->views->systemCheck($checks);
        Approvals::verifyHtml($actual);
    }

    /**
     * Tests whether ::administration() returns a form.
     *
     * @return void
     */
    public function testAdministrationHasForm()
    {
        $actual = $this->views->administration('url', '', '');
        $this->assertStringContainsString("<form ", $actual);
    }

    /**
     * Checks for bug, where img tag missed < (reported by learnandcode)
     *
     * @return void
     */
    public function testAdministrationShowsWarningIcon()
    {
        $actual = $this->views->administration('url', '', '');
        $this->assertStringContainsString("<img ", $actual);
    }
}

?>
