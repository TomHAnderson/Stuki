<?php

return array(
    'di' => array(

        'instance' => array(
            'alias' => array(
                // Controllers
                'favorites' => 'Favorites\Controller\IndexController',

                // Models
                'modelFavorites' => 'Favorites\Model\Favorites',
            ),

            'Zend\View\PhpRenderer' => array(
                'parameters' => array(
                    'resolver' => 'Zend\View\TemplatePathStack',
                    'options' => array(
                        'script_paths' => array(
                            'favorites' => __DIR__ . '/../views',
                        ),
                    ),
                    'broker' => 'Zend\View\HelperBroker',
                ),
            ),

/**
 * FIXME: Need to find the right way to add entities
 */
/*
            'orm_driver_chain' => array(
                'parameters' => array(
                    'drivers' => array(
                        'attachments_annotation_driver' => array(
                            'class'     => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                            'namespace' => 'Entities',
                            'paths'     => array(
                                APPLICATION_PATH . '/plugins/Attachments/src/Attachments/Entity'
                            )
                        )
                    )
                )
            ),
*/
        ),
    ),
);