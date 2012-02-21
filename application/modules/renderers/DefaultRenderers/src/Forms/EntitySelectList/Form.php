<?php

namespace DefaultRenderers\EntitySelectList;

use Zend\Form\Form as ZFForm;

class Form extends ZFForm {
    function init() {
        $param1 = $this->createElement('text', 'entity_key');
        $param1->setLabel('Root Options Entity Key');


        // Create submit button
        $submit = $this->createElement('submit', 'save');
        $submit->setLabel("Save");

        $this->addElement($param1);
        $this->addElement($submit);
    }
}