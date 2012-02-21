<?php

namespace DefaultRenderers\Parameters;

use Zend\Form\Form as ZFForm;

class Form extends ZFForm {
    function init() {
        $param1 = $this->createElement('text', 'param1');
        $param1->setLabel('Param 1');

        $param2 = $this->createElement('text', 'param2');
        $param2->setLabel('Param 2');

        $param3 = $this->createElement('text', 'param3');
        $param3->setLabel('Param 3');

        // Create submit button
        $submit = $this->createElement('submit', 'save');
        $submit->setLabel("Save");

        $this->addElement($param1);
        $this->addElement($param2);
        $this->addElement($param3);
        $this->addElement($submit);
    }
}