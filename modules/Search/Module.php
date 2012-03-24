<?php
namespace Search;

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
#        $events->attach('bootstrap', 'bootstrap', array($this, 'initializeView'), 100);

        // Add search listeners
        $events->attach('Stuki\Model\Entities', 'insert', array($this, 'indexSearch'));
        $events->attach('Stuki\Model\Entities', 'update', array($this, 'indexSearch'));
        $events->attach('Stuki\Model\Entities', 'delete', array($this, 'deleteSearch'));
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
     * This handles adding entities to the search index
     */
    public function indexSearch($event) {
        $search = $event->getTarget()->getLocator()->get('modelSearchIndex');
        $search->index($event->getParam('entity'));
    }

    public function deleteSearch($event) {
        $search = $event->getTarget()->getLocator()->get('modelSearchIndex');
        $search->delete($event->getParam('entity'));
    }

    /**
     * Return a configuration for an autoloader to cover the
     * classes used in this module
     */
    public function getAutoloaderConfig() {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    'Search' => __DIR__ . '/src/Search',
                )
            ),
            'Zend\Loader\ClassMapAutoloader' => array(
                array(
                    'Stuki/Module' => __DIR__ . '/Module.php'
                )
            )
        );
    }



    public function initializeView($e)
    {
        $app          = $e->getParam('application');
        $locator      = $app->getLocator();
        $config       = $e->getParam('config');
        $view         = $this->getView($app);
        $viewListener = $this->getViewListener($view, $config);
        $app->events()->attachAggregate($viewListener);
        $events       = StaticEventManager::getInstance();
        //      $viewListener->registerStaticListeners($events, $locator);
    }

    protected function getViewListener($view, $config)
    {
        if ($this->viewListener instanceof \Stuki\View\Listener) {
            return $this->viewListener;
        }

        $viewListener       = new \Stuki\View\Listener($view, $config->layout);
        $viewListener->setDisplayExceptionsFlag($config->display_exceptions);

        $this->viewListener = $viewListener;
        return $viewListener;
    }

    protected function getView($app)
    {
        if (isset($this->view) && $this->view) {
            return $this->view;
        }

        $di     = $app->getLocator();
        $view   = $di->get('view');
        //      $url    = $view->plugin('url');
        //      $url->setRouter($app->getRouter());
        //      $view->plugin('headTitle')->setSeparator(' - ')
        //      ->setAutoEscape(false)
        //      ->append('ZF2 Skeleton Application');
        //      $basePath = $app->getRequest()->detectBaseUrl();
        //      $view->plugin('headLink')->appendStylesheet($basePath . 'css/bootstrap.min.css');
        //      $html5js = '<script src="' . $basePath . 'js/html5.js"></script>';
        //      $view->plugin('placeHolder')->__invoke('html5js')->set($html5js);
        //      $favicon = '<link rel="shortcut icon" href="' . $basePath . 'images/favicon.ico">';
        //      $view->plugin('placeHolder')->__invoke('favicon')->set($favicon);

        $this->view = $view;
        return $view;
    }


}
