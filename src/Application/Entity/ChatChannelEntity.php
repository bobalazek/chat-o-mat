<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * State Entity
 *
 * @ORM\Table(name="chat_channels")
 * @ORM\Entity(repositoryClass="Application\Repository\ChatChannelRepository")
 * @ORM\HasLifecycleCallbacks()
 * @DoctrineAssert\UniqueEntity(fields="name")
 */
class ChatChannelEntity
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=32, nullable=true)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    protected $description;

    /**
     * @var boolean
     *
     * @ORM\Column(name="private", type="boolean")
     */
    protected $private = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="archived", type="boolean")
     */
    protected $archived = false;

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
     * @ORM\ManyToOne(targetEntity="Application\Entity\UserEntity", inversedBy="chatChannelMessages")
     */
    protected $user;

    /**
     * @var Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Application\Entity\UserEntity", inversedBy="chatChannels")
     * @ORM\JoinTable(
     *      name="chat_channel_users",
     *      joinColumns={@ORM\JoinColumn(name="chat_channel_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")}
     *  )
     */
    protected $users;

    /**
     * @var Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Application\Entity\ChatChannelMessageEntity", mappedBy="chatChannel", cascade={"all"})
     */
    protected $chatChannelMessages;

    /*************** Methods ***************/
    /********** General Methods **********/
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

    /*** Name ***/
    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $slugify = new \Cocur\Slugify\Slugify();

        $name = $slugify->slugify(
            $name
        );

        $this->name = $name;

        return $this;
    }

    /*** Description ***/
    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /*** Archived ***/
    public function getArchived()
    {
        return $this->archived;
    }

    public function setArchived($archived)
    {
        $this->archived = $archived;

        return $this;
    }

    public function isArchived()
    {
        return $this->getArchived();
    }

    /*** Private ***/
    public function getPrivate()
    {
        return $this->private;
    }

    public function setPrivate($private)
    {
        $this->private = $private;

        return $this;
    }

    public function isPrivate()
    {
        return $this->getPrivate();
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

    /*** User ***/
    public function getUser()
    {
        return $this->user;
    }

    public function setUser(\Application\Entity\UserEntity $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /*** Users ***/
    public function getUsers()
    {
        return $this->users;
    }

    public function setUsers($users)
    {
        $this->users = $users;

        return $this;
    }

    /********** Magic Methods **********/
    public function __toString()
    {
        return $this->getName();
    }

    public function toArray()
    {
        return array(
            'id' => $this->getId(),
            'name' => $this->getName(),
            'description' => $this->getDescription(),
        );
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
