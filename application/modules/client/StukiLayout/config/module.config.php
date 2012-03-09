<?php

return array(
//     'bootstrap_class' => 'Stuki\Bootstrap',
    'layout' => 'layout/layout.phtml',
    'di' => array(

        'instance' => array(
            'alias' => array(
            ),

            'Zend\View\Resolver\AggregateResolver' => array(
                'injections' => array(
                    'Zend\View\Resolver\TemplateMapResolver',
                    'Zend\View\Resolver\TemplatePathStack',
                ),
            ),
            'Zend\View\Resolver\TemplateMapResolver' => array(
                'parameters' => array(
                    'map'  => array(
                        'stukilayout/layout' => __DIR__ . '/../view/layout/layout.phtml',
                    ),
                ),
            ),
            'Zend\View\Resolver\TemplatePathStack' => array(
                'parameters' => array(
                    'paths'  => array(
                        'stukilayout' => __DIR__ . '/../view',
                    ),
                ),
            ),
            'Zend\View\Renderer\PhpRenderer' => array(
                'parameters' => array(
                    'resolver' => 'Zend\View\Resolver\AggregateResolver',
                ),
            ),


              // enabling the code below overrides the main application module default path
             'Zend\Mvc\View\DefaultRenderingStrategy' => array(
                 'parameters' => array(
                     'layoutTemplate' => 'stukilayout/layout',
                 ),
             ),

        ),
    ),
);