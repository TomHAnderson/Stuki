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

            'Zend\View\Resolver\TemplatePathStack' => array(
                'parameters' => array(
                    'paths' => array(
                        'search' => __DIR__ . '/../views',
                    ),
                ),
            ),

        ),
    ),
);