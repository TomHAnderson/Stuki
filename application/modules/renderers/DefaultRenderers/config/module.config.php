<?php

return array(
    'di' => array(

        'instance' => array(
            'alias' => array(
                // Controllers
#                'attachments' => 'Attachments\Controller\IndexController',

                // Models
#                'modelAttachments' => 'Attachments\Model\Attachments',
            ),

            'Zend\View\PhpRenderer' => array(
                'parameters' => array(
                    'resolver' => 'Zend\View\TemplatePathStack',
                    'options' => array(
                        'script_paths' => array(
                            'defaultrenderers' => __DIR__ . '/../views',
                        ),
                    ),
                    'broker' => 'Zend\View\HelperBroker',
                ),
            ),
        ),
    ),
);