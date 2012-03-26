<?
namespace Attachments\Entity;

use Stuki\Entity\Entity,
    Doctrine\ORM\Mapping AS ORM;

class Attachments extends Entity
{
    protected $attachment_key;

    public function getKey() {
        return $this->attachment_key;
    }

    protected $entity;

    public function getEntity() {
        return $this->entity;
    }

    public function setEntity(\Entities\Entities $value) {
        $this->entity = $value;
        return $this;
    }

    protected $filename;

    public function getFilename() {
        return $this->filename;
    }

    public function setFilename($value) {
        $this->filename = $value;
        return $this;
    }

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

    protected $md5;

    public function getMd5() {
        return $this->md5;
    }

    public function setMd5($value) {
        $this->md5 = $value;
        return $this;
    }

    protected $path;

    public function getPath() {
        return $this->path;
    }

    public function setPath($value) {
        $this->path = $value;
        return $this;
    }

    protected $description = '';

    public function getDescription() {
        return $this->description;
    }

    public function setDescription($value) {
        $this->description = $value;
        return $this;
    }
}