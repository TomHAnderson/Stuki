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
 * @ORM\Table(name="audit_attribute_set_plugins_xref", indexes={@ORM\index(name="attribute_set_plugins_idx", columns={"audit_key", "ref_attribute_set", "ref_plugin"})}))
 */
class AuditAttributeSetPlugins extends Entity implements Audit
{
    public function getGetterSetterMap() {
        return array (
            'getAttributeSet' => 'setAttributeSet',
            'getPlugin' => 'setPlugin'
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
     * @ORM\ManyToOne(targetEntity="AuditAttributeSets", inversedBy="plugins")
     * @ORM\JoinColumn(name="ref_attribute_set", referencedColumnName="attribute_set_key")
     */
    protected $attributeSet;

    public function getAttributeSet() {
        return $this->attributeSet;
    }

    public function setAttributeSet($value) {
        $this->attributeSet = $value;
    }

    /**
     * @ORM\ManyToOne(targetEntity="AuditPlugins", inversedBy="attributeSets")
     * @ORM\JoinColumn(name="ref_plugin", referencedColumnName="plugin_key")
     */
    protected $plugin;

    public function getPlugin() {
        return $this->plugin;
    }

    public function setPlugin($value) {
        $this->plugin = $value;
    }
}