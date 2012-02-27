<?
/**
 * This entity manages plugin configurations
 * Plugins were used in Stuki 2.0 extensively.  They will probably be transformed with ZF2
 */
namespace Entities;

use Stuki\Entity\Entity,
    Stuki\Entity\Audit,
    Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="audit_plugins", indexes={@ORM\index(name="plugin_key_idx", columns={"plugin_key"})}))
 */
class AuditPlugins extends Entity implements Audit
{
    public function getGetterSetterMap() {
        return array (
            'getKey' => 'setKey',
            'getAlias' => 'setAlias',
            'getClass' => 'setClass'
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
    protected $plugin_key;

    public function getKey() {
        return $this->plugin_key;
    }

    public function setKey($value) {
        $this->plugin_key = $value;
        return $this;
    }

    /**
     * @ORM\Column(type="string")
     */
    protected $alias;

    public function getAlias() {
        return $this->alias;
    }

    public function setAlias($value) {
        $this->alias = $value;
    }

    /**
     * @ORM\Column(type="string")
     */
    protected $class;

    public function getClass() {
        return $this->class;
    }

    public function setClass($value) {
        $this->class = $value;
    }

    /**
     * Return the plugin class
     */
    public function getClassObject() {
        return new $this->class;
    }

    /**
     * @ORM\OneToMany(targetEntity="AuditAttributeSetPlugins", mappedBy="plugin")
     */
    protected $attributeSets;

    public function getAttributeSets() {
        return $this->attributeSets;
    }
}