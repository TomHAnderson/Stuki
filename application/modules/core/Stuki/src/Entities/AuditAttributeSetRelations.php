<?
/**
 * These are plugins associated with an attribute set.  So these plugins are displayed/ran
 * when an associated entity of this attribute set type is displayed.
 */
namespace Entities;

use Stuki\Entity\Entity,
    Stuki\Entity\Audit,
    Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="audit_attribute_set_relations_xref", indexes={@ORM\index(name="attribute_set_plugins_idx", columns={"audit_key", "ref_attribute_set_parent", "ref_attribute_set_child"})}))
 */
class AuditAttributeSetRelations extends Entity implements Audit
{
    public function getGetterSetterMap() {
        return array (
            'getParent' => 'setParent',
            'getChild' => 'setChild'
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
     * @ORM\ManyToOne(targetEntity="AuditAttributeSets", inversedBy="relations")
     * @ORM\JoinColumn(name="ref_attribute_set_parent", referencedColumnName="attribute_set_key")
     */
    protected $parent;

    public function getParent() {
        return $this->parent;
    }

    public function setParent(\Entities\AttributeSets $value) {
        $this->parent = $value;
    }

    /**
     * @ORM\ManyToOne(targetEntity="AuditAttributeSets", inversedBy="inverseRelations")
     * @ORM\JoinColumn(name="ref_attribute_set_child", referencedColumnName="attribute_set_key")
     */
    protected $child;

    public function getChild() {
        return $this->child;
    }

    public function setChild(\Entities\AttributeSets $value) {
        $this->child = $value;
    }
}