<?php

namespace Stuki\Form\Themes;

use Stuki\Form;

class Insert extends Form {

    public function init() {

        // New renderer class input
        $alias = $this->createElement('text', 'alias');
        $alias->setLabel('Alias');
        $alias->addValidator('stringLength', false, array(1, 255));
        $alias->setDescription('An alias to display for this theme');
        $alias->setRequired(true);

        $alias->size = 35;
        $alias->maxlength = 255;
        $alias->tabindex = 1;

        $class = $this->createElement('text', 'template');
        $class->setLabel('Theme');
        $class->addValidator('stringLength', false, array(1, 255));
        $class->setDescription('Enter the relative path to the theme file e.g. theme/view.phtml');

        $class->size = 35;
        $class->maxlength = 255;
        $class->tabindex = 2;

        $submit = $this->createElement('submit', 'submit');
        $submit->setLabel("Add Theme");
        $submit->setIgnore(true);
        $submit->tabindex = 3;


        // Build the form
        $this->addElement($alias);
        $this->addElement($class);
        $this->addElement($submit);
    }
}