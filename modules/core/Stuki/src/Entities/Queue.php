<?
/**
 * This is the queue for Zend\Queue
 */
namespace Entities;

use Stuki\Entity\Entity,
    Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="queue")
 */
class Queue extends Entity
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    protected $queue_id;

    public function getKey() {
        return $this->queue_id;
    }

    /**
     * @ORM\Column(type="string")
     */
    protected $queue_name;

    public function getName() {
        return $this->queue_name;
    }

    public function setName($value) {
        $this->queue_name = $value;
    }

    /**
     * @ORM\Column(type="integer")
     */
    protected $timeout = 30;

    public function getTimeout() {
        return $this->timeout;
    }

    public function setTimeout($value) {
        $this->timeout = $value;
    }

    /**
     * @ORM\OneToMany(targetEntity="Message", mappedBy="queue")
     */
    protected $messages ;

    public function getMessages() {
        return $this->messages;
    }
}