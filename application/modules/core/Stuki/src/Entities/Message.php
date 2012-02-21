<?
/**
 * The queue messages tables for Zend\Queue
 */
namespace Entities;

use Stuki\Entity\Entity,
    Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="message")
 */
class Message extends Entity
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    protected $message_id;

    public function getKey() {
        return $this->message_id;
    }

    /**
     * @ORM\ManyToOne(targetEntity="Queue", inversedBy="messages")
     * @ORM\JoinColumn(name="queue_id", referencedColumnName="queue_id")
     */
    protected $queue;

    public function getQueue() {
        return $this->queue;
    }

    public function setQueue($value) {
        $this->queue = $value;
    }

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $handle;

    public function getHandle() {
        return $this->handle;
    }

    public function setHandle($value) {
        $this->handle = $value;
    }

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $body;

    public function getBody() {
        return $this->body;
    }

    public function setBody($value) {
        $this->body = $value;
    }

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $md5;

    public function getMd5() {
        return $this->md5;
    }

    public function setMd5($value) {
        $this->md5 = $value;
    }

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    protected $timeout;

    public function getTimeout() {
        return $this->timeout;
    }

    public function setTimeout($value) {
        $this->timeout = $value;
    }

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $created;

    public function getCreated() {
        return $this->created;
    }

    public function setCreated($value) {
        $this->created = $value;
    }
}