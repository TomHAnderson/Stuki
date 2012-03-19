<?php
namespace Favorites\Model;

use Stuki\Model\Model,
    Entities\AttributeSets as EntityAttributeSets,
    Stuki\AttributeSets\Exception as Exception
    ;

class Favorites extends Model {

    /**
     * Retrieve one
     */
    public function find($attachment_key) {
        if (!is_int($attachment_key)) throw new \Stuki\Exception('Favorites key must be an integer');
        return $this->getEm()->getRepository('Entities\Favorites')->find($attachment_key);
    }

    /**
     * Retrieve all
     */
    public function findAll($sort = null) {
#        if (!$sort) $sort = array('entity' => 'asc');
        return $this->getEm()->getRepository('Entities\Favorites')->findBy(array(), $sort);
    }

    /**
     * Retrieve a subset
     */
    public function findBy($search, $sort = null) {
#        if (!$sort) $sort = array('entity' => 'asc');
        return $this->getEm()->getRepository('Entities\Favorites')->findBy($search, $sort);
    }

    /**
     * Retrieve one using an assoc array of params
     */
    public function findOneBy($search) {
        return $this->getEm()->getRepository('Entities\Favorites')->findOneBy($search);
    }


    public function insert (\Entities\Entities $entity, \ZfcUserDoctrineORM\Entity\User $user) {

        $favorite = new \Entities\Favorites();
        $favorite->setEntity($entity);
        $favorite->setUser($user);

        $this->getEm()->persist($favorite);
        $this->getEm()->flush();

        return $favorite;
    }

    public function delete(\Entities\Entities $entity, \ZfcUserDoctrineORM\Entity\User $user) {
        $favorite = $this->findOneBy(array(
            'entity' => $entity,
            'user' => $user
        ));

        $this->getEm()->remove($favorite);
        $this->getEm()->flush();
    }
}
