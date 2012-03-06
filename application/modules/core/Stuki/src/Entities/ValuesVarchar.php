<?
/**
 * This is one of many EAV values tables.  These hold the actual data in a column.
 */

namespace Entities;

use Stuki\Entity\Entity,
    Stuki\Entity\Value,
    Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="values_varchar")
 */
class ValuesVarchar extends Entity implements Value
{

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    protected $value_key;

    public function getKey() {
        return $this->value_key;
    }

    /**
     * @ORM\ManyToOne(targetEntity="Entities")
     * @ORM\JoinColumn(name="ref_entity", referencedColumnName="entity_key")
     */
    protected $entity;

    public function getEntity() {
        return $this->entity;
    }

    public function setEntity($value) {
        $this->entity = $value;
    }

    /**
     * @ORM\ManyToOne(targetEntity="Attributes")
     * @ORM\JoinColumn(name="ref_attribute", referencedColumnName="attribute_key")
     */
    protected $attribute;

    public function getAttribute() {
        return $this->attribute;
    }

    public function setAttribute($value) {
        $this->attribute = $value;
    }

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $value;

    public function getValue() {
        return $this->value;
    }

    public function setValue($value) {
        $this->value = $value;
    }
}