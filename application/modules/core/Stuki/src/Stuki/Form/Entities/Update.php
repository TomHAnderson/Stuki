<?php
/**
 * This form and the edit counterpart are special.
 * They build a dynamic form based on a attribute_set definition
 */
namespace Stuki\Form\Entities;

use Stuki\Renderer\Parameters;

class Update extends \Stuki\Form {

    function __construct(\Entities\Entities $entity) {
        parent::__construct(null); # normally options are passed to the constructor

        // Build dynamic fields
        foreach ($entity->getAttributeSet()->getAttributes() as $a) {

            $element = $a->getRenderer()->getClassObject('eav_' . $a->getKey());

            // Set renderer parameters
            if ($element instanceof Parameters)
                $element->setParameters($a->getParameters());

            // Configure element
            $element->setLabel($a->getLabel())
                    ->setDescription($a->getDescription())
                    ->setRequired($a->getIsRequired());
            $element->class = $element->getDatatype();

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
            if ($element instanceof Zend_Form_Element_Multiselect) {
                $element->setName($element->getName() . '[]');
            }

            // Check for uniqueness
            # FIXME:
            if ($a->getIsUnique()) {

            }

            // Custom prep function
            $element->postCreate();
            $this->addElement($element);
        }

        // Create submit button
        $submit = $this->createElement('submit', 'save');
        $submit->setLabel("Save");

        // Add elements to form:
        $this->addElement($submit);

    }
}
