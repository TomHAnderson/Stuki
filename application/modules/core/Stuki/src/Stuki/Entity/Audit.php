<?
/**
 * This is here so audit entities can have a common ancestor
 */

namespace Stuki\Entity;

use Stuki\Entity\Exception;

interface Audit
{
    /**
     * This function returns an associative array of getter and setter
     * function names for the entity
     */
    public function getGetterSetterMap();
}
