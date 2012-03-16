<?php

namespace DefaultRenderers;

use Stuki\Renderer\Renderer as Renderer,
    Zend\Form\Element\Text as Text;

class String extends Text implements Renderer
{
    private $datatype = 'varchar';

    public function getDataType() {
        return $this->datatype;
    }

    public function postCreate() {
        $this->size = 55;
        $this->addValidator('stringLength', false, array(1, 255));
    }

    public function formatValue($value) {
        return htmlentities($value, ENT_COMPAT, 'UTF-8');
    }

    public function formatEditValue($value) {
        return $value;
    }

    // Format for search
    public function formatSearchValue($value) {
        return $value;
    }
}
