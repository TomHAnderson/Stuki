<?
/**
 * This is the parent class for all entities
 * This defines magic methods because properties must be protected
 * -HasLifecycleCallbacks
 */

namespace Stuki\Entity;

class Entity
{
    public function getEm() {
        return $this->getLocator()->get('doctrine_em');
    }

    public function getLocator() {
        return \Zend\Registry::get('locator');
    }
}
