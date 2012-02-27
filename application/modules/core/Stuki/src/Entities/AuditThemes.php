<?
/**
 */
namespace Entities;

use Stuki\Entity\Entity,
    Stuki\Entity\Audit,
    Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="audit_themes", indexes={@ORM\index(name="theme_key_idx", columns={"theme_key"})}))
 */
class AuditThemes extends Entity implements Audit
{
    public function getGetterSetterMap() {
        return array (
            'getKey' => 'setKey',
            'getAlias' => 'setAlias',
            'getTemplate' => 'setTemplate'
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
    protected $theme_key;

    public function getKey() {
        return $this->theme_key;
    }

    public function setKey($value) {
        $this->theme_key = $value;
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
    protected $template;

    public function getTemplate() {
        return $this->template;
    }

    public function setTemplate($value) {
        $this->template = $value;
    }

    /**
     * @ORM\OneToMany(targetEntity="AuditAttributeSets", mappedBy="theme")
     */
    protected $attributeSets;

    public function getAttributeSets() {
        return $this->attributeSets;
    }
}