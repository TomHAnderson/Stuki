<?php
namespace DefaultRenderers;

use Zend\EventManager\Event,
    Zend\EventManager\EventCollection,
    Zend\EventManager\StaticEventCollection,
    Zend\EventManager\StaticEventManager,
    Zend\Module\Consumer\AutoloaderProvider,
    Zend\Module\Manager
    ;

class Module implements AutoloaderProvider
{
    protected $appListeners    = array();
    protected $staticListeners = array();
    protected $viewListener;

    public function init(Manager $moduleManager)
    {
    }

    protected function initAutoloader($env = null)
    {
//        require __DIR__ . '/autoload_register.php';
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
                    'DefaultRenderers' => __DIR__ . '/src/'
                )
            ),
            'Zend\Loader\ClassMapAutoloader' => array(
                array(
                    'Stuki/Module' => __DIR__ . '/Module.php'
                )
            )
        );
    }
}
