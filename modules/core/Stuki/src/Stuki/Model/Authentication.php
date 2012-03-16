<?php

namespace Stuki\Model;

use Stuki\Model\Model as StukiModel,
    ZfcUser\Service\User as ZfcUserService
    ;

class Authentication extends StukiModel {
    /**
     * Return the key for the logged in user
     */
    public function getIdentity() {
        $userService = new ZfcUserService();
        $auth = $userService->getAuthService();

        // If we are not in the user module then check auth
        return $auth->getIdentity();
    }

    public function getEntity() {
        $entity = $this->getEm()->getRepository('Entities\Users')->find($this->getIdentity());

        if (!$entity) throw new \Stuki\Exception('Cannot return an entity when user is not authenticated');

        return $entity;
    }
}

