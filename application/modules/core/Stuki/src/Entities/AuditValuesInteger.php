<?
/**
 * This is one of many EAV values tables.  These hold the actual data in a column.
 */

namespace Entities;

use Stuki\Entity\Entity,
    Stuki\Entity\Value,
    Stuki\Entity\Audit,
    Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="audit_values_integer", indexes={@ORM\index(name="value_key_idx", columns={"value_key"})})
 */
class AuditValuesInteger extends Entity implements Value, Audit
{
    public function getGetterSetterMap() {
        return array (
            'getKey' => 'setKey',
            'getEntity' => 'setEntity',
            'getAttribute' => 'setAttribute',
            'getValue' => 'setValue'
        );
    }

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    protected $audit_key;

    /**
     * @ORM\Column(name="audit_start", type="datetime")
     */
    protected $auditStart;

    /**
     * @ORM\Column(name="audit_start_usec", type="integer")
     */
    protected $auditStartUsec;

    /**
     * @ORM\Column(name="audit_stop", type="datetime")
     */
    protected $auditStop;

    /**
     * @ORM\Column(name="audit_stop_usec", type="integer")
     */
    protected $auditStopUsec;

    /**
     * @ORM\ManyToOne(targetEntity="AuditUsers")
     * @ORM\JoinColumn(name="audit_ref_user", referencedColumnName="user_key")
     */
    protected $auditUser;

    /**
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $value_key;

    public function getKey() {
        return $this->value_key;
    }

    public function setKey($value) {
        $this->value_key = $value;
        return $this;
    }

    /**
     * @ORM\ManyToOne(targetEntity="AuditEntities")
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
     * @ORM\ManyToOne(targetEntity="AuditAttributes")
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
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $value;

    public function getValue() {
        return $this->value;
    }

    public function setValue($value) {
        $this->value = $value;
    }
}