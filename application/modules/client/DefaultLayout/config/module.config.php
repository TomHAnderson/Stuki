<?php

return array(
//     'bootstrap_class' => 'Stuki\Bootstrap',
    'layout' => 'layout/layout.phtml',
    'di' => array(

        'instance' => array(
            'alias' => array(
                'view' => 'Zend\View\PhpRenderer',
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
            'Zend\View\PhpRenderer' => array(
                'parameters' => array(
                    'resolver' => 'Zend\View\TemplatePathStack',
                    'options' => array(
                        'script_paths' => array(
                            'DefaultLayout' => __DIR__ . '/../views',
                        ),
                    ),
                    'broker' => 'Zend\View\HelperBroker',
                ),
            ),
        ),
    ),
);