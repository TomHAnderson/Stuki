<?php

return array(
    'di' => array(

        'instance' => array(
            'alias' => array(
                // Controllers
                'stuql' => 'StuQL\Controller\IndexController',

                'modelStuQL' => 'StuQL\Model\StuQL',
            ),

            'Zend\View\HelperBroker' => array(
                'parameters' => array(
                    'loader' => 'Zend\View\HelperLoader',
                ),
            ),

            'Zend\View\PhpRenderer' => array(
                'parameters' => array(
                    'resolver' => 'Zend\View\TemplatePathStack',
                    'options' => array(
                        'script_paths' => array(
                            'stuql' => __DIR__ . '/../views',
                        ),
                    ),
                    'broker' => 'Zend\View\HelperBroker',
                ),
            ),


            /*
            'Stuki\Model' => array(
                'parameters' => array(
                    'em' => 'doctrine_em'
                )
            ),
*/
        ),
    ),
);