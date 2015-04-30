<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Chat Channel Message Entity
 *
 * @ORM\Table(name="chat_channel_messages")
 * @ORM\Entity(repositoryClass="Application\Repository\ChatChannelMessageRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class ChatChannelMessageEntity
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
     * @ORM\Column(name="content", type="string", length=255)
     */
    protected $content;

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
     * @ORM\ManyToOne(targetEntity="Application\Entity\ChatChannelEntity", inversedBy="chatChannelMessages")
     */
    protected $chatChannel;

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

    /*** Content ***/
    public function getContent()
    {
        return $this->content;
    }

    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /*** HTML ***/
    public function getHtml($app = null)
    {
        $messageParser = new \Application\MessageParser($app);

        return $messageParser->parse(
            $this->getContent()
        );
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

    /*** Time created ***/
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

    /*** Chat Channel ***/
    public function getChatChannel()
    {
        return $this->chatChannel;
    }

    public function setChatChannel(\Application\Entity\ChatChannelEntity $chatChannel = null)
    {
        $this->chatChannel = $chatChannel;

        return $this;
    }

    /********** Magic Methods **********/
    public function __toString()
    {
        return $this->getContent();
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
        $this->setTimeCreated(new \DateTime('now'));
        $this->setTimeUpdated(new \DateTime('now'));
    }
}
