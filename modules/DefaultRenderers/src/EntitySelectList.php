<?php

namespace DefaultRenderers;

use Stuki\Renderer\Renderer as Renderer,
    Stuki\Renderer\Select as RendererSelect,
    Stuki\Renderer\Parameters as RendererParameters,
    DefaultRenderers\Forms\EntitySelectList\Form as ParametersForm,
    Zend\Form\Element\Select as ElementSelect;

class EntitySelectList extends ElementSelect implements Renderer, RendererParameters, RendererSelect
{
    public $datatype = 'integer';
    private $parameters;

    public function getDataType() {
        return $this->datatype;
    }

    public function postCreate() {
        $this->addValidator('Int');
        //$this->addValidator('stringLength', false, array(1, 255));
    }

    public function formatValue($value) {
        $locator = \Zend\Registry::get('locator');
        $modelEntities = $locator->get('modelEntities');
        $entity = $modelEntities->find((int)$value);
        return '<a href="/entities/view?entity_key=' . $entity->getKey() . '">' . $entity->getTitle() . '</a>' ;
    }

    // Preformat value when editing such as date times > dates
    public function formatEditValue($value) {
        return $value;
    }

    public function formatSearchValue($value) {
        $locator = \Zend\Registry::get('locator');
        $modelEntities = $locator->get('modelEntities');
        return $modelEntities->find((int)$value)->getTitle();
    }

    /**
     * Parameter options
     */
    public function getParametersForm() {
        return new ParametersForm();
    }

    // getParameters is an attribute method
    public function setParameters($array) {
        $this->parameters = $array;
    }

    public function getRendererMultiOptions(\Entities\Attributes $attribute) {
        $locator = \Zend\Registry::get('locator');
        $modelEntities = $locator->get('modelEntities');
        if (!$select = $modelEntities->find((int)$this->parameters['entity_key']))
            throw new \Stuki\Exception("Cannot find root options entity");

        $return = array();
        foreach ($select->getChildren() as $option) {
            $return[$option->getKey()] = $option->getTitle();
        }
        return $return;
    }
}
