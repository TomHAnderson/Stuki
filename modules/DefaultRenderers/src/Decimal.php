<?php

namespace DefaultRenderers;

use Stuki\Renderer\Renderer,
    Zend\Form\Element\Text;

class Decimal extends Text implements Renderer
{
    private $datatype = 'decimal';

    public function getDataType() {
        return $this->datatype;
    }

    public function postCreate() {
        $this->addValidator('Float');
    }

    public function formatValue($value) {
        if (!$value) return '';
        return trim(trim(number_format($value, 10), '0'), '.');
    }

    // Preformat value when editing such as date times > dates
    public function formatEditValue($value) {
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
