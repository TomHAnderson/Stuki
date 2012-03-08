<?php

return array(
//     'bootstrap_class' => 'Stuki\Bootstrap',
    'layout' => 'layout/layout.phtml',
    'di' => array(

        'instance' => array(
            'alias' => array(
                'view' => 'Zend\View\Renderer\PhpRenderer',
            ),
            'Zend\View\HelperLoader' => array(
                'parameters' => array(
                    'map' => array(
#                       'splitbutton' => 'other\View\Helper\SplitButton',
                    ),
                ),
            ),
            'Zend\View\HelperBroker' => array(
                'parameters' => array(
                    'loader' => 'Zend\View\HelperLoader',
                ),
            ),

            'Zend\View\Renderer\PhpRenderer' => array(
                'parameters' => array(
                    'options' => array(
                        'script_paths' => array(
                            'DefaultLayout' => __DIR__ . '/../views',
                        ),
                    ),
                ),
            ),
        ),
    ),
);