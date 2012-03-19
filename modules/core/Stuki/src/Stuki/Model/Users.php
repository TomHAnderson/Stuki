<?php
/**
 * This model is used for finding the current version and
 * checking a plugin's version against the application
 */

namespace Stuki\Model;

use Stuki\Model\Model as StukiModel;

class Users extends StukiModel {

/*
 * Audit testing code
    public function testing() {
        // testing
        $a = new \Entities\Attributes();
        $a->renderer = $this->em->getRepository('Entities\Renderers')->find(6);
        $this->em->persist($a);
        $this->em->flush();

        // update
        $a->renderer = $this->em->getRepository('Entities\Renderers')->find(7);
        $this->em->flush();

        // delete
        $this->em->remove($a);
        $this->em->flush();

        die('lifecycle');

        // /testing
    }
*/

    // Find the current user
    public function fetchCurrentUser() {
        $plugins = \Stuki\Model\Broker::get('Plugins');
        $p = $plugins->fetchGroup('Authentication');

        foreach ((array)$p as $auth) {
            if ($user_key = $auth->fetchCurrentUser()) return new \Stuki\Model\Result(true, $user_key);
        }

        return new \Stuki\Model\Result(true, 1); #Default user to #1
    }

    public function find($user_key) {
        return new \Stuki\Model\Result(true, $this->em->getRepository('Entities\Users')->find($user_key));
    }
    public function findOneBy($params) {
        return new \Stuki\Model\Result(true, $this->em->getRepository('Entities\Users')->findOneBy($params));
    }


    public function insert($values) {
        $user = new \ZfcUserDoctrineORM\Entity\User;
        foreach ($values as $key => $val) {
            $user->$key = $val;
        }
        $this->em->persist($user);
        $this->em->flush();

        return new \Stuki\Model\Result(true, $user);
    }

    public function delete($key) {
        if ($record = $this->wrapper->find($key)) {
            $this->em->remove($record);
            $this->em->flush();
        }

        return new \Stuki\Model\Result(true, true);
    }

}