<?php

return array(
    'display_exceptions' => true,
    'layout' => 'layouts/layout.phtml',

    'di' => array(
        'instance' => array(
            'alias' => array(
                // Controllers
                'index' => 'Stuki\Controller\IndexController',
                'error' => 'Stuki\Controller\ErrorController',
                'attributesets' => 'Stuki\Controller\AttributeSetsController',
                'attributes' => 'Stuki\Controller\AttributesController',
                'renderers' => 'Stuki\Controller\RenderersController',
                'entities' => 'Stuki\Controller\EntitiesController',
                'plugins' => 'Stuki\Controller\PluginsController',
                'themes' => 'Stuki\Controller\ThemesController',

                // Models
                'modelAuthentication' => 'Stuki\Model\Authentication',
                'modelAttributeSets' => 'Stuki\Model\AttributeSets',
                'modelAttributes' => 'Stuki\Model\Attributes',
                'modelRenderers' => 'Stuki\Model\Renderers',
                'modelEntities' => 'Stuki\Model\Entities',
                'modelPlugins' => 'Stuki\Model\Plugins',
                'modelThemes' => 'Stuki\Model\Themes',
                'modelQueue' => 'Stuki\Model\Queue',

                'view' => 'Zend\View\PhpRenderer',

            ),

            'modelQueue' => array(
                'parameters' => array(
                    'entities' => 'modelEntities',
                    'searchIndex' => 'modelSearchIndex',
                    'att' => 'modelAttributeSets',
                )
            ),

            'Stuki\Model' => array(
                'parameters' => array(
                    'em' => 'doctrine_em'
                )
            ),

            // Inject the plugin broker for controller plugins into
            // the action controller for use by all controllers that
            // extend it.
            'Zend\Mvc\Controller\ActionController' => array(
                'parameters' => array(
                    'broker' => 'Zend\Mvc\Controller\PluginBroker',
                ),
            ),

            'Zend\Mvc\Controller\PluginBroker' => array(
                'parameters' => array(
                    'loader' => 'Zend\Mvc\Controller\PluginLoader',
                ),
            ),

            'Zend\View\PhpRenderer' => array(
                'parameters' => array(
                    'resolver' => 'Zend\View\TemplatePathStack',
                    'options' => array(
                        'script_paths' => array(
                            'stuki' => __DIR__ . '/../views',
                        ),
                    ),
                ),
            ),
        ),
    ),

    'routes' => array(
        'default' => array(
            'type' => 'Zend\Mvc\Router\Http\Segment',
            'options' => array(
                'route' => '/[:controller[/:action]]',
                'constraints' => array(
                    'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                ),
                'defaults' => array(
                    'controller' => 'index',
                    'action' => 'index',
                ),
            ),
        ),
        'home' => array(
            'type' => 'Zend\Mvc\Router\Http\Literal',
            'options' => array(
                'route' => '/',
                'defaults' => array(
                    'controller' => 'index',
                    'action' => 'index',
                ),
            ),
        ),
    )
);