<?php

namespace DefaultRenderers\Forms\AttributeSetSelectList;

use Zend\Form\Form as ZFForm;

class Form extends ZFForm {
    function init() {
        $param1 = $this->createElement('select', 'attribute_set_key');
        $param1->setLabel('Attribute Set');
        $param1->setDescription("Select the attribute set to show entities");

        $locator = \Zend\Registry::get('locator');

        $modelAttributeSets = $locator->get('modelAttributeSets');

        foreach ($modelAttributeSets->findAll() as $att) {
            $param1->addMultiOption($att->getKey(), $att->getName());
        }

        // Create submit button
        $submit = $this->createElement('submit', 'save');
        $submit->setLabel("Save");

        $this->addElement($param1);
        $this->addElement($submit);
    }
}
