<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * User Entity
 *
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="Application\Repository\UserRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class UserEntity
    implements AdvancedUserInterface, \Serializable
{
    /*************** Variables ***************/
    /********** General Variables **********/
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * What is the locale for this user?
     *
     * @var string
     *
     * @ORM\Column(name="locale", type="string", length=8, nullable=true)
     */
    protected $locale = 'en_US';

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=64, unique=true)
     */
    protected $username;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=64, unique=true)
     */
    protected $email;

    /**
     * @var string
     *
     * @ORM\Column(name="roles", type="string", length=255)
     */
    protected $roles;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255)
     */
    protected $password;

    protected $plainPassword;

    /**
     * @var string
     *
     * @ORM\Column(name="salt", type="string", length=255, nullable=true)
     */
    protected $salt;

    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=255, nullable=true)
     */
    protected $token;

    /**
     * @var string
     *
     * @ORM\Column(name="access_token", type="string", length=255, nullable=true)
     */
    protected $accessToken;

    /**
     * @var boolean
     *
     * @ORM\Column(name="enabled", type="boolean")
     */
    protected $enabled = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="locked", type="boolean")
     */
    protected $locked = false;

    /**
     * @var string
     *
     * @ORM\Column(name="reset_password_code", type="string", length=255, nullable=true, unique=true)
     */
    protected $resetPasswordCode;

    /**
     * @var string
     *
     * @ORM\Column(name="activation_code", type="string", length=255, nullable=true, unique=true)
     */
    protected $activationCode;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="time_last_active", type="datetime", nullable=true)
     */
    protected $timeLastActive;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="time_created", type="datetime")
     */
    protected $timeCreated;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="time_updated", type="datetime")
     */
    protected $timeUpdated;

    /***** Relationship Variables *****/
    /**
     * @ORM\OneToOne(targetEntity="Application\Entity\ProfileEntity", mappedBy="user", cascade={"all"})
     **/
    protected $profile;

    /**
     * @var Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Application\Entity\EmojiEntity", mappedBy="user")
     */
    protected $emojis;

    /**
     * @ORM\ManyToMany(targetEntity="Application\Entity\ChatChannelEntity", mappedBy="users")
     */
    private $chatChannels;

    /**
     * @var Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Application\Entity\ChatChannelMessageEntity", mappedBy="user", cascade={"all"})
     */
    protected $chatChannelMessages;

    /**
     * @var Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Application\Entity\ChatUserMessageEntity", mappedBy="user", cascade={"all"})
     */
    protected $chatUserMessages;

    /**
     * @var Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Application\Entity\ChatUserMessageEntity", mappedBy="userFrom", cascade={"all"})
     */
    protected $chatUserMessagesSent;

    /***** Other Variables *****/
    protected $expired = false; // userExpired / accountExpired
    protected $credentialsExpired = false;

    /*************** Methods ***************/
    /***** Constructor *****/
    public function __construct()
    {
        $this->setSalt(
            md5(uniqid(null, true))
        );

        $this->setToken(
            md5(uniqid(null, true))
        );

        $this->setAccessToken(
            md5(uniqid(null, true))
        );

        $this->setActivationCode(
            md5(uniqid(null, true))
        );

        $this->setResetPasswordCode(
            md5(uniqid(null, true))
        );
    }

    /***** Getters, Setters and Other stuff *****/
    /*** Id ***/
    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /*** Locale ***/
    public function getLocale()
    {
        return $this->locale;
    }

    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /*** Username ***/
    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /*** Email ***/
    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /*** Password ***/
    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        if ($password) {
            $this->password = $password;
        }

        return $this;
    }

    /*** Plain password ***/
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    public function setPlainPassword($plainPassword, \Symfony\Component\Security\Core\Encoder\EncoderFactory $encoderFactory = null)
    {
        $this->plainPassword = $plainPassword;

        if ($encoderFactory) {
            $encoder = $encoderFactory->getEncoder($this);

            $password = $encoder->encodePassword(
                $this->getPlainPassword(),
                $this->getSalt()
            );

            $this->setPassword($password);
        }

        return $this;
    }

    /*** Salt ***/
    public function getSalt()
    {
        return $this->salt;
    }

    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /*** Token ***/
    public function getToken()
    {
        return $this->token;
    }

    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /*** Access Token ***/
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    /*** Enabled ***/
    public function getEnabled()
    {
        return $this->enabled;
    }

    public function isEnabled()
    {
        return $this->getEnabled();
    }

    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function enable()
    {
        $this->setEnabled(true);

        return $this;
    }

    public function disable()
    {
        $this->setEnabled(false);

        return $this;
    }

    /*** Locked ***/
    public function getLocked()
    {
        return $this->locked;
    }

    public function isLocked()
    {
        return $this->getLocked();
    }

    public function setLocked($locked)
    {
        $this->locked = $locked;

        return $this;
    }

    public function lock($reason = '')
    {
        $this->setLocked(true);

        return $this;
    }

    public function isAccountNonLocked()
    {
        return ! $this->isLocked();
    }

    /*** Reset password code ***/
    public function getResetPasswordCode()
    {
        return $this->resetPasswordCode;
    }

    public function setResetPasswordCode($resetPasswordCode)
    {
        $this->resetPasswordCode = $resetPasswordCode;

        return $this;
    }

    /*** Activate account code ***/
    public function getActivationCode()
    {
        return $this->activationCode;
    }

    public function setActivationCode($activationCode)
    {
        $this->activationCode = $activationCode;

        return $this;
    }

    /*** Time last active ***/
    public function getTimeLastActive()
    {
        return $this->timeLastActive;
    }

    public function setTimeLastActive(\DateTime $timeLastActive = null)
    {
        $this->timeLastActive = $timeLastActive;

        return $this;
    }

    /*** Time created ***/
    public function getTimeCreated()
    {
        return $this->timeCreated;
    }

    public function setTimeCreated(\DateTime $timeCreated)
    {
        $this->timeCreated = $timeCreated;

        return $this;
    }

    /*** Time updated ***/
    public function getTimeUpdated()
    {
        return $this->timeUpdated;
    }

    public function setTimeUpdated(\DateTime $timeUpdated)
    {
        $this->timeUpdated = $timeUpdated;

        return $this;
    }

    /*** Expired ***/
    public function getExpired()
    {
        return $this->expired;
    }

    public function isExpired()
    {
        return $this->getExpired();
    }

    public function isAccountNonExpired()
    {
        return ! $this->getExpired();
    }

    /*** Credentials expired ***/
    public function getCredentialsExpired()
    {
        return $this->credentialsExpired;
    }

    public function isCredentialsExpired()
    {
        return $this->getCredentialsExpired();
    }

    public function isCredentialsNonExpired()
    {
        return ! $this->getExpired();
    }

    /*** Roles ***/
    public function getRoles()
    {
        $roles = $this->roles;

        if (is_string($roles)) {
            $roles = explode(',', $roles);
        } else {
            $roles = array();
        }

        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles($roles)
    {
        if (is_array($roles)) {
            $roles = implode(',', $roles);
        }

        $this->roles = $roles;

        return $this;
    }

    public function isGranted($role)
    {
        return in_array($role, $this->getRoles());
    }

    /*** Profile ***/
    public function getProfile()
    {
        return $this->profile;
    }

    public function setProfile(\Application\Entity\ProfileEntity $profile)
    {
        $this->profile = $profile;

        $this->getProfile()->setUser($this);

        return $this;
    }

    /*** Chat Channels ***/
    public function getChatChannels()
    {
        return $this->chatChannels->toArray();
    }

    public function setChatChannels($chatChannels)
    {
        $this->chatChannels = $chatChannels;

        return $this;
    }

    /*** Chat Channel Messages ***/
    public function getChatChannelMessages()
    {
        return $this->chatChannelMessages->toArray();
    }

    public function setChatChannelMessages($chatChannelMessages)
    {
        $this->chatChannelMessages = $chatChannelMessages;

        return $this;
    }

    /*** Chat User Messages ***/
    public function getChatUserMessages()
    {
        return $this->chatUserMessages->toArray();
    }

    public function setChatUserMessages($chatUserMessages)
    {
        $this->chatUserMessages = $chatUserMessages;

        return $this;
    }

    /*** Chat User Messages Sent ***/
    public function getChatUserMessagesSent()
    {
        return $this->chatUserMessagesSent->toArray();
    }

    public function setChatUserMessagesSent($chatUserMessagesSent)
    {
        $this->chatUserMessagesSent = $chatUserMessagesSent;

        return $this;
    }

    /***** Other AdvancedUserInterface Methods *****/
    public function isEqualTo(AdvancedUserInterface $user)
    {
        if (! $user instanceof AdvancedUserInterface) {
            return false;
        }

        if ($this->password !== $user->getPassword()) {
            return false;
        }

        if ($this->getSalt() !== $user->getSalt()) {
            return false;
        }

        if ($this->username !== $user->getUsername()) {
            return false;
        }

        return true;
    }

    public function eraseCredentials()
    {
        $this->setPlainPassword(null);
    }

    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->email,
            $this->password,
            $this->salt,
        ));
    }

    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->username,
            $this->email,
            $this->password,
            $this->salt,
        ) = unserialize($serialized);
    }

    /********** Magic Methods **********/
    public function __toString()
    {
        return $this->getUsername()
            ? $this->getUsername()
            : ''
        ;
    }

    public function toArray()
    {
        $data = array();

        $data['id'] = $this->getId();
        $data['username'] = $this->getUsername();
        $data['email'] = $this->getEmail();
        $data['image'] = array(
            'url' => $this->getProfile()->getImageUrl(),
        );

        return $data;
    }

    /********** Callback Methods **********/
    /**
     * @ORM\PreUpdate
     */
    public function preUpdate()
    {
        $this->setTimeUpdated(new \DateTime('now'));
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->setTimeUpdated(new \DateTime('now'));
        $this->setTimeCreated(new \DateTime('now'));
    }
}
