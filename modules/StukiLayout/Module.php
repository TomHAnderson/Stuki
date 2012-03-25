<?php
namespace StukiLayout;

use InvalidArgumentException,
    Zend\Config\Config,
    Zend\Di\Locator,
    Zend\EventManager\Event,
    Zend\EventManager\EventCollection,
    Zend\EventManager\StaticEventCollection,
    Zend\EventManager\StaticEventManager,
    Zend\Module\Consumer\AutoloaderProvider,
    Zend\Module\Manager,
    Zend\Loader\StandardAutoloader as StandardAutoloader,

    Search\Model\Index as SearchIndex
    ;

class Module implements AutoloaderProvider
{
    protected $appListeners    = array();
    protected $staticListeners = array();
    protected $view;
    protected $viewListener;


    public function init(Manager $moduleManager)
    {
        // Add default Stuki listeners
        $events = StaticEventManager::getInstance();

        // Add view listener
#        $events->attach('bootstrap', 'bootstrap', array($this, 'initializeView'), 100);
        $events->attach('Zend\Mvc\Application', 'dispatch', array($this, 'addTabbedForms'));


    }

    public function addTabbedForms($e) {
        $response = $e->getTarget()->getResponse();
        $request = $e->getTarget()->getRequest();
        $locator = $e->getTarget()->getLocator();
        $route = $e->getRouteMatch();

        $controller = $route->getParam('controller');
        $action = $route->getParam('action');

        switch ($controller) {
            case 'attributesets':
                switch ($action) {
                    case 'insert':
                    case 'update':
                        $locator->get('view')->plugin('headScript')->prependFile('/assets/StukiLayout/js/AttributeSets/tabbedform.js');
                        break;
                    default:
                        break;
                }
                break;
            case 'attributes':
                switch ($action) {
                    case 'insert':
                    case 'update':
                        $locator->get('view')->plugin('headScript')->prependFile('/assets/StukiLayout/js/Attributes/tabbedform.js');
                        break;
                    default:
                        break;
                }
            default:
                break;
        }
    }

    public static function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    /**
     * Return a configuration for an autoloader to cover the
     * classes used in this module
     */
    public function getAutoloaderConfig() {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                )
            ),
            'Zend\Loader\ClassMapAutoloader' => array(
                array(
                    'StukiLayout/Module' => __DIR__ . '/Module.php'
                )
            )
        );
    }

}

