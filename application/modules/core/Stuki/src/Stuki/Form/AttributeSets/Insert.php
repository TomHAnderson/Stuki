<?php

namespace Stuki\Form\AttributeSets;

use Stuki\Form,
    Zend\Validator as Validate;

class Insert extends Form
{
    public function init()
    {

        // Code - add validator called in isValid()
        $name = $this->createElement('text', 'name');
        $name->addValidator('stringLength', false, array(1, 255))
                 ->setRequired(true)
                 ->setLabel('Attribute Set Name')
                 ->setDescription('A name to describes this attribute set.');
        $name->size = 55;

        // Code
        $code = $this->createElement('text', 'code');
        $code->addValidator('stringLength', false, array(1, 255))
             ->addFilter('StringToLower')
             ->addFilter('Alnum')
             ->setLabel('Code')
             ->setRequired(true)
             ->setDescription('A code to reference this attribute set.')
        ;

        // Description
        $description = $this->createElement('textarea', 'description');
        $description->setLabel('Description')
                    ->setDescription('Description of what this attribute set defines.');
        $description->rows = 3;
        $description->cols = 58;

        // Title - an encoded string for showing titles
        $title = $this->createElement('text', 'title');
        $title->setLabel('Title')
                 ->setDescription("The title for each item.  Use {code} for dynamic fields.  You may use reference fields.  See the help section 'Attribute Set Titles'.");
        $title->size = 55;

        // Theme
        $theme = $this->createElement('select', 'ref_theme');
        $theme->setRequired(true)
                 ->setLabel('Theme')
                 ->setOptions(array(
                     'id' => 'theme'
                 ))
                 ->setDescription('Which theme should this use?');

        // # of tabs of data
        $tabs = $this->createElement('text', 'tabs');
        $tabs->size = 10;
        $tabs->addValidator('Int')
             ->addValidator('GreaterThan', false, array('min' => 0, 'messages' => array(Validate\GreaterThan::NOT_GREATER => 'Minimum of 1 tab')))
             ->addValidator('LessThan', false, array('max' => 11, 'messages' => array(Validate\LessThan::NOT_LESS => 'Maximum of 10 tabs')))
             ->setLabel("# of Tabs")
             ->setRequired(true)
             ->setDescription("How many tabs will be available for data?  Minimum 1");


        // Create submit button
        $submit = $this->createElement('submit', 'save');
        $submit->setLabel("Save Attribute Set");


        // Add elements to form:
        $this->addElement($name)
             ->addElement($code)
             ->addElement($description)
             ->addElement($title)
             ->addElement($theme)
             ->addElement($tabs)
             ->addElement($submit);
    }
}
