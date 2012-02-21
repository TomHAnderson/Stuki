<?php
/**
 * Stuki
 *
 * @author tanderson@soliantconsulting.com
 */

function undoMagicQuotes($array, $topLevel=true) {
    $newArray = array();
    foreach($array as $key => $value) {
        if (!$topLevel) {
            $newKey = stripslashes($key);
            if ($newKey!==$key) {
                unset($array[$key]);
            }
            $key = $newKey;
        }
        $newArray[$key] = is_array($value) ? undoMagicQuotes($value, false) : stripslashes($value);
    }
    return $newArray;
}

if (get_magic_quotes_gpc()) {
    $_GET = undoMagicQuotes($_GET);
    $_POST = undoMagicQuotes($_POST);
}

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(__DIR__ . '/../application'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ?: 'production'));

// Ensure library is on the include path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

// Start autoloader
require_once 'Zend/Loader/AutoloaderFactory.php';
Zend\Loader\AutoloaderFactory::factory(array(
    'Zend\Loader\StandardAutoloader' => array()
));

// Load global application config
$appConfig = include APPLICATION_PATH . '/configs/application.config.php';

// Start Module Loader
$listenerOptions = new Zend\Module\Listener\ListenerOptions($appConfig['module_listener_options']);
$defaultListeners = new Zend\Module\Listener\DefaultListenerAggregate($listenerOptions);
$defaultListeners->getConfigListener()->addConfigGlobPath(APPLICATION_PATH . '/configs/autoload/*.config.php');
$defaultListeners->getConfigListener()->addConfigGlobPath(APPLICATION_PATH . '/configs/autoload/' . APPLICATION_ENV . '/*.config.php');

// Module settings
$moduleManager = new Zend\Module\Manager($appConfig['modules']);
$moduleManager->events()->attachAggregate($defaultListeners);
$moduleManager->loadModules();

// Bootstrap and run
#print_r($defaultListeners->getConfigListener()->getMergedConfig()); die();
$bootstrap = new Zend\Mvc\Bootstrap($defaultListeners->getConfigListener()->getMergedConfig());
$application = new Zend\Mvc\Application;
$bootstrap->bootstrap($application);
$application->run()->send();