<?

namespace Entities;

use Stuki\Entity\Entity,
    Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="audit_renderers", uniqueConstraints={@ORM\UniqueConstraint(name="model_idx", columns={"model"})}, indexes={@ORM\index(name="renderer_key_idx", columns={"renderer_key"})})
 */
class AuditRenderers extends Entity
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
    protected $renderer_key;

    /**
     * @ORM\Column(name="model", type="string")
     */
    protected $model;

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