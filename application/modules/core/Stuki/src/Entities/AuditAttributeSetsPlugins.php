<?

namespace Entities;

use Stuki\Entity\Entity,
    Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="audit_attribute_set_plugins_xref", indexes={@ORM\index(name="primary_key_idx", columns={"ref_attribute_set", "ref_plugin"})})
 */
class AuditAttributeSetPlugins extends Entity
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    protected $audit_key;

    /**
     * @ORM\ManyToOne(targetEntity="AuditAttributeSets", inversedBy="plugins")
     * @ORM\JoinColumn(name="ref_attribute_set", referencedColumnName="attribute_set_key")
     */
    protected $attributeSet;

    /**
     * @ORM\ManyToOne(targetEntity="AuditPlugins", inversedBy="attributeSets")
     * @ORM\JoinColumn(name="ref_plugin", referencedColumnName="plugin_key")
     */
    protected $plugin;

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