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
        $events->attach('Zend\Mvc\Application', 'dispatch', array($this, 'initLayout'));

        $events->attach('Stuki\Controller\AttributeSetsController', 'insert', array($this, 'addTabbedForms'));
        $events->attach('Stuki\Controller\AttributeSetsController', 'update', array($this, 'addTabbedForms'));

        $events->attach('Stuki\Controller\AttributesController', 'insert', array($this, 'addTabbedForms'));
        $events->attach('Stuki\Controller\AttributesController', 'update', array($this, 'addTabbedForms'));

        $events->attach('Stuki\Controller\EntitiesController', 'insert', array($this, 'addTabbedForms'));
        $events->attach('Stuki\Controller\EntitiesController', 'update', array($this, 'addTabbedForms'));
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

    public function initLayout($e) {
        $view = $e->getTarget()->getLocator()->get('view');
        $view->plugin('headScript')->prependFile('/assets/StukiLayout/js/jquery-ui.min.js');
        $view->plugin('headScript')->prependFile('/assets/StukiLayout/js/jquery.min.js');
        $view->plugin('headScript')->appendFile('/assets/StukiLayout/js/googleAnalytics.js');
        $view->plugin('headLink')->appendStylesheet('/assets/StukiLayout/css/stuki.css');
        $view->plugin('headLink')->appendStylesheet('/assets/StukiLayout/css/style.css');
//        $view->plugin('headLink')->appendStylesheet('/assets/StukiLayout/css/jquery-ui/themes/base/jquery.ui.all.css');
        $view->plugin('headLink')->appendStylesheet('/assets/StukiLayout/css/jquery-ui/themes/smoothness/jquery.ui.css');
    }

    /**
     * Add javascript to forms which will modify
     * them to be tabbed
     */
    public function addTabbedForms($e) {

        $locator = $e->getTarget()->getLocator();

        switch (get_class($e->getTarget())) {
            case 'Stuki\Controller\AttributeSetsController':
                $locator->get('view')->plugin('headScript')->appendFile('/assets/StukiLayout/js/AttributeSets/tabbedform.js');
                break;
            case 'Stuki\Controller\AttributesController':
                $locator->get('view')->plugin('headScript')->appendFile('/assets/StukiLayout/js/Attributes/tabbedform.js');
                break;
            case 'Stuki\Controller\EntitiesController':
                $attributeSet = $e->getParam('attributeSet');
                $entity = $e->getParam('entity');

                if ($entity) {
                     $key = $entity->getKey();
                    $locator->get('view')->plugin('headScript')->appendFile('/stukilayout/entityupdatejs?entity_key=' . $key);
                } else {
                    if ($attributeSet) $key = $attributeSet->getKey();
                    $locator->get('view')->plugin('headScript')->appendFile('/stukilayout/entityinsertjs?attribute_set_key=' . $key);

                }

                break;
            default:
                break;
        }


        return array();
    }
}

