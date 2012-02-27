<?
/**
 * Renderers are the heart of Stuki EAV.  They are the core component all other
 * EAV entities rely on.
 */
namespace Entities;

use Stuki\Entity\Entity,
    Stuki\Entity\Audit,
    Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="audit_renderers", indexes={@ORM\index(name="renderer_key_idx", columns={"renderer_key"})}, uniqueConstraints={@ORM\UniqueConstraint(name="class_idx", columns={"class", "audit_key"})})
 */
class AuditRenderers extends Entity implements Audit
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
    protected $renderer_key;

    public function getKey() {
        return $this->renderer_key;
    }

    public function setKey($value) {
        $this->renderer_key = $value;
        return $this;
    }

    /**
     * @ORM\Column(name="alias", type="string")
     */
    protected $alias;

    public function getAlias() {
        return $this->alias;
    }

    public function setAlias($value) {
        $this->alias = $value;
    }

    /**
     * @ORM\Column(name="class", type="string")
     */
    protected $class;

    public function getClass() {
        return $this->class;
    }

    public function getClassObject($fieldName) {

        if (!is_string($fieldName))
            throw new \Stuki\Exception('The field name to create a renderer must be a string');

        $renderer = new $this->class($fieldName);

        return $renderer;
    }

    public function setClass($value) {
        $this->class = $value;
    }

    /**
     * @ORM\OneToMany(targetEntity="AuditAttributes", mappedBy="renderer")
     */
    protected $attributes;

    public function getAttributes() {
        return $this->attributes;
    }
}