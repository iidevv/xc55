<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\VendorMessages\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Message
 *
 * @ORM\Entity
 * @ORM\Table (name="vendor_convo_message_reads",
 *      uniqueConstraints={
 *          @ORM\UniqueConstraint (name="id", columns={"message_id", "profile_id"})
 *      }
 *     )
 * @ORM\HasLifecycleCallbacks
 */
class MessageRead extends \XLite\Model\AEntity
{
    /**
     * Unique ID
     *
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column         (type="integer", options={ "unsigned": true })
     */
    protected $id;

    /**
     * Read date
     *
     * @var integer
     *
     * @ORM\Column (type="integer", options={ "unsigned": true })
     */
    protected $date;

    /**
     * Message
     *
     * @var \XC\VendorMessages\Model\Message
     *
     * @ORM\ManyToOne  (targetEntity="XC\VendorMessages\Model\Message", inversedBy="readers")
     * @ORM\JoinColumn (name="message_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $message;

    /**
     * Reader
     *
     * @var \XLite\Model\Profile
     *
     * @ORM\ManyToOne  (targetEntity="XLite\Model\Profile")
     * @ORM\JoinColumn (name="profile_id", referencedColumnName="profile_id", onDelete="CASCADE")
     */
    protected $reader;

    /**
     * Prepare date before create entity
     *
     * @ORM\PrePersist
     */
    public function prepareDate()
    {
        if (!$this->getDate()) {
            $this->setDate(\XLite\Core\Converter::time());
        }
    }

    /**
     * Get ID
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get date
     *
     * @return integer
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set date
     *
     * @param integer $date Set date
     *
     * @return MessageRead
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get message
     *
     * @return Message
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set message
     *
     * @param Message $message
     *
     * @return MessageRead
     */
    public function setMessage(Message $message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get reader
     *
     * @return \XLite\Model\Profile
     */
    public function getReader()
    {
        return $this->reader;
    }

    /**
     * Set reader
     *
     * @param \XLite\Model\Profile $reader Reader
     *
     * @return MessageRead
     */
    public function setReader(\XLite\Model\Profile $reader)
    {
        $this->reader = $reader;

        return $this;
    }

    /**
     * Check is own
     *
     * @param \XLite\Model\Profile $profile
     *
     * @return boolean
     */
    public function isOwn($profile)
    {
        if ($profile && $this->getReader() && $this->getReader()->getProfileId() === $profile->getProfileId()) {
            return true;
        }

        return false;
    }
}
