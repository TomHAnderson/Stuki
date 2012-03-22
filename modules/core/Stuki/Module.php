<?php
namespace Stuki;

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

    ZfcUser\Service\User as ZfcUserService,

    Stuki\Search\Index as SearchIndex
    ;

class Module implements AutoloaderProvider
{
    protected $appListeners    = array();
    protected $staticListeners = array();
    protected $viewListener;
    protected $view;

    public function init(Manager $moduleManager)
    {
        // Add default Stuki listeners
        $events = StaticEventManager::getInstance();

        // Bootstrap the app
#        $events->attach('bootstrap', 'bootstrap', array($this, 'initializeView'), 100);
        $events->attach('bootstrap', 'bootstrap', array($this, 'initializeRegistry'));

        // Add queue listener in lieu of a cron script
        $events->attach('Zend\Mvc\Application', 'finish', array($this, 'processQueue'));

        // Always force login
        $events->attach('Zend\Mvc\Application', 'dispatch', array($this, 'checkSecurity'));
    }

    protected function initAutoloader($env = null)
    {
        require __DIR__ . '/autoload_register.php';
    }

    public static function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    /**
     * A rudimentary auth simply requiring you are logged in to use the site.
     */
    public function checkSecurity($e) {
        $response = $e->getTarget()->getResponse();
        $locator = $e->getTarget()->getLocator();
        $userService = $locator->get('zfcuser_user_service');
        $auth = $userService->getAuthService();

        // If we are not in the user module then check auth
        if (!$e->getTarget() instanceof \ZfcUser\Controller\UserController) {
            if (!$auth->hasIdentity()) {
                $e->getTarget()->plugin('redirect')->toUrl('/user');
                $e->stopPropagation();
                return $response;
            }
        }

        // User is logged in
        return true;
    }

    /**
     * Run the queue
     * This can be replaced with a cron script for live
     */
    public function processQueue($e) {
        $modelQueue = $e->getTarget()->getLocator()->get('modelQueue');
//        $modelQueue->process(25);
    }

    /**
     * Return a configuration for an autoloader to cover the
     * classes used in this module
     */
    public function getAutoloaderConfig() {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    'Stuki' => __DIR__ . '/src/Stuki',
                    'Entities' => __DIR__ . '/src/Entities'
                )
            ),
            'Zend\Loader\ClassMapAutoloader' => array(
                array(
                    'Stuki/Module' => __DIR__ . '/Module.php'
                )
            )
        );
    }

    // Store registry values prn
    public function initializeRegistry($e) {
        $application = $e->getParam('application');
        $locator = $application->getLocator();
        \Zend\Registry::set('locator', $locator);
    }

    public function registerApplicationListeners(EventCollection $events, Locator $locator, Config $config)
    {
        $view          = $locator->get('view');
        $viewListener  = $this->getViewListener($view, $config);
        $events->attachAggregate($viewListener);
    }

    public function registerStaticListeners(StaticEventCollection $events, Locator $locator, Config $config)
    {
        $view         = $locator->get('view');
        $viewListener = $this->getViewListener($view, $config);

        $viewListener->registerStaticListeners($events, $locator);
    }

    protected function getViewListener($view, $config)
    {
        if ($this->viewListener instanceof View\Listener) {
            return $this->viewListener;
        }

        $viewListener = new View\Listener($view, $config->layout);
        $viewListener->setDisplayExceptionsFlag($config->display_exceptions);

        $this->viewListener = $viewListener;
        return $viewListener;
    }

    public function initializeView($e) {
            $app = $e->getParam('application');
            $locator = $app->getLocator();
            $config = $e->getParam('config');
            $view = $this->getView($app);
            $viewListener = $this->getViewListener($view, $config);
            $app->events()->attachAggregate($viewListener);
            $events = StaticEventManager::getInstance();
            $viewListener->registerStaticListeners($events, $locator);
    }

    protected function getView($app) {
        if ($this->view) {
            return $this->view;
        }

        $locator = $app->getLocator();
        $view = $locator->get('view');

        // Set up view helpers
        $view->plugin('url')->setRouter($app->getRouter());
        $view->doctype()->setDoctype('HTML5');

        $basePath = $app->getRequest()->getBaseUrl();
        $view->plugin('basePath')->setBasePath($basePath);

        $this->view = $view;
        return $view;
    }
}
