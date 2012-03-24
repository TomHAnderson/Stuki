<?php
/**
 * This form and the edit counterpart are special.
 * They build a dynamic form based on a attribute_set definition
 */
namespace Stuki\Form\Entities;

use Stuki\Form,
    Zend\Validator as Validate,
    Stuki\Renderer\Parameters;

class Insert extends Form
{
    function __construct($attributeSet) {
        parent::__construct(null); # normally options are passed to the constructor

        // Build dynamic fields
        foreach ($attributeSet->getAttributes() as $a) {
            // Field names must be prefixed because field names cannot be integers
            $element = $a->getRenderer()->getClassObject('eav_' . $a->getKey());

            // Set renderer parameters
            if ($element instanceof Parameters)
                $element->setParameters($a->getParameters());

            $element->setLabel($a->getLabel())
                    ->setDescription($a->getDescription())
                    ->setRequired($a->getIsRequired());
            $element->class = $element->getDataType();

            // Check regex
            if ($a->getRegex()) {
                $element->addValidator('regex', false, array($a->getRegex()));
            }

            // Check custom error
            if ($a->getError()) {
                $element->addErrorMessage($a->getError());
            }

            // Check for select options
            // An attribute may have options even if it's not a select element
            if (method_exists($element, 'setRegisterInArrayValidator')) {
                $element->setRegisterInArrayValidator(false);
            }
            if (method_exists($element, 'addMultiOptions')) {
                $element->addMultiOptions($element->getRendererMultiOptions($a));
            }

            // If an element is a multi-select rename to array
            if ($element instanceof \Zend\Form\Element\Multiselect) {
                $element->setName($element->getName() . '[]');
            }

            // Check for uniqueness
            if ($a->getIsUnique()) {
                # todo
            }

            // Custom prep function
            $element->postCreate();
            $this->addElement($element);
        }
/*
                // Check for uniqueness
                if ($def['is_unique']) {
                    $exclude = "ref_attribute = " . $db->quote($def['attribute_key']);

                    $element->addValidator('db_NoRecordExists', false, array(
                        'adapter' => Zend_Registry::get('db'),
                        'table' => 'items_' . $renderer['type'],
                        'field' => 'value',
                        'exclude' => $exclude
                    ));
                }
*/


        // Create submit button
        $submit = $this->createElement('submit', 'save');
        $submit->setLabel("Save");

        // Add elements to form:
        $this->addElement($submit);

    }
}
