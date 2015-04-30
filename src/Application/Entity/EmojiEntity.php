<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;

/**
 * Emoji Entity
 *
 * @ORM\Table(name="emojis")
 * @ORM\Entity(repositoryClass="Application\Repository\EmojiRepository")
 * @DoctrineAssert\UniqueEntity(fields="name")
 * @ORM\HasLifecycleCallbacks()
 */
class EmojiEntity
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name;

    protected $image;

    protected $imageUploadPath;

    protected $imageUploadDir;

    /**
     * @var string
     *
     * @ORM\Column(name="image_url", type="text", nullable=true)
     */
    protected $imageUrl;

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
     * Who added this place?
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\UserEntity", inversedBy="emojis")
     */
    protected $user;

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

    /*** Image ***/
    public function getImage()
    {
        return $this->image;
    }

    public function setImage(\Symfony\Component\HttpFoundation\File\File $image = null)
    {
        $this->image = $image;

        return $this;
    }

    /*** Image path ***/
    public function getImageUploadPath()
    {
        return $this->imageUploadPath;
    }

    public function setImageUploadPath($imageUploadPath)
    {
        $this->imageUploadPath = $imageUploadPath;

        return $this;
    }

    /*** Image upload dir ***/
    public function getImageUploadDir()
    {
        return $this->imageUploadDir;
    }

    public function setImageUploadDir($imageUploadDir)
    {
        $this->imageUploadDir = $imageUploadDir;

        return $this;
    }

    /*** Image URL ***/
    public function getImageUrl()
    {
        return $this->imageUrl;
    }

    public function setImageUrl($imageUrl)
    {
        $this->imageUrl = $imageUrl;

        return $this;
    }

    /*** Image upload ***/
    public function imageUpload()
    {
        if (null === $this->getImage()) {
            return;
        }

        $slugify = new \Cocur\Slugify\Slugify();

        $filename = $slugify->slugify(
            $this->getImage()->getClientOriginalName()
        );

        $filename .= '_'.sha1(uniqid(mt_rand(), true)).'.'.
            $this->getImage()->guessExtension()
        ;

        $this->getImage()->move(
            $this->getImageUploadDir(),
            $filename
        );

        $this->setImageUrl($this->getImageUploadPath().$filename);

        $this->setImage(null);
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

    /********** Magic Methods **********/
    public function __toString()
    {
        return $this->getName();
    }

    public function toArray()
    {
        return array(
            'name' => $this->getName(),
            'image' => array(
                'url' => $this->getImageUrl(),
            ),
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
