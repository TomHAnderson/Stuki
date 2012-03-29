<?php

namespace Stuki\Form\Plugins;

use Stuki\Form;

class UpdateXref extends Form {

    public function init() {

        // New renderer class input
        $alias = $this->createElement('text', 'alias');
        $alias->setLabel('Alias');
        $alias->addValidator('stringLength', false, array(1, 255));
        $alias->setDescription('An alias to display for this plugin');
        $alias->setRequired(true);

        $alias->size = 35;
        $alias->maxlength = 255;
        $alias->tabindex = 1;

        $submit = $this->createElement('submit', 'submit');
        $submit->setLabel("Update");
        $submit->setIgnore(true);
        $submit->tabindex = 2;


        // Build the form
        $this->addElement($alias);
        $this->addElement($submit);
    }
}