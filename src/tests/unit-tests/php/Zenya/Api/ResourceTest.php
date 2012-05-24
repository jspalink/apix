<?php
#require_once dirname(__FILE__) . '/../../../../../../../../../../../../../www/ENCOURS/zenya-api-server/src/php/Zenya/Api/Server.php';

/**
 * Copyright (c) 2011 Zenya.com
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Zenya nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package     Zenya
 * @subpackage  ApiServer
 * @author      Franck Cassedanne <fcassedanne@zenya.com>
 * @copyright   2011 zenya.com
 * @license     http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link        http://zenya.github.com
 * @version     @@PACKAGE_VERSION@@
 */

namespace Zenya\Api;

class HelpTest
{
    /*
     * Hold the output array
     */
    protected $_output = array();

    public function __construct()
    {
    }

    public function toArray()
    {
        return $this->_output;
    }
}

/**
 * Test class for Resource.
 * Generated by PHPUnit on 2012-05-10 at 10:48:49.
 */
class ResourceTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Resource object
     */
    protected $Obj;

    public $resources = array('BlankResource'=>'Zenya\Api\Resource\BlankResource');

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->Obj = new Resource($this->resources);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {

    }

    /**
     * @covers Zenya\Api\Resource::getPublicAppelation
     */
    public function testGetPublicAppelationAlwaysUcFirst()
    {
        // check resource name is always higner case
        $this->assertEquals('BlankResource', $this->Obj->getPublicAppelation('BlankResource'));
    }

    /**
     * @covers						Zenya\Api\Resource::getPublicAppelation
     * @expectedException			Zenya\Api\Exception
     * @expectedExceptionMessage	Invalid resource's name specified (Resource-that-does-not-exist-ever)
     * @expectedExceptionCode		404
      */
    public function testGetPublicAppelationThrowsException()
    {
        $this->Obj->getPublicAppelation('resource-that-does-not-exist-ever');
    }

    /**
     * @covers Zenya\Api\Resource::getInternalAppelation
     */
    public function testGetInternalAppelation()
    {
        $this->assertEquals('Zenya\Api\Resource\BlankResource', $this->Obj->getInternalAppelation('blankResource'));
    }

    /**
     * @covers Zenya\Api\Resource::getResources
     */
    public function testGetResources()
    {
        $this->assertSame( $this->resources, $this->Obj->getResources() );
    }

    /**
     * @covers Zenya\Api\Resource::call
     * @todo: mock request
     */
    public function testCallResourceReturnAnArray()
    {
        // mock request


#		$results = $this->Obj->call('BlankResource');
#		$this->assertTrue( is_array( $results ));
#		print_r($results);
    }

}
