<?
/**
 * All stuki entity value tables must implement this interface
 */

namespace Stuki\Entity;

interface Value
{

    public function getKey();

    public function getEntity();

    public function setEntity($value);

    public function getAttribute();

    public function setAttribute($value);

    public function getValue();

    public function setValue($value);
}