<?php

namespace Stuki\Form\Renderers;

use Stuki\Form;

class Insert extends Form {

    public function init() {

        // New renderer class input
        $alias = $this->createElement('text', 'alias');
        $alias->setLabel('Alias');
        $alias->addValidator('stringLength', false, array(1, 255));
        $alias->setDescription('An alias to display when selecting this renderer');
        $alias->setRequired(true);
        # FIXME:  add validator for regex not allowing leading \

        $alias->size = 35;
        $alias->maxlength = 255;
        $alias->tabindex = 1;

        $class = $this->createElement('text', 'class');
        $class->setLabel('Renderer');
        $class->addValidator('stringLength', false, array(1, 255));
        $class->setDescription('Enter the fully namespaced renderer class such as Stuki\Renderer\Date');
        # FIXME:  add validator for regex not allowing leading \

        $class->size = 35;
        $class->maxlength = 255;
        $class->tabindex = 2;

        $submit = $this->createElement('submit', 'submit');
        $submit->setLabel("Add");
        $submit->setIgnore(true);
        $submit->tabindex = 3;


        // Build the form
        $this->addElement($alias);
        $this->addElement($class);
        $this->addElement($submit);
    }
}