<?php
class XMLElement
{
    /**
     * @var string
     */
    private $_nom;

    /**
     * @var string[]
     */
    private $_attributs = array();

    /**
     * @var XMLElement[]|bool
     */
    private $_children = array();

    /**
     * @var string
     */
    private $_valeur;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->_nom;
    }

    /**
     * @return \string[]
     */
    public function getAttributes()
    {
        return $this->_attributs;
    }

    /**
     * @param $attribut
     * @return null|string
     */
    public function getUnAttribut($attribut)
    {
        if (array_key_exists(strtolower($attribut), $this->_attributs)) {
            return $this->_attributs[strtolower($attribut)];
        } else {
            return null;
        }
    }

    /**
     * @return array|XMLElement[]
     */
    public function getChildren()
    {
        if ($this->_children === false) {
            return array();
        }

        return $this->_children;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->_valeur;
    }

    /**
     * @param string $nom
     * @throws InvalidArgumentException
     */
    public function setNom($nom)
    {
        if (!is_string($nom)) {
            throw new InvalidArgumentException('Invalid element name: expected string.');
        }

        $this->_nom = $nom;
    }

    /**
     * @param string[] $attributs
     * @throws InvalidArgumentException
     */
    public function setAttributs($attributs)
    {
        if (!is_array($attributs)) {
            throw new InvalidArgumentException('Invalid attributes value: expected array.');
        }

        $this->_attributs = $attributs;
    }

    /**
     * @param XMLElement[]|bool $children
     * @throws InvalidArgumentException
     */
    public function setChildren($children)
    {
        if (!is_array($children) && !is_bool($children)) {
            throw new InvalidArgumentException('Invalid children value: expected array or boolean.');
        }

        if (is_array($children)) {
            foreach ($children as $unFils) {
                if (!$unFils instanceof XMLElement) {
                    throw new InvalidArgumentException('Invalid children value: expected XMLElement.');
                }
            }
        }

        $this->_children = $children;
    }

    /**
     * @param string $valeur
     * @throws InvalidArgumentException
     */
    public function setValeur($valeur)
    {
        if (!is_string($valeur)) {
            throw new InvalidArgumentException('Invalid value: expected string.');
        }

        $this->_valeur = $valeur;
    }

    /**
     * @param array $donnees
     */
    public function setDonnees($donnees)
    {
        $this->setNom($donnees['element']);
        $this->setAttributs($donnees['attr']);
        $this->setChildren($donnees['children']);
        if (isset($donnees['data'])) {
            $this->setValeur($donnees['data']);
        }
    }
}