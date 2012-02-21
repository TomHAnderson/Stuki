<?php
/**
 * This model works in conjunction with entity events
 * to audit whole classes
 */

namespace Stuki\Model;

use Stuki\Model\Model as StukiModel;

class Audit extends StukiModel {

    /**
     * Divine the class name for the audit entity
     */
    public function getClassName($entity) {
        $table = substr(strrchr($entity, '\\'), 1);
        $namespace = substr($entity, 0, strpos($entity, '\\' . $table));

        return new \Stuki\Model\Result(true, $namespace . '\\Audit' . $table);
    }

}