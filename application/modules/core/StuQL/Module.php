<?php
namespace StuQL;

use InvalidArgumentException,
    Zend\Config\Config,
    Zend\Di\Locator,
    Zend\EventManager\Event,
    Zend\EventManager\EventCollection,
    Zend\EventManager\StaticEventCollection,
    Zend\EventManager\StaticEventManager,
    Zend\Module\Consumer\AutoloaderProvider,
    Zend\Module\Manager,
    Zend\Loader\StandardAutoloader as StandardAutoloader
    ;

class Module implements AutoloaderProvider
{
    protected $appListeners    = array();
    protected $staticListeners = array();
    protected $viewListener;

    public function init(Manager $moduleManager)
    {
        // Add default Stuki listeners
        $events = StaticEventManager::getInstance();
#        $events->attach('Zend\Mvc\Application', 'route', array($this, 'testParser'));

        // Add search listeners
    }

    public function testParser($e) {
        $stuql = $e->getTarget()->getLocator()->get('modelStuQL');

        $res = $stuql->query("SELECT root.* FROM root");

        print_r($res);
        die();
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
                    'StuQL' => __DIR__ . '/src/StuQL',
                    'PHPSQLParser' => __DIR__ . '/vendor/PHPSQLParser',
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
