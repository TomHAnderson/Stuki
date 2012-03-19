<?
namespace Entities;

use Stuki\Entity\Entity,
    Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
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
        return $this;
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
        return $this;
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
        return $this;
    }

    /**
     * @ORM\Column(name="md5", type="string")
     */
    protected $md5;

    public function getMd5() {
        return $this->md5;
    }

    public function setMd5($value) {
        $this->md5 = $value;
        return $this;
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
        return $this;
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
        return $this;
    }
}