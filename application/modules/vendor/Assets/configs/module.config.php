<?php
return array(
    'bootstrap_class' => 'Naf\Bootstrap',

    'di' => array(
        'instance' => array(
            'alias' => array(
                'assets-index' => 'Assets\Controller\IndexController',
            ),
/*
            'Zend\View\HelperLoader' => array(
                'parameters' => array(
                    'map' => array(
                        'asset' => 'Assets\View\Helper\Link',
                    ),
                ),
            ),
*/
        ),
    ),

    'routes' => array(
        'asset' => array(
            'type' => 'Zend\Mvc\Router\Http\Regex',
            'options' => array(
                'regex' => '/assets/(?P<module>[^/]+)/(?P<asset>[^\.].+)',
                'spec' => '/assets/%module%/%asset%',
                'defaults' => array(
                    'controller' => 'assets-index',
                    'action' => 'index',
                ),
            ),
        ),

    ),
);
