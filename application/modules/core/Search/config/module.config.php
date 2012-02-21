<?php

return array(
    'di' => array(

        'instance' => array(
            'alias' => array(
                // Controllers
                'search' => 'Search\Controller\SearchController',

                'modelSearch' => 'Search\Model\Search',
                'modelSearchIndex' => 'Search\Model\Index',
                'view' => 'Zend\View\PhpRenderer',
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
                            'search' => __DIR__ . '/../views',
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