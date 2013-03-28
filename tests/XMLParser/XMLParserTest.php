<?php
class XMLParserTest extends PHPUnit_Framework_TestCase
{
    /** @var XMLParser */
    private $_xmlParser;

    public function setUp()
    {
        $this->_xmlParser = new XMLParser();
    }

    public function testSetContenu()
    {
        $this->_xmlParser->setInitialContent("<root></root>");

        $this->assertEquals("<root></root>", $this->_xmlParser->getInitialContent());
    }

    /**
     * @expectedException     InvalidArgumentException
     */
    public function testSetContenuErrone()
    {
        $this->_xmlParser->setInitialContent(array());
    }

    public function testGetErreurVide()
    {
        $this->assertNull($this->_xmlParser->getErrorMessage());
    }

    public function testParse()
    {
        $this->_xmlParser->setAndParseContent("<root></root>");

        $this->assertEquals('root', $this->_xmlParser->getParsedData()->getName());
    }

    public function testParseErreur()
    {
        $this->_xmlParser->setAndParseContent("<root></toor>");

        $this->assertNull($this->_xmlParser->getParsedData());
        $this->assertFalse($this->_xmlParser->isValidXML());
        $this->assertInternalType('string', $this->_xmlParser->getErrorMessage());
    }

    public function testGetValeur()
    {
        $this->_xmlParser->setAndParseContent(" <root> \n \n <elem>value</elem></root>");
        $this->assertEquals('elem', $this->_xmlParser->getValue('elem')[0]->getName());
        $this->assertEquals('value', $this->_xmlParser->getValue('elem')[0]->getValue());
    }

    public function testGetValeurLointaine()
    {
        $this->_xmlParser->setAndParseContent("<root><elem><deeper><yes>valeur</yes></deeper></elem></root>");

        $this->assertEquals('yes', $this->_xmlParser->getValue('elem.deeper.yes')[0]->getName());
        $this->assertEquals('valeur', $this->_xmlParser->getValue('elem.deeper.yes')[0]->getValue());
    }

    public function testGetValeurAttribut()
    {
        $this->_xmlParser->setAndParseContent("<root><elem attr=\"y\">ok</elem><elem attr=\"n\">nok</elem></root>");

        $this->assertEquals('elem', $this->_xmlParser->getValue('elem[attr=y]')[0]->getName());
        $this->assertEquals('ok', $this->_xmlParser->getValue('elem[attr=y]')[0]->getValue());
    }

    public function testParseMemeTag()
    {
        $this->_xmlParser->setAndParseContent(
            "<root><element><element><element>value</element></element></element><element>value2</element></root>"
        );

        $this->assertTrue($this->_xmlParser->isValidXML());
    }

    public function testGetValeurSupportAccent()
    {
        $this->_xmlParser->setAndParseContent("<root><elem>AccentuéHey</elem></root>");

        $this->assertEquals('elem', $this->_xmlParser->getValue('elem')[0]->getName());
        $this->assertEquals('AccentuéHey', $this->_xmlParser->getValue('elem')[0]->getValue());
    }

    public function testGetValeurExistePasNull()
    {
        $this->_xmlParser->setAndParseContent("<root><elem></elem></root>");

        $this->assertNull($this->_xmlParser->getValue('fake.elem'));
    }

    public function testGetDonneesParseesAssocArray()
    {
        $this->_xmlParser->setAndParseContent("<root><elem>tttt</elem><elem2><elem3>Yuleh</elem3></elem2></root>");

        $this->assertEquals(
            array('root' => array('attributes' => array(),
                'children' => array(
                    array('elem' => array('attributes' => array(), 'value' => 'tttt')),
                    array('elem2' => array('attributes' => array(),
                        'children' => array(
                            array('elem3' => array('attributes' => array(), 'value' => 'Yuleh')),
                        ))),
                ))),
            $this->_xmlParser->getParsedDataAsAssocArray()
        );
    }

    public function testSetAndParseContenu()
    {
        $this->_xmlParser->setAndParseContent("<root><elem>heyho</elem></root>");

        $this->assertEquals('elem', $this->_xmlParser->getValue('elem')[0]->getName());
        $this->assertEquals('heyho', $this->_xmlParser->getValue('elem')[0]->getValue());

    }
}