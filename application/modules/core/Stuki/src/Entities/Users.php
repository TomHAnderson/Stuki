<?
/**
 * This is the users table.
 */
namespace Entities;

use Stuki\Entity\Entity,
    Doctrine\ORM\Mapping AS ORM;

/**
 *
 * @ORM\Entity
 * @ORM\Table(name="users", uniqueConstraints={@ORM\UniqueConstraint(name="idx_username", columns={"username"})})
 */
class Users extends Entity
{

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    protected $user_key;

    public function getKey() {
        return $this->user_key;
    }

    public function getUserId() {
        return $this->user_key;
    }

    /**
     * @ORM\Column(name="username", type="string", nullable=true)
     */
    protected $username;

    public function getUsername() {
        return $this->username;
    }

    public function setUsername($value) {
        $this->username = $value;
        return $this;
    }

    /**
     * @ORM\Column(name="email", type="string")
     */
    protected $email;

    public function getEmail() {
        return $this->email;
    }

    public function setEmail($value) {
        $this->email = $value;
        return $this;
    }

    /**
     * @ORM\Column(name="name", type="string", nullable=true)
     */
    protected $name;

    public function getDisplayName() {
        return $this->name;
    }

    public function setDisplayName($value) {
        $this->name = $value;
        return $this;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($value) {
        $this->name = $value;
        return $this;
    }

    /**
     * @ORM\Column(name="password", type="string", nullable=true)
     */
    protected $password;

    public function getPassword() {
        return $this->password;
    }

    public function setPassword($value) {
        $this->password = $value;
        return $this;
    }

    /**
     * @ORM\Column(name="last_login_at", type="datetime", nullable=true)
     */
    protected $lastLogin;

    public function getLastLogin() {
        return $this->lastLogin;
    }

    public function setLastLogin($value) {
        if (!$value instanceof \Datetime)
            throw new \Stuki\Exception("Invalid value passed to setLastLogin; must be a Datetime object");
        $this->lastLogin = $value;
        return $this;
    }


    /**
     * @ORM\Column(name="last_ip", type="integer", nullable=true)
     */
    protected $lastIp;

    public function getLastIp() {
        return $this->lastIp;
    }

    public function setLastIp($value) {
        $this->lastIp = $value;
        return $this;
    }


    /**
     * @ORM\Column(name="registered_at", type="datetime")
     */
    protected $registeredAt;

    public function getRegisteredAt() {
        return $this->registeredAt;
    }

    // Alias for zfcusers
    public function getRegisterTime() {
        return $this->registeredAt;
    }

    public function setRegisteredAt($value) {
        if (!$value instanceof \Datetime)
            throw new \Stuki\Exception("Invalid value passed to setLastLogin; must be a Datetime object");
        $this->registeredAt = $value;
        return $this;
    }

    // Alias for zfcusers
    public function setRegisterTime($value) {
        if (!$value instanceof \Datetime)
            throw new \Stuki\Exception("Invalid value passed to setLastLogin; must be a Datetime object");
        $this->registeredAt = $value;
        return $this;
    }


    /**
     * @ORM\Column(name="register_ip", type="integer", nullable=true)
     */
    protected $registerIp;

    public function getRegisterIp() {
        return $this->registerIp;
    }

    public function setRegisterIp($value) {
        $this->registerIp = $value;
        return $this;
    }


    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    protected $isActive = false;

    public function getIsActive() {
        return $this->isActive;
    }

    public function setIsActive($value) {
        $this->isActive = $value;
    }

    // Aliases for ZfcUser
    public function getActive() {
        return $this->isActive;
    }

    public function setActive($value) {
        $this->isActive = $value;
    }


    /**
     * @ORM\Column(name="is_enabled", type="boolean")
     */
    protected $isEnabled = false;

    public function getIsEnabled() {
        return $this->isEnabled;
    }

    public function setIsEnabled($value) {
        $this->isEnabled = $value;
    }

    // Aliases for ZfcUser
    public function getEnabled() {
        return $this->isEnabled;
    }

    public function setEnabled($value) {
        $this->isEnabled = $value;
    }


    /**
     * @ORM\OneToMany(targetEntity="Entities", mappedBy="user")
     */
    protected $entities;

    public function getEntities() {
        return $this->entities;
    }
}