<?php

namespace Stuki\Model;

use Doctrine\ORM\EntityManager,
    Zend\EventManager\EventCollection as Events,
    Zend\EventManager\EventManager,
    Zend\EventManager\StaticEventManager,
    Zend\Queue\Queue,
    Stuki\Model\Authentication as ModelAuthentication;

abstract class Model {

    private $em;
    private $authentication;
    private $events;

    public function getEm() {
        return $this->getLocator()->get('doctrine_em');
    }

    public function getAuthentication() {
        return $this->getLocator()->get('modelAuthentication');
    }

    public function getLocator() {
        # FIXME:  Temporary Registry set until it can be injected properly
        return \Zend\Registry::get('locator');
    }

    public function getQueue() {
        //return new Queue('Db', \Zend\Registry::get('queueOptions'));
        return new Queue('Null');
    }


    /**
     * Set the event manager to use with this object
     *
     * @param  Events $events
     * @return void
     */
    public function setEventManager(Events $events)
    {
        $this->events = $events;
    }

    /**
     * Retrieve the currently set event manager
     *
     * If none is initialized, an EventManager instance will be created with
     * the contexts of this class, the current class name (if extending this
     * class), and "bootstrap".
     *
     * @return Events
     */
    public function events()
    {
        if (!$this->events instanceof Events) {
            $this->setEventManager(new EventManager(array(
                __CLASS__,
                get_called_class(),
            )));
        }
        return $this->events;
    }
}