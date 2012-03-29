<?php

namespace Attachments;

use Stuki\Plugin\Plugin as Plugin;

class Attachments implements Plugin {

    protected $parameters;

    /**
     * This is the only function from the implementation
     */
    public function run(\Entities\AttributeSetPlugins $pluginXref, \Entities\Entities $entity) {
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
}