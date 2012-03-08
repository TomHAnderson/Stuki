<?php

return array(
    'gdocs' => array(
        'username' => 'tom.h.anderson',
        'password' => 'sphix142',
    ),
    'di' => array(
        'instance' => array(
            'alias' => array(
                // Controllers
                'googledocs' => 'GoogleDocs\Controller\IndexController',
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