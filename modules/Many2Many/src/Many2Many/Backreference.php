<?php

namespace Many2Many;

use Stuki\Plugin\Plugin as Plugin,
    Stuki\Plugin\Parameters,
    Many2Many\Form\Parameters as ParametersForm
;

class Backreference implements Plugin {

    protected $parameters;

    /**
     * This is the only function from the implementation
     */
    public function run(\Entities\AttributeSetPlugins $pluginXref, \Entities\Entities $entity) {
        $locator = \Zend\Registry::get('locator');

        $modelEntityRelations = $locator->get('modelEntityRelations');
        $modelAttributeSets = $locator->get('modelAttributeSets');
        $modelEntities = $locator->get('modelEntities');
        $view = $locator->get('view');

        $attributeSet = $modelAttributeSets->find((int)$this->parameters['attribute_set_key']);

        // Pull existing relations
        $relations = $modelEntityRelations->findBy(array(
            'child' => $entity
        ));

        $view->setVars(
            array(
                'pluginXref' => $pluginXref,
                'relations' => $relations,
                'entity' => $entity,
            )
        );

        return $view->render('many2many/backreference.phtml');
    }
}