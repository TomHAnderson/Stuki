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

            'Zend\View\PhpRenderer' => array(
                'parameters' => array(
                    'resolver' => 'Zend\View\TemplatePathStack',
                    'options' => array(
                        'script_paths' => array(
                            'googledocs' => __DIR__ . '/../views',
                        ),
                    ),
                    'broker' => 'Zend\View\HelperBroker',
                ),
            ),
        ),
    ),
);