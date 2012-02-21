<?
namespace Entities;

use Stuki\Entity\Entity,
    Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="attachments")
 */
class Attachments extends Entity
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    protected $attachment_key;

    public function getKey() {
        return $this->attachment_key;
    }

    /**
     * @ORM\ManyToOne(targetEntity="Entities")
     * @ORM\JoinColumn(name="ref_entity", referencedColumnName="entity_key")
     */
    protected $entity;

    public function getEntity() {
        return $this->entity;
    }

    public function setEntity(\Entities\Entities $value) {
        $this->entity = $value;
    }


    /**
     * @ORM\Column(type="text")
     */
    protected $filename;

    public function getFilename() {
        return $this->filename;
    }

    public function setFilename($value) {
        $this->filename = $value;
    }


    /**
     * @ORM\Column(name="uploaded_at", type="datetime")
     */
    protected $uploadedAt;

    public function getUploadedAt() {
        return $this->uploadedAt;
    }

    public function setUploadedAt($value) {
        if (!$value instanceof \DateTime)
            throw new \Stuki\Exception('Uploaded at must be a date time object');

        $this->uploadedAt = $value;
    }


    /**
     * @ORM\Column(name="path", type="text")
     */
    protected $path;

    public function getPath() {
        return $this->path;
    }

    public function setPath($value) {
        $this->path = $value;
    }


    /**
     * The description of the attachment
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected $description = '';

    public function getDescription() {
        return $this->description;
    }

    public function setDescription($value) {
        $this->description = $value;
    }


    /**
     * @ORM\PostPersist
     */
    function insert() {
        $this->auditInsert();
    }

    /**
     * @ORM\PostUpdate
     */
    function update() {
        $this->auditUpdate();
    }

    /**
     * @ORM\PreRemove
     */
    function delete() {
        $this->auditDelete();
    }
}