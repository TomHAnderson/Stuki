<?php

namespace Many2Many;

use Stuki\Plugin\Plugin as Plugin,
    Stuki\Plugin\Parameters,
    Many2Many\Form\Parameters as ParametersForm
;

class Many2Many implements Plugin, Parameters {

    protected $parameters;

    /**
     * This is the only function from the implementation
     */
    public function run(\Entities\Entities $entity) {
        $locator = \Zend\Registry::get('locator');

        $modelAttachments = $locator->get('modelAttachments');
        $view = $locator->get('view');

        $view->plugin('headScript')->appendFile('/assets/Attachments/js/upload.js');
        $view->plugin('headLink')->appendStylesheet('/assets/Attachments/css/upload.css');

        $view->setVars(
            array(
                'attachments' => $modelAttachments->findBy(
                    array(
                        'entity' => $entity
                    )
                ),
                'entity' => $entity
            )
        );

        return $view->render('attachments/view.phtml');
    }

    public function getParametersForm() {
        return new ParametersForm();
    }

    public function setParameters($parameters) {
        $this->parameters = $parameters;
    }
}