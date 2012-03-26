<?php

/**
 * This is a testing renderer for parameters
 * This is not intended for production.
 */

namespace DefaultRenderers;

use Stuki\Renderer\Renderer,
    Stuki\Renderer\Parameters as RendererParameters,
    Zend\Form\Element\Text,
    DefaultRenderers\Parameters\Form as ParametersForm;

class Parameters extends Text implements Renderer, RendererParameters
{
    private $datatype = 'varchar';

    /**
     * Parameters may be used in any other method to
     * configure the renderer
     */
    private $parameters;

    public function getParametersForm() {
        return new ParametersForm();
    }

    // getParameters is an attribute method
    public function setParameters($array) {
        $this->parameters = $array;
    }

    public function getDataType() {
        return $this->datatype;
    }

    public function postCreate() {
        $this->addValidator('Date');
        $this->size = 10;

        $this->class = "date";

        return $this;
    }

    // For display
    public function formatValue($value) {
        if (!$value) return '';
        return $value->format('Y-m-d');
    }

    // For editing
    public function formatEditValue($value) {
        if (!$value) return '';
        return $value->format('Y-m-d');
    }

    // Format for search
    public function formatSearchValue($value) {
        return $value;
    }
}
