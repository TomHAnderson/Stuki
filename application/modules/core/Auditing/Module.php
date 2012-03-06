<?php
namespace Auditing;

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

use SimpleThings\EntityAudit\AuditConfiguration;
use SimpleThings\EntityAudit\AuditManager;

class Module implements AutoloaderProvider
{
    protected $appListeners    = array();
    protected $staticListeners = array();
    protected $viewListener;

    public function init(Manager $moduleManager) {
        $events = StaticEventManager::getInstance();
        $events->attach('bootstrap', 'bootstrap', array($this, 'initListeners'), 100);
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
                    'SimpleThings\EntityAudit' => __DIR__ . '/vendor/EntityAudit',
                )
            ),
            'Zend\Loader\ClassMapAutoloader' => array(
                array(
                    'Stuki/Module' => __DIR__ . '/Module.php'
                )
            )
        );
    }

    /**
     * Configure EntityAudit
     */
    public function initListeners($e) {
        $locator = $e->getParam('application')->getLocator();

        $users = $locator->get('modelAuthentication');
        $em = $locator->get('doctrine_em');

        $auditconfig = new AuditConfiguration();
        $auditconfig->setAuditedEntityClasses(array(
            # Default Stuki entities
            'Entities\Attributes',
            'Entities\AttributeSets',
            'Entities\AttributeSetPlugins',
            'Entities\AttributeSetRelations',
            'Entities\Entities',
            'Entities\Plugins',
            'Entities\Renderers',
            'Entities\Themes',
            'Entities\Users',
            'Entities\ValuesArray',
            'Entities\ValuesDatetime',
            'Entities\ValuesDecimal',
            'Entities\ValuesInteger',
            'Entities\ValuesText',
            'Entities\ValuesVarchar',

            # Plugins
            'Entities\Attachments',
            'Entities\Favorites'
        ));

        // Set the current user
        $auditconfig->setCurrentUsername($users->getIdentity());

        $evm = $em->getEventManager();

        $auditManager = new AuditManager($auditconfig);
        $auditManager->registerEvents($evm);
    }

}
