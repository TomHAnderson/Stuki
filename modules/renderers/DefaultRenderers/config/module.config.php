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

            'Zend\View\Renderer\PhpRenderer' => array(
                'parameters' => array(
                    'resolver' => 'Zend\View\Resolver\AggregateResolver',
                    'options' => array(
                        'script_paths' => array(
                            'stuql' => __DIR__ . '/../views',
                        ),
                    ),
                    'broker' => 'Zend\View\HelperBroker',
                ),
            ),

        ),
    ),
);