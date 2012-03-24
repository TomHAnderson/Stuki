<?php

namespace DefaultRenderers;

use Stuki\Renderer\Renderer,
    Zend\Form\Element\Text;

class Integer extends Text implements Renderer
{
    private $datatype = 'integer';

    public function getDataType() {
        return $this->datatype;
    }

    public function formatValue($value)
    {
        return $value;
    }

    public function postCreate()
    {
        $this->addValidator('Int');
    }

    // Preformat value when editing such as date times > dates
    public function formatEditValue($value)
    {
        return $value;
    }

    // Format for search
    public function formatSearchValue($value) {
        return $value;
    }

    /**
     * Return an about section of the ini
     */
    public function getAbout($field) {
        $ini = new \Zend_Config_Ini(__DIR__ . '/plugin.ini', 'about');
        return $ini->$field;
    }

}
