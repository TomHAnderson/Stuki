<?php

return array(
    'di' => array(

        'instance' => array(
            'alias' => array(
                // Controllers
                'search' => 'Search\Controller\SearchController',

                'modelSearch' => 'Search\Model\Search',
                'modelSearchIndex' => 'Search\Model\Index',
            ),

            'Zend\View\HelperBroker' => array(
                'parameters' => array(
                    'loader' => 'Zend\View\HelperLoader',
                ),
            ),

            'Zend\View\Renderer\PhpRenderer' => array(
                'parameters' => array(
                    'resolver' => 'Zend\View\Resolver\AggregateResolver',
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