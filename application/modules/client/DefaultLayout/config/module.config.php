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
                        'defaultlayout/layout' => __DIR__ . '/../view/layout/layout.phtml',
                        'defaultlayout/ate' => __DIR__ . '/../view/layout/ate.phtml',
                        'defaultlayout/different' => __DIR__ . '/../view/layout/different.phtml',
                        'defaultlayout/footer_one' => __DIR__ . '/../view/layout/footer_one.phtml',
                        'defaultlayout/another' => __DIR__ . '/../view/layouts/another.phtml',
                    ),
                ),
            ),
            'Zend\View\Resolver\TemplatePathStack' => array(
                'parameters' => array(
                    'paths'  => array(
                        'defaultlayout' => __DIR__ . '/../view',
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
                     'layoutTemplate' => 'defaultlayout/layout',
                 ),
             ),

        ),
    ),
);