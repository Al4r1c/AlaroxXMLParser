<?php
class XMLElementTest extends PHPUnit_Framework_TestCase
{
    /** @var XMLElement */
    private $_xmlElement;

    public function setUp()
    {
        $this->_xmlElement = new XMLElement();
    }

    public function testSetNom()
    {
        $this->_xmlElement->setNom('nom');

        $this->assertEquals('nom', $this->_xmlElement->getName());
    }

    /**
     * @expectedException     InvalidArgumentException
     */
    public function testSetNomErrone()
    {
        $this->_xmlElement->setNom(array());
    }

    public function testSetChildren()
    {
        $this->_xmlElement->setChildren(array(new XMLElement()));

        $this->assertEquals(array(new XMLElement()), $this->_xmlElement->getChildren());
    }

    /**
     * @expectedException     InvalidArgumentException
     */
    public function testSetChildrenErrone()
    {
        $this->_xmlElement->setChildren('fils');
    }

    /**
     * @expectedException     InvalidArgumentException
     */
    public function testSetChildrenErrone2()
    {
        $this->_xmlElement->setChildren(array(5));
    }

    public function testGetChildrenMaisAucunChildren()
    {
        $this->_xmlElement->setChildren(false);

        $this->assertEquals(array(), $this->_xmlElement->getChildren());
    }

    public function testSetAttributs()
    {
        $this->_xmlElement->setAttributs(array('param1' => 'val1'));

        $this->assertEquals(array('param1' => 'val1'), $this->_xmlElement->getAttributes());
    }

    /**
     * @expectedException     InvalidArgumentException
     */
    public function testSetAttributsErrone()
    {
        $this->_xmlElement->setAttributs('stringgggggg');
    }

    public function testGetAttribut()
    {
        $this->_xmlElement->setAttributs(array('param1' => 'val1', 'param2' => 'val2'));

        $this->assertEquals('val2', $this->_xmlElement->getUnAttribut('param2'));
    }

    public function testGetAttributNonExistant()
    {
        $this->_xmlElement->setAttributs(array('param1' => 'val1'));

        $this->assertNull($this->_xmlElement->getUnAttribut('param2'));
    }

    public function testSetValeur()
    {
        $this->_xmlElement->setValeur('valeur');

        $this->assertEquals('valeur', $this->_xmlElement->getValue());
    }

    /**
     * @expectedException     InvalidArgumentException
     */
    public function testSetValeurErrone()
    {
        $this->_xmlElement->setValeur(null);
    }

    public function testSetDonnees()
    {
        $this->_xmlElement->setDonnees(
            array('element' => 'nom',
                'attr' => array('attr1' => 'val1'),
                'children' => array(new XMLElement()),
                'data' => 'value')
        );

        $this->assertEquals('nom', $this->_xmlElement->getName());
        $this->assertEquals(array('attr1' => 'val1'), $this->_xmlElement->getAttributes());
        $this->assertEquals(array(new XMLElement()), $this->_xmlElement->getChildren());
        $this->assertEquals('value', $this->_xmlElement->getValue());
    }
}