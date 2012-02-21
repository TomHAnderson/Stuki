<?

namespace Entities;

use Stuki\Entity\Entity,
    Doctrine\ORM\Mapping AS ORM;

/**
 *
 * @ORM\Entity
 * @ORM\Table(name="audit_values_varchar", indexes={@ORM\index(name="value_key_idx", columns={"value_key"})})
 */
class AuditValuesVarchar extends Entity
{

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    protected $audit_key;

    /**
     * @ORM\Column(type="integer")
     */
    protected $value_key;

    /**
     * @ORM\ManyToOne(targetEntity="AuditEntities")
     * @ORM\JoinColumn(name="ref_entity", referencedColumnName="entity_key")
     */
    protected $entity;

    /**
     * @ORM\ManyToOne(targetEntity="AuditAttributes")
     * @ORM\JoinColumn(name="ref_attribute", referencedColumnName="attribute_key")
     */
    protected $attributeSet;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $value;

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
     * @ORM\Column(name="audit_ref_user", type="integer")
     */
    protected $auditUser;

}