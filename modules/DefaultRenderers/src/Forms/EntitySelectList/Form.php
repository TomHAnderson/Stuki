<?php

namespace DefaultRenderers\Forms\EntitySelectList;

use Zend\Form\Form as ZFForm;

class Form extends ZFForm {
    function init() {
        $param1 = $this->createElement('text', 'entity_key');
        $param1->setLabel('Options Entity Key');
	$param1->setDescription("Enter the entity key for the root element which has references you want to list in your attribute");


        // Create submit button
        $submit = $this->createElement('submit', 'save');
        $submit->setLabel("Save");

        $this->addElement($param1);
        $this->addElement($submit);
    }
}
