<?php

namespace Stuki\Model;

use Stuki\Model\Model as StukiModel,
    Zend\Queue\Message,
    Zend\Queue\Queue as ZendQueue;

class Queue extends StukiModel {
    private $attributeSets;
    private $entities;
    private $searchIndex;

    # abbrev function name because di wasn't working
    public function setAtt(\Stuki\Model\AttributeSets $att) {
        $this->attributeSets = $att;
    }

    public function setEntities(\Stuki\Model\Entities $entities) {
        $this->entities = $entities;
    }

    public function setSearchIndex(\Search\Model\Index $searchIndex) {
        $this->searchIndex = $searchIndex;
    }

    public function process($count) {
        $queue = $this->getQueue();
        $messages = $queue->receive($count);
        foreach ($messages as $i => $message) {
            $action = strtok($message->body, ':');
            $key = strtok(':');
            $extra = strtok(':');

            switch ($action) {
                case 'INDEX':
                    // Reindex
                    $entity = $this->entities->find((int)$key);
                    $this->searchIndex->index($entity);
                    $queue->deleteMessage($message);
                    break;

                case 'TITLE':
                    // Retitle and reindex
                    $entity = $this->entities->find((int)$key);
                    $this->searchIndex->index($this->entities->buildTitle($entity));
                    $queue->deleteMessage($message);
                    break;

                case 'ATTRIBUTESET_RETITLE_ALL':
                    // Retitle and reindex all matching attribute sets
                    $attributeSet = $this->attributeSets->find((int)$key);
                    foreach ($attributeSet->getEntities() as $entity) {
                        $queue->send('TITLE:' . $entity->getKey());
                    }
                    $queue->deleteMessage($message);
                    break;

                case 'REINDEXALL':
                    // Rebuild the complete index
                    $this->entities->findAll();

                    # FIXME:  finding all entities this way is expensive
                    foreach ($this->entities->findAll() as $entity) {
                        $queue->send('INDEX:' . $entity->getKey());
                    }
                    $queue->deleteMessage($message);
                    break;

                case 'OPTIMIZE':
                    $this->searchIndex->optimize();
                    $queue->deleteMessage($message);
                    break;

                default:
                    break;
            }
        }
    }
}