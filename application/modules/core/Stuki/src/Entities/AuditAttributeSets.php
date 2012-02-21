<?

namespace Entities;

use Stuki\Entity\Entity,
    Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="audit_attribute_sets", indexes={@ORM\index(name="attribute_set_key_idx", columns={"attribute_set_key"})}))
 */
class AuditAttributeSets extends Entity
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
    protected $attribute_set_key;

    /**
     * @ORM\ManyToOne(targetEntity="AuditUsers")
     * @ORM\JoinColumn(name="ref_user", referencedColumnName="user_key")
     */
    protected $user;

    /**
     * @ORM\OneToMany(targetEntity="AuditAttributes", mappedBy="attributeSet")
     */
    protected $attributes;

    /**
     * @ORM\OneToMany(targetEntity="AuditAttributeSetPlugins", mappedBy="attributeSet")
     */
    protected $plugins;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $name;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $description;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $tabs;

    /**
     * @ORM\Column(name="tab_titles", type="array", nullable=true)
     */
    protected $tabTitles;

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