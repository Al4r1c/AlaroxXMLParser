<?php
include_once('XMLElement.php');

class XMLParser
{
    /**
     * @var string
     */
    private $_initialContent;

    /**
     * @var XMLElement
     */
    private $_parsedData;

    /**
     * @var string[]
     */
    private $_error;

    /**
     * @return string
     */
    public function getInitialContent()
    {
        return $this->_initialContent;
    }

    /**
     * @return XMLElement
     */
    public function getParsedData()
    {
        return $this->_parsedData;
    }

    /**
     * @return array
     */
    public function getParsedDataAsAssocArray()
    {
        return $this->dataToArray($this->_parsedData);
    }

    /**
     * @param XMLElement $xmlElement
     * @return array
     */
    private function dataToArray($xmlElement)
    {
        $result = array();

        $result[$xmlElement->getName()]['attributes'] = $xmlElement->getAttributes();
        if ($xmlElement->hasChildren()) {
            foreach ($xmlElement->getChildren() as $unElementFils) {
                $result[$xmlElement->getName()]['children'][] = $this->dataToArray($unElementFils);
            }
        } else {
            $result[$xmlElement->getName()]['value'] = $xmlElement->getValue();
        }

        return $result;
    }

    /**
     * @return string
     */
    public function getErrorMessage()
    {
        if ($this->isValidXML()) {
            return null;
        } else {
            return sprintf(
                'XML error at line %d column %d: %s', $this->_error['line'], $this->_error['column'],
                $this->_error['message']
            );
        }
    }

    /**
     * @param $clefConfig
     * @return null|XMLElement[]
     */
    public function getValue($clefConfig)
    {
        if (false !== $valeur = $this->rechercheValeurTableauMultidim(
            explode('.', strtolower($clefConfig)), $this->_parsedData->getChildren()
        )
        ) {
            return $valeur;
        } else {
            return null;
        }
    }

    /**
     * @param string $contenuXml
     * @throws InvalidArgumentException
     */
    public function setInitialContent($contenuXml)
    {
        if (!is_string($contenuXml)) {
            throw new InvalidArgumentException('Invlid content: expected string.');
        }

        $this->_initialContent = $contenuXml;
    }

    public function setAndParseContent($contenuXml)
    {
        $this->setInitialContent($contenuXml);
        $this->parse();
    }

    /**
     * @return bool
     */
    public function isValidXML()
    {
        return empty($this->_error);
    }

    public function parse()
    {
        $parser = xml_parser_create();

        xml_set_object($parser, $this);
        xml_set_element_handler($parser, 'tagDebutXML', 'tagFinXML');
        xml_set_character_data_handler($parser, 'valeurXML');
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, false);

        $lignes = explode("\n", $this->_initialContent);
        foreach ($lignes as $uneLigne) {
            if (trim($uneLigne) == '') {
                continue;
            }

            $donnee = $uneLigne . "\n";

            if (!xml_parse($parser, $donnee)) {
                $this->_parsedData = null;
                $this->_error = array('line' => xml_get_current_line_number($parser),
                    'column' => xml_get_current_column_number($parser),
                    'message' => xml_error_string(xml_get_error_code($parser)));
            }
        }
        unset($GLOBALS['temporaire']);
    }

    /**
     * @param $parser
     * @param string $nom
     * @param string[] $attributs
     */
    private function tagDebutXML($parser, $nom, $attributs)
    {
        $this->_parsedData[] = array(
            'element' => strtolower($nom),
            'attr' => array_map('strtolower', $attributs),
            'children' => array()
        );
    }

    /**
     * @param $parser
     * @param string $nom
     */
    private function tagFinXML($parser, $nom)
    {
        end($this->_parsedData);

        $nouvelElement = new XMLElement();
        $nouvelElement->setDonnees($this->_parsedData[$last = key($this->_parsedData)]);

        if (count($this->_parsedData) > 1) {
            prev($this->_parsedData);
            $nouveauLast = key($this->_parsedData);

            $this->_parsedData[$nouveauLast]['children'][] = $nouvelElement;
            unset($this->_parsedData[$last]);
        } else {
            $this->_parsedData = $nouvelElement;
        }
    }

    /**
     * @param $parser
     * @param string $valeur
     */
    private function valeurXML($parser, $valeur)
    {
        if (trim($valeur) != '') {
            end($this->_parsedData);
            if (!isset($this->_parsedData[key($this->_parsedData)]['data'])) {
                $this->_parsedData[key($this->_parsedData)]['data'] = ltrim(str_replace("\n", '', $valeur));
            } else {
                $this->_parsedData[key($this->_parsedData)]['data'] .= str_replace("\n", '', $valeur);
            }
            $this->_parsedData[key($this->_parsedData)]['children'] = false;
        }
    }

    /**
     * @param $tabKey array
     * @param $arrayValues XMLElement[]
     * @return string|bool
     * */
    private function rechercheValeurTableauMultidim(array $tabKey, array $arrayValues)
    {
        if (count($tabKey) == 1) {
            $tabResult = array();

            if (preg_match_all('#^[a-z]+(\[[a-z]+=[a-z0-9]+\]){1}$#', $tabKey[0])) {
                $tabClef = explode('[', $tabKey[0]);
                $clef = $tabClef[0];
                $filtres = explode('=', $tabClef[1]);
                $filtres[1] = substr($filtres[1], 0, -1);
            } else {
                $clef = $tabKey[0];
            }

            foreach ($arrayValues as $unElement) {
                if ($unElement->getName() === $clef) {
                    if (isset($filtres) && $unElement->getUnAttribut($filtres[0]) !== strtolower($filtres[1])) {
                        continue;
                    }

                    $tabResult[] = $unElement;
                }
            }

            return $tabResult;
        } else {
            foreach ($arrayValues as $unElement) {
                if ($unElement->getName() === $tabKey[0]) {
                    array_shift($tabKey);

                    return $this->rechercheValeurTableauMultidim($tabKey, $unElement->getChildren());
                }
            }

            return false;
        }
    }
}