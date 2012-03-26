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

                'view' => 'Zend\View\Renderer\PhpRenderer',

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

            // Setup for controllers.

            // Injecting the plugin broker for controller plugins into
            // the action controller for use by all controllers that
            // extend it
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

            // Setup for router and routes
            'Zend\Mvc\Router\RouteStack' => array(
                'parameters' => array(
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
                                    'controller' => 'Stuki\Controller\IndexController',
                                    'action' => 'index',
                                ),
                            ),
                        ),
                        'home' => array(
                            'type' => 'Zend\Mvc\Router\Http\Literal',
                            'options' => array(
                                'route' => '/',
                                'defaults' => array(
                                    'controller' => 'Stuki\Controller\IndexController',
                                    'action' => 'index',
                                ),
                            ),
                        ),
                    ),
                ),
            ),

            // Setup for the view layer.

            // Using the PhpRenderer, which just handles html produced by php
            // scripts
            'Zend\View\Renderer\PhpRenderer' => array(
                'parameters' => array(
                    'resolver' => 'Zend\View\Resolver\AggregateResolver',
                ),
            ),
            // Defining how the view scripts should be resolved by stacking up
            // a Zend\View\Resolver\TemplateMapResolver and a
            // Zend\View\Resolver\TemplatePathStack
            'Zend\View\Resolver\AggregateResolver' => array(
                'injections' => array(
                    'Zend\View\Resolver\TemplateMapResolver',
                    'Zend\View\Resolver\TemplatePathStack',
                ),
            ),
            // Defining where the layout/layout view should be located
            'Zend\View\Resolver\TemplateMapResolver' => array(
                'parameters' => array(
                    'map' => array(
                        'layout/layout' => __DIR__ . '/../view/layouts/layout.phtml',
                    ),
                ),
            ),
            // Defining where to look for views. This works with multiple paths,
            // very similar to include_path
            'Zend\View\Resolver\TemplatePathStack' => array(
                'parameters' => array(
                    'paths' => array(
                        'application' => __DIR__ . '/../view',
                    ),
                ),
            ),
            // View for the layout
            'Zend\Mvc\View\DefaultRenderingStrategy' => array(
                'parameters' => array(
                    'layoutTemplate' => 'layout/layout',
                ),
            ),
            // Injecting the router into the url helper
            'Zend\View\Helper\Url' => array(
                'parameters' => array(
                    'router' => 'Zend\Mvc\Router\RouteStack',
                ),
            ),
            // Configuration for the doctype helper
            'Zend\View\Helper\Doctype' => array(
                'parameters' => array(
                    'doctype' => 'HTML5',
                ),
            ),
            // View script rendered in case of 404 exception
            'Zend\Mvc\View\RouteNotFoundStrategy' => array(
                'parameters' => array(
                    'displayNotFoundReason' => true,
                    'displayExceptions' => true,
                    'notFoundTemplate' => 'error/404',
                ),
            ),
            // View script rendered in case of other exceptions
            'Zend\Mvc\View\ExceptionStrategy' => array(
                'parameters' => array(
                    'displayExceptions' => true,
                    'exceptionTemplate' => 'error/index',
                ),
            ),
        ),
    ),
);
