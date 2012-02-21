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
        # FIXME:  Temporary Registry set until it can be injected properly
        return \Zend\Registry::get('locator');
    }

    /**
     * Audit Insert
     *
     * Called from an entity with
     * -HasLifecycleCallbacks
     * in the entity definition and
     *
     *    /**
     *     * -PostPersist
     *     * /
     *    function insert() {
     *        $this->auditInsert();
     *    }
     *
     * This is called by functions on the entity
     * because doctrine doesn't do inheritance of entities
     */
    public function auditInsert() {
        return true; # return until we're ready to finalize this

        // Is auditing turned on?
        $config = \Zend_Registry::get('config');
        if (!$config['stuki']['useAuditTables']) return;

        $audit = \Stuki\Model\Broker::get('Audit');
        $users = \Stuki\Model\Broker::get('Users');
        $em = \Zend_Registry::get('doctrine')->getEntityManager();

        if (!$class = $audit->getClassName(get_class($this))) {
            throw new Stuki\Exception('Audit table does not exists for ' . get_class($this));
        }

        $start = new $class;

        // Copy this object to the new audit object
        foreach (get_object_vars($this) as $var => $value) {
            $start->$var = $value;
        }

        // Set audit values
        $start->auditStart = new \DateTime();
        $start->auditStartUsec = 0;
        $start->auditStop = new \DateTime('9999-12-31');
        $start->auditStopUsec = 0;
        $start->auditUser = $users->fetchCurrentUser();

        $em->persist($start);
        $em->flush();
    }

    /**
     * Audit Update
     *
     * Called from an entity with
     * -HasLifecycleCallbacks
     * in the entity definition and
     *
     *    /**
     *     * -PostUpdate
     *     * /
     *    function update() {
     *        $this->auditUpdate();
     *    }
     *
     * Notice this function is called in PreRemove and NOT PostRemove
     */
    public function auditUpdate() {
        return true; # return until we're ready to finalize this

        // Is auditing turned on?
        $config = \Zend_Registry::get('config');
        if (!$config['stuki']['useAuditTables']) return;

        $audit = \Stuki\Model\Broker::get('Audit');
        $users = \Stuki\Model\Broker::get('Users');

        $em = \Zend_Registry::get('doctrine')->getEntityManager();

        // Get identifying values
        $query = array('auditStop' => '9999-12-31');
        $query += $em->getClassMetadata(get_class($this))->getIdentifierValues($this);

        // Fetch old audit record
        if (!$auditOld = $em->getRepository($audit->getClassName(get_class($this)))->findOneBy($query)) {
            // If the old audit doesn't exist, create it.  This avoids issues
            // if the audit records become asyncronous.
            $this->auditInsert();
            #throw new \Stuki\Exception('Unable to find old audit record in audit update');
        }

        // Update the old audit record and add a new one
        if (!$class = $audit->getClassName(get_class($this))) {
            throw new Stuki\Exception('Audit table does not exists for ' . get_class($this));
        }
        $stop = new $class;

        // Copy this object to the new audit object
        foreach (get_object_vars($this) as $var => $value) {
            $stop->$var = $value;
        }

        // Set new audit values
        $now = new \DateTime();
        $stop->auditStart = $now;
        $stop->auditStartUsec = $auditOld->audit_key;
        $stop->auditStop = new \DateTime('9999-12-31');
        $stop->auditStopUsec = 0;
        $stop->auditUser = $users->fetchCurrentUser();

        // Update old audit values
        $auditOld->auditStop = $now;
        $auditOld->auditStopUsec = $auditOld->audit_key;

        $em = \Zend_Registry::get('doctrine')->getEntityManager();
        $em->persist($stop);
        $em->flush();

    }

    /**
     * Audit Delete
     *
     * Called from an entity with
     * -HasLifecycleCallbacks
     * in the entity definition and
     *
     *    /**
     *     * -PreRemove
     *     * /
     *    function delete() {
     *        $this->auditDelete();
     *    }
     *
     * Notice: this function is called in PreRemove and NOT PostRemove
     * because the old values are not available in PostRemove
     */
    public function auditDelete() {
        return true; # return until we're ready to finalize this

        // Is auditing turned on?
        $config = \Zend_Registry::get('config');
        if (!$config['stuki']['useAuditTables']) return;

        // When deleting we want to add one final audit record
        // to save current user
        $this->auditUpdate();

        $audit = \Stuki\Model\Broker::get('Audit');
        $em = \Zend_Registry::get('doctrine')->getEntityManager();

        // Get identifying values
        $query = array('auditStop' => '9999-12-31');
        $query += $em->getClassMetadata(get_class($this))->getIdentifierValues($this);

        // Get audit record to delete
        if (!$auditOld = $em->getRepository($audit->getClassName(get_class($this)))->findOneBy(
            $query
        )) {
            // If the old audit doesn't exist, create it.  This avoids issues
            // if the audit records become asyncronous.
            $this->auditInsert();
        }

        $auditOld->auditStop = new \DateTime();
        $auditOld->auditStopUsec = $auditOld->audit_key;

        $em->flush();
    }
}
