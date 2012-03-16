<?
/**
 * Add a new attribute to an attribute set
 */

namespace Stuki\Form\Attributes;

use Stuki\Form,
    Zend\Validator as Validate;

class Insert extends Form
{
    function init()
    {

        // Label
        $label = $this->createElement('text', 'label');
        $label->addValidator('stringLength', false, array(1, 255))
              ->setRequired(true)
              ->setLabel('Label')
              ->setDescription('The label for this attribute');
        $label->size = 60;

        // Code
        $code = $this->createElement('text', 'code');
        $code->addValidator('stringLength', false, array(1, 255))
             ->addFilter('StringToLower')
             ->addFilter('Alnum')
             ->setLabel('Code')
             ->setRequired(true)
             ->setDescription('A code to reference this attribute.')
        ;


        // Type of handler to use when displaying and editing this code
        $renderer = $this->createElement('select', 'ref_renderer');
        $renderer->setRequired(true)
                 ->setLabel('Renderer')
                 ->setOptions(array(
                     'onChange' => 'return showRenderer()',
                     'id' => 'renderer'
                 ))
                 ->setDescription('What kind of form field should this be?');

        // Desc
        $description = $this->createElement('textarea', 'description');
        $description->setLabel('Description')
                    ->setDescription('Description for this attribute.');
        $description->rows = 1;
        $description->cols = 58;


        // Regex filter for input
        $regex = $this->createElement('text', 'regex');
        $regex->addValidator('stringLength', false, array(1, 255))
              ->setLabel('Regex')
              ->setDescription('The regex filter for input (e.g. /[star]{4} ever$/');

        // Regex error
        $error = $this->createElement('text', 'error');
        $error->addValidator('stringLength', false, array(1, 255))
              ->setLabel('Custom Error Message')
              ->setDescription('What error should the user see?');
        $error->size = 55;

        // Options for renderer Select List
        $options = $this->createElement('textarea', 'options');
        $options->setLabel('Options for Select Lists')
                ->setDescription('One option per line.');
#                ->setOptions(array('disabled' => true));
        $options->rows = 1;
        $options->cols = 40;

        // Boolean values
        $is_required = $this->createElement('checkbox', 'isRequired');
        $is_required->setLabel("Required?")
                ->setDescription("Is this attribute required?");

        $is_unique = $this->createElement('checkbox', 'isUnique');
        $is_unique->setLabel("Unique?")
                ->setDescription("Are values for this attribute unique?");

        // Should this be included in a summary table?
        $is_included_in_summary = $this->createElement('checkbox', 'isIncludedInSummary');
        $is_included_in_summary->setLabel("Include in summary?")
                ->setDescription("Should this attribute be included in entity summary tables?");

        // Create submit button
        $submit = $this->createElement('submit', 'save');
        $submit->setLabel("Save Attribute");


        // Add elements to form:
        $this
             ->addElement($label)
             ->addElement($code)
             ->addElement($description)
             ->addElement($renderer)
             ->addElement($regex)
             ->addElement($error)
             ->addElement($options)
             ->addElement($is_required)
             ->addElement($is_unique)
             ->addElement($is_included_in_summary)
             ->addElement($submit)
             ;
    }
}
