<?php

namespace DefaultRenderers;

use Stuki\Renderer\Renderer as Renderer,
    Stuki\Renderer\Select as RendererSelect,
    Zend\Form\Element\Select as ElementSelect;

class Select extends ElementSelect implements Renderer, RendererSelect
{
    public $datatype = 'varchar';

    public function getDataType() {
        return $this->datatype;
    }

    public function postCreate() {
        $this->addValidator('stringLength', false, array(1, 255));
    }

    public function formatValue($value) {
        return htmlentities($value, ENT_COMPAT, 'UTF-8');
    }

    // Preformat value when editing such as date times > dates
    public function formatEditValue($value) {
        return $value;
    }

    public function formatSearchValue($value) {
        return $value;
    }

    public function getRendererMultiOptions(\Entities\Attributes $attribute) {
        return $attribute->getOptions();
    }
}
