<?php
namespace Attachments\Model;

use Stuki\Model\Model,
    Entities\AttributeSets as EntityAttributeSets,
    Stuki\AttributeSets\Exception as Exception
    ;

class Attachments extends Model {

    /**
     * Retrieve one
     */
    public function find($attachment_key) {
        if (!is_int($attachment_key)) throw new \Stuki\Exception('Attachment key must be an integer');
        return $this->getEm()->getRepository('Entities\Attachments')->find($attachment_key);
    }

    /**
     * Retrieve all
     */
    public function findAll($sort = null) {
        if (!$sort) $sort = array('filename' => 'asc');
        return $this->getEm()->getRepository('Entities\Attachments')->findBy(array(), $sort);
    }

    /**
     * Retrieve a subset
     */
    public function findBy($search, $sort = null) {
        if (!$sort) $sort = array('filename' => 'asc');
        return $this->getEm()->getRepository('Entities\Attachments')->findBy($search, $sort);
    }

    /**
     * Retrieve one using an assoc array of params
     */
    public function findOneBy($search) {
        return $this->getEm()->getRepository('Entities\Attachments')->findOneBy($search);
    }

    /**
     * Generate and prepare a path to save the filename
     */
    public function generateFilePath($filename, $prefixPath) {
        $subdir1 = substr(md5(uniqid()), 0, 2);
        $subdir2 = substr(md5(uniqid()), 0, 2);

        // Check for file path uniqueness
        if (is_file($prefixPath . '/' . $subdir1 . '/' . $subdir2 . '/' . $filename))
            return $this->generateFilePath($filename, $prefixPath);

        // Create directories
        if (!is_dir($prefixPath . '/' . $subdir1)) mkdir($prefixPath . '/' . $subdir1);
        if (!is_dir($prefixPath . '/' . $subdir1 . '/' . $subdir2)) mkdir($prefixPath . '/' . $subdir1 . '/' . $subdir2);

        return $prefixPath . '/' . $subdir1 . '/' . $subdir2;
    }

    public function insert (\Entities\Entities $entity, $values) {

        if (!file_exists($values['path']))
            throw new \Stuki\Exception('Attachment file does not exist: ' . $values['path']);

        $attachment = new \Entities\Attachments();

        $attachment->setEntity($entity);
        $attachment->setDescription($values['description']);
        $attachment->setFilename($values['filename']);
        $attachment->setPath($values['path']);
        $attachment->setUploadedAt(new \Datetime());

        $this->getEm()->persist($attachment);
        $this->getEm()->flush();

        // Run events
        $this->events()->trigger('insert', $this, array('attachment' => $attachment));

        return $attachment;
    }

    public function delete(\Entities\Attachments $attachment) {
        @unlink($attachment->getPath());

        // Run events
        $this->events()->trigger('delete', $this, array('attachment' => $attachment));

        $this->getEm()->remove($attachment);
        $this->getEm()->flush();
    }
}
