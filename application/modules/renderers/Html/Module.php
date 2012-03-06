<?php
namespace Html;

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
        $events = StaticEventManager::getInstance();
        $events->attach('Zend\Mvc\Application', 'route', array($this, 'checkInstall'));
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
                    'Html' => __DIR__ . '/src/Html',
                )
            ),
            'Zend\Loader\ClassMapAutoloader' => array(
                array(
                    'Stuki/Module' => __DIR__ . '/Module.php'
                )
            )
        );
    }

    public function checkInstall($e) {
        $response = $e->getTarget()->getResponse();
        $return = '';
        $errors = array();

        // Check directories
        $checkDirs = array(
            'HTMLPurifier Definition Cache' => APPLICATION_PATH . '/data/HTMLPurifier/',
        );
        $return .= '<style>span.fail {color: red;}</style>';
        $return .= "<h1>Stuki HTML Renderer Installation - Paths</h1><pre>";

        foreach ($checkDirs as $key => $checkDir) {
            $return .= "Testing $key Directory\n";

            // Directory testing from Smarty->testInstall();
            if (!is_dir($checkDir)) {
                $status = false;
                $message = "<span class=\"fail\">FAILED</span>: {$checkDir} is not a directory";
                $return .= $message . ".\n\n";
                $errors['compile_dir'] = $message;
            } elseif (!is_readable($checkDir)) {
                $status = false;
                $message = "<span class=\"fail\">FAILED</span>: {$checkDir} is not readable";
                $return .= $message . ".\n\n";
                $errors['compile_dir'] = $message;
            } elseif (!is_writable($checkDir)) {
                $status = false;
                $message = "<span class=\"fail\">FAILED</span>: {$checkDir} is not writable";
                $return .= $message . ".\n\n";
                $errors['compile_dir'] = $message;
            } else {
                $return .= "{$checkDir} is OK.\n\n";
            }

        }
        $return .= '</pre>
            You must fix these errors to continue.
        ';

        if ($errors) {
            echo $return;
            $e->stopPropagation();
            return $response;
        }

        return true;
    }
}
