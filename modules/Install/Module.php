<?php
namespace Install;

use Zend\Module\Consumer\AutoloaderProvider,
    Zend\Module\Manager as ModuleManager,
    Zend\EventManager\StaticEventManager,
    Stuki\Version,
    Doctrine\ORM\Tools\SchemaTool
    ;

class Module
{
    public function init(ModuleManager $manager) {
        $events = StaticEventManager::getInstance();
        $events->attach('bootstrap', 'bootstrap', array($this, 'checkInstall'));
    }

    public function checkInstall($e) {

        $events = StaticEventManager::getInstance();
        $application = $e->getParam('application');
        $em = $application->getLocator()->get('doctrine_em');

        // Default events for install check
        $events->attach('Zend\Mvc\Application', 'route', array($this, 'checkPaths'));
        $events->attach('Zend\Mvc\Application', 'route', array($this, 'checkIni'));

        $conn = $em->getConnection();
        $sql = "SELECT * FROM user";
        try {

            $stmt = $conn->query($sql);

            // Installed; check for db updates
            $events->attach('Zend\Mvc\Application', 'route', array($this, 'syncronizeDatabase'));

            return; // Stuki is installed

        } catch (\PDOException $error) {
            switch ($error->getCode()) {
                case 1049: // Database does not exist
                    $events->attach('Zend\Mvc\Application', 'route', array($this, 'notifyNoDatabase'));
                    break;
                case '42S02':  // Table or view not found; this is what we expect
                    if ($application->getRequest()->query()->get('install')) {
                        // The last event in this list must stop propogation
                        $events->attach('Zend\Mvc\Application', 'route', array($this, 'buildDatabase'));
                    } else {
                        // Begin installation
                        $events->attach('Zend\Mvc\Application', 'route', array($this, 'showInstall'));
                    }
                    break;
                default:
                    echo '<h1>An unexpected database error has occurred while checking installation</h1>';
                    die($error->getMessage());
            }
        } catch (\Exception $e) {
            die('exception');
        }
    }

    /**
     * FIXME: This is intended as a development tool and will be replaced later on
     */
    public function syncronizeDatabase($e) {
        if (isset($_REQUEST['overrideSync'])) return;

        $em = $e->getTarget()->getLocator()->get('doctrine_em');
        $tool = new \Doctrine\ORM\Tools\SchemaTool($em);

        // update the database
        if (isset($_REQUEST['sync'])) $res = $tool->UpdateSchema($em->getMetadataFactory()->getAllMetadata());

        if ($res = $tool->getUpdateSchemaSql($em->getMetadataFactory()->getAllMetadata())) {
            echo "Database Sync Needed.  Use ?overrideSync=true to bypass.<br><pre>";
            // Show the pending sql statements
            print_r($res);
            echo  "</pre><BR><BR><a href=\"/?sync=true\">Sync</a>";

            $e->stopPropagation();
            return $e->getTarget()->getResponse();
        }
    }

    public function notifyNoDatabase($e) {
        $response = $e->getTarget()->getResponse();
        $response->setContent('<h1>Stuki ' . Version::getVersion() . '</h1>
              The stuki database does not exist.
              Please create the database and try again.
        ');
        $e->stopPropagation();
        return $response;
    }

    /**
     * Begin the installation
     */
    public function showInstall($e) {
        $response = $e->getTarget()->getResponse();
        $response->setContent('<h1>Stuki ' . Version::getVersion() . '</h1>
              Welcome to Stuki.  Stuki thinks it is not installed because it cannot connect to your
              database.
              <br>
              <a href="/?install=1">Begin installation</a>
        ');

        $e->stopPropagation();
        return $response;
    }

    /**
     * Check ini setttings
     */
    public function checkIni($e) {
        $response = $e->getTarget()->getResponse();
        $return = '';
        $errors = array();

        //
        try {
            $date = new \Datetime();
        } catch (\Exception $exception) {

            $errors = true;
        }

        $return .= '
            Your default timezone is not set in your php ini settings.<br>
            You must fix this error to continue.
        ';

        if ($errors) {
            $response->setContent($return);
            $e->stopPropagation();
            return $response;
        }

        return true;
    }

    public function checkPaths($e) {
        $response = $e->getTarget()->getResponse();
        $return = '';
        $errors = array();

        // Check directories
        $checkDirs = array(
            'Search' => APPLICATION_PATH . '/data/Search/',
            'Cache' => APPLICATION_PATH . '/data/Cache/',
            'Doctrine Proxies' => APPLICATION_PATH . '/data/DoctrineORMModule/'
        );
        $return .= '<style>span.fail {color: red;}</style>';
        $return .= "<h1>Stuki Installation Step 1 - Paths</h1><pre>";

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
            $response->setContent($return);
            $e->stopPropagation();
            return $response;
        }

        return true;
    }

    /**
     * Create the database
     * This is the last step so always stop propagation
     */
    public function buildDatabase($e) {
        $response = $e->getTarget()->getResponse();

        // Build the database
        try {
            $em = $e->getTarget()->getLocator()->get('doctrine_em');
            $tool = new SchemaTool($em);
            $res = $tool->createSchema($em->getMetadataFactory()->getAllMetadata());

            // Set Defaults
            $this->setDefaults($e);

            // Create administrator user
            $password = $this->generatePassword(8);

/*
            $user = new \ZfcUserDoctrineORM\Entity\User;
            $user->setName('Administrator');
            $user->setUsername('administrator');
            $user->setEmail('root@localhost');
            $user->setPassword($password);
            $user->setRegisteredAt(new \Datetime());

            $em->persist($user);
            $em->flush();
*/
#User administrator has password $password<BR><BR>
            $response->setContent("
            <a href=\"/\">Start using Stuki</a>
            ");

            $e->stopPropagation();
            return $response;
        } catch (\Exception $error) {
            print_r($error->getMessage());
        }

//        return $response;
    }

    public function setDefaults($e) {
        $renderers = $e->getTarget()->getLocator()->get('modelRenderers');
        $themes = $e->getTarget()->getLocator()->get('modelThemes');
        $plugins = $e->getTarget()->getLocator()->get('modelPlugins');

        $renderers->insert('Date', 'DefaultRenderers\Date');
        $renderers->insert('Decimal', 'DefaultRenderers\Decimal');
        $renderers->insert('Integer', 'DefaultRenderers\Integer');
        $renderers->insert('String', 'DefaultRenderers\String');
        $renderers->insert('Text', 'DefaultRenderers\Text');
        $renderers->insert('Select', 'DefaultRenderers\Select');
        $renderers->insert('EntitySelectList', 'DefaultRenderers\EntitySelectList');
        $renderers->insert('HTML', 'Html\Renderer');

        $themes->insert('Default', 'entities/view.phtml');

        $plugins->insert('Attachments', 'Attachments\Attachments');
        $plugins->insert('Favorites', 'Favorites\Favorites');
    }

    private function generatePassword($length) {
        $i = 0;
        $pass = '' ;

        $chars = "abcdefghijkmnopqrstuvwxyz023456789";
        srand((double)microtime()*1000000);

        while ($i < $length) {
            $pass .= substr($chars, rand() % 33, 1);
            $i++;
        }

        return $pass;
    }
}
