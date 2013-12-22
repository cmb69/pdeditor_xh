<?php

/**
 * A test case for the views class.
 *
 * PHP version 5
 *
 * @category  CMSimple_XH
 * @package   Pdeditor
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2013 Christoph M. Becker <http://3-magi.net/>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @version   SVN: $Id$
 * @link      http://3-magi.net/?CMSimple_XH/Pdeditor_XH
 */

/**
 * The model class.
 */
require_once './classes/Model.php';

/**
 * The class under test.
 */
require_once './classes/Views.php';

/**
 * The version number of the plugin.
 */
define('PDEDITOR_VERSION', '@PDEDITOR_VERSION@');

/**
 * A test case for the views class.
 *
 * @category CMSimple_XH
 * @package  Pdeditor
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Pdeditor_XH
 */
class ViewsTest extends PHPUnit_Framework_TestCase
{
    protected $views;

    public function setUp()
    {
        $model = $this->getMockBuilder('Pdeditor_Model')
            ->disableOriginalConstructor()
            ->getMock();
        $model->expects($this->any())
             ->method('pluginIconPath')
             ->will($this->returnValue('foo'));

        $this->views = new Pdeditor_Views($model);
    }

    public function testAboutShowsVersion()
    {
        $matcher = array('tag' => 'p', 'content' => '@PDEDITOR_VERSION@');
        $actual = $this->views->about();
        $this->assertTag($matcher, $actual);
    }

    public function testAboutShowsCurrentYear()
    {
        $currentYear = date('Y');
        $matcher = array('tag' => 'p', 'content' => $currentYear);
        $actual = $this->views->about();
        $this->assertTag($matcher, $actual);
    }

    public function testSystemCheckHasDesiredStructure()
    {
        $checks = array('one' => 'ok');
        $matcher = array(
            'tag' => 'ul',
            'attributes' => array('class' => 'pdeditor_system_check'),
            'children' => array('count' => count($checks))
        );
        $actual = $this->views->systemCheck($checks);
        $this->assertTag($matcher, $actual);
    }
}

?>
