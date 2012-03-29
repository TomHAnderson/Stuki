<?php

namespace Many2Many;

use Stuki\Plugin\Plugin as Plugin,
    Stuki\Plugin\Parameters,
    Many2Many\Form\Parameters as ParametersForm
;

class Many2Many implements Plugin, Parameters {

    protected $parameters;

    /**
     * This is the only function from the implementation
     */
    public function run(\Entities\AttributeSetPlugins $pluginXref, \Entities\Entities $entity) {
        $this->setParameters($pluginXref->getParameters());

        $locator = \Zend\Registry::get('locator');

        $modelEntityRelations = $locator->get('modelEntityRelations');
        $modelAttributeSets = $locator->get('modelAttributeSets');
        $modelEntities = $locator->get('modelEntities');
        $view = $locator->get('view');

        $attributeSet = $modelAttributeSets->find((int)$this->parameters['attribute_set_key']);

        // Pull existing relations
        $relations = $modelEntityRelations->findByAttributeSet($attributeSet, array(
            'parent' => $entity
        ));

        // Pull all possible relations
        # FIXME:  it may be better to dynamically pull relations when the user wishes to add
        # a new relation
        $referenceEntities = $modelEntities->findBy(array(
            'attributeSet' => $attributeSet
        ));


        $view->setVars(
            array(
                'pluginXref' => $pluginXref,
                'relations' => $relations,
                'referenceEntities' => $referenceEntities,
                'entity' => $entity,
            )
        );

        return $view->render('many2many/view.phtml');
    }

    public function getParametersForm() {
        return new ParametersForm();
    }

    public function setParameters($parameters) {
        $this->parameters = $parameters;
    }
}