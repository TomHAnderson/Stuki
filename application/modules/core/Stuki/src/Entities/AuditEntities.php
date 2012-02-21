<?

namespace Entities;

use Stuki\Entity\Entity,
    Doctrine\ORM\Mapping AS ORM;

/**
  * @ORM\Entity
  * @ORM\Table(name="audit_entities", indexes={@ORM\index(name="entity_key_idx", columns={"entity_key"})})
  */

class AuditEntities extends Entity
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
    protected $entity_key;

    /**
     * @ORM\ManyToOne(targetEntity="AuditUsers")
     * @ORM\JoinColumn(name="ref_user", referencedColumnName="user_key")
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="AuditAttributeSets")
     * @ORM\JoinColumn(name="ref_attribute_set", referencedColumnName="attribute_set_key")
     */
    protected $attributeSet;

    /**
     * @ORM\ManyToOne(targetEntity="AuditEntities")
     * @ORM\JoinColumn(name="ref_entity", referencedColumnName="entity_key")
     */
    protected $parent;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $title;

    /**
     * @ORM\Column(name="is_locked", type="boolean", nullable=true)
     */
    protected $isLocked;

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