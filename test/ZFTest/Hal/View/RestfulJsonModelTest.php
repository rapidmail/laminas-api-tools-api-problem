<?php
/**
 * @license   http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 */

namespace ZFTest\Hal\View;

use PHPUnit_Framework_TestCase as TestCase;
use stdClass;
use ZF\Hal\HalCollection;
use ZF\Hal\HalResource;
use ZF\Hal\View\RestfulJsonModel;

/**
 * @subpackage UnitTest
 */
class RestfulJsonModelTest extends TestCase
{
    public function setUp()
    {
        $this->model = new RestfulJsonModel;
    }

    public function testPayloadIsNullByDefault()
    {
        $this->assertNull($this->model->getPayload());
    }

    public function testPayloadIsMutable()
    {
        $this->model->setPayload('foo');
        $this->assertEquals('foo', $this->model->getPayload());
    }

    public function invalidPayloads()
    {
        return array(
            'null'       => array(null),
            'true'       => array(true),
            'false'      => array(false),
            'zero-int'   => array(0),
            'int'        => array(1),
            'zero-float' => array(0.0),
            'float'      => array(1.1),
            'string'     => array('string'),
            'array'      => array(array()),
            'stdclass'   => array(new stdClass),
        );
    }

    public function invalidHalCollectionPayloads()
    {
        $payloads = $this->invalidPayloads();
        $payloads['exception'] = array(new \Exception);
        $payloads['stdclass']  = array(new stdClass);
        $payloads['hal-item']  = array(new HalResource(array(), 'id', 'route'));
        return $payloads;
    }

    /**
     * @dataProvider invalidHalCollectionPayloads
     */
    public function testIsHalCollectionReturnsFalseForInvalidValues($payload)
    {
        $this->model->setPayload($payload);
        $this->assertFalse($this->model->isHalCollection());
    }

    public function testIsHalCollectionReturnsTrueForHalCollectionPayload()
    {
        $collection = new HalCollection(array(), 'item/route');
        $this->model->setPayload($collection);
        $this->assertTrue($this->model->isHalCollection());
    }

    public function invalidHalResourcePayloads()
    {
        $payloads = $this->invalidPayloads();
        $payloads['exception']      = array(new \Exception);
        $payloads['stdclass']       = array(new stdClass);
        $payloads['hal-collection'] = array(new HalCollection(array(), 'item/route'));
        return $payloads;
    }

    /**
     * @dataProvider invalidHalResourcePayloads
     */
    public function testIsHalResourceReturnsFalseForInvalidValues($payload)
    {
        $this->model->setPayload($payload);
        $this->assertFalse($this->model->isHalResource());
    }

    public function testIsHalResourceReturnsTrueForHalResourcePayload()
    {
        $item = new HalResource(array(), 'id', 'route');
        $this->model->setPayload($item);
        $this->assertTrue($this->model->isHalResource());
    }
}
