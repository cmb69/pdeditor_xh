<?php

/**
 * Testing the CSRF protection.
 *
 * The environment variable CMSIMPLEDIR has to be set to the installation folder
 * (e.g. / or /cmsimple_xh/).
 *
 * PHP version 5
 *
 * @category  Testing
 * @package   Pdeditor
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2014 Christoph M. Becker <http://3-magi.net/>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link      http://3-magi.net/?CMSimple_XH/Pdeditor_XH
 */

/**
 * A test case to actually check the CSRF protection.
 *
 * @category Testing
 * @package  Pdeditor
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Pdeditor_XH
 */
class CSRFAttackTest extends PHPUnit_Framework_TestCase
{
    protected $url;

    protected $curlHandle;

    protected $cookieFile;

    /**
     * Log in to back-end and store cookies in a temp file.
     */
    public function setUp()
    {
        $this->url = 'http://localhost' . getenv('CMSIMPLEDIR');
        $this->cookieFile = tempnam(sys_get_temp_dir(), 'CC');

        $this->curlHandle = curl_init($this->url . '?&login=true&keycut=test');
        curl_setopt($this->curlHandle, CURLOPT_COOKIEJAR, $this->cookieFile);
        curl_setopt($this->curlHandle, CURLOPT_RETURNTRANSFER, true);
        curl_exec($this->curlHandle);
        curl_close($this->curlHandle);
    }

    protected function setCurlOptions($fields)
    {
        $options = array(
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $fields,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_COOKIEFILE => $this->cookieFile
        );
        curl_setopt_array($this->curlHandle, $options);
    }

    public function dataForAttack()
    {
        return array(
            array( // edit
                array('value' => 'foo'),
                '&pdeditor&admin=plugin_main&action=save&pdeditor_attr=description&edit'
            ),
            array( // delete
                array(),
                '&pdeditor&admin=plugin_main&action=delete&pdeditor_attr=description&edit'
            )
        );
    }

    /**
     * @dataProvider dataForAttack
     */
    public function testAttack($fields, $queryString = null)
    {
        $url = $this->url . (isset($queryString) ? '?' . $queryString : '');
        $this->curlHandle = curl_init($url);
        $this->setCurlOptions($fields);
        curl_exec($this->curlHandle);
        $actual = curl_getinfo($this->curlHandle, CURLINFO_HTTP_CODE);
        curl_close($this->curlHandle);
        $this->assertEquals(403, $actual);
    }
}

?>
