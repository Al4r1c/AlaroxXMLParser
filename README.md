AlaroxXMLParser v0.3
===============

Very simple XML parser.


Parser
===============

Parse a XML string and get its associative array:

    $xmlParser = new XMLParser();

    $xmlParser->setAndParseContent('<root><elem>value</elem><nextelem><child>myChild</child><child attr="this">mySecondChild</child></nextelem></root>');

    var_dump($xmlParser->getParsedDataAsAssocArray());

	array(2) {
	  'elem' =>
	  string(5) "value"
	  'nextelem' =>
	  array(1) {
	    'child' =>
	    string(7) "myChild"
	  }
	}


Find a value
===============

Find a value:

	$this->_xmlParser->getValue('elem');

	array(1) {
	  [0] =>
	  class XMLElement#232 (4) {
	    private $_nom =>
	    string(4) "elem"
	    private $_attributs =>
	    array(0) {
	    }
	    private $_children =>
	    bool(false)
	    private $_valeur =>
	    string(5) "value"
	  }
	}

Find a value navigating in the tree with .(dot):

	$this->_xmlParser->getValue('nextelem.child');

	array(2) {
	  [0] =>
	  class XMLElement#231 (4) {
	    private $_nom =>
	    string(5) "child"
	    private $_attributs =>
	    array(0) {
	    }
	    private $_children =>
	    bool(false)
	    private $_valeur =>
	    string(7) "myChild"
	  }
	  [1] =>
	  class XMLElement#230 (4) {
	    private $_nom =>
	    string(5) "child"
	    private $_attributs =>
	    array(1) {
	      'attr' =>
	      string(4) "this"
	    }
	    private $_children =>
	    bool(false)
	    private $_valeur =>
	    string(13) "mySecondChild"
	  }
	}

Find a value with a specified attribute value:

	$this->_xmlParser->getValue('nextelem.child[attr=this]');

	array(1) {
      [0] =>
      class XMLElement#230 (4) {
        private $_nom =>
        string(5) "child"
        private $_attributs =>
        array(1) {
          'attr' =>
          string(4) "this"
        }
        private $_children =>
        bool(false)
        private $_valeur =>
        string(13) "mySecondChild"
      }
    }

Get element name, attributes, children or value.
===============

	$xmlElement = $this->_xmlParser->getValue('elem')[0];

    $xmlElement->getName();
    $xmlElement->getAttributes();
    $xmlElement->getValue();
    $xmlElement->getChildren();