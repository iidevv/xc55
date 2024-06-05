<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\BackInStock\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Back in stock abstract record
 *
 * @ORM\MappedSuperclass
 */
abstract class ARecord extends \XLite\Model\AEntity
{
    /**
     * State codes
     */
    public const STATE_STANDBY = 1; // Product is out-of-stock
    public const STATE_READY   = 2; // Product is in-stock, but messages is unsend
    public const STATE_SENT    = 3; // Message sent
    public const STATE_BOUNCED = 4; // Message sent but with some errors
    public const STATE_SENDING = 5; // Messages sending in this time

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column (type="integer", options={ "unsigned": true })
     */
    protected $id;

    /**
     * Creation date
     *
     * @var int
     *
     * @ORM\Column (type="integer")
     */
    protected $date;

    /**
     * @var int
     *
     * @ORM\Column (type="integer")
     */
    protected $state = self::STATE_STANDBY;

    /**
     * @var int
     *
     * @ORM\Column (type="integer", nullable=true)
     */
    protected $backDate;

    /**
     * @var int
     *
     * @ORM\Column (type="integer", nullable=true)
     */
    protected $sentDate;

    /**
     * @var int
     *
     * @ORM\Column (type="integer", nullable=true)
     */
    protected $startSendingDate;

    /**
     * Customer email
     *
     * @var string
     *
     * @ORM\Column (type="string", nullable=true)
     */
    protected $email;

    /**
     * @var string
     *
     * @ORM\Column (type="string", length=32, nullable=true)
     */
    protected $hash;

    /**
     * @var \XLite\Model\Product
     *
     * @ORM\ManyToOne (targetEntity="XLite\Model\Product", cascade={"merge","detach"})
     * @ORM\JoinColumn (name="product_id", referencedColumnName="product_id", onDelete="CASCADE")
     */
    protected $product;

    /**
     * Customer
     *
     * @var \XLite\Model\Profile
     *
     * @ORM\ManyToOne (targetEntity="XLite\Model\Profile", cascade={"merge","detach"})
     * @ORM\JoinColumn (name="profile_id", referencedColumnName="profile_id", onDelete="CASCADE")
     */
    protected $profile;

    /**
     * Customer Language code
     *
     * @var string
     *
     * @ORM\Column (type="string", length=2, nullable=true)
     */
    protected $language = '';

    /**
     * Check record waiting state
     *
     * @return boolean
     */
    abstract public function checkWaiting();

    /**
     * Prepare model
     *
     * @ORM\PrePersist
     */
    public function prepareBeforeCreate()
    {
        if (!$this->getDate()) {
            $this->setDate(\XLite\Core\Converter::time());
        }

        if (!$this->getHash()) {
            $this->generateHash();
        }
    }

    /**
     * Generate hash
     *
     * @return static
     */
    public function generateHash()
    {
        $this->setHash(md5(uniqid(\XLite\Core\Converter::time(), true)));

        return $this;
    }

    /**
     * Get customer's email
     *
     * @return string
     */
    public function getCustomerEmail()
    {
        return $this->getEmail();
    }

    // {{{ Logic

    /**
     * Check - customer still wait message or not
     *
     * @return boolean
     */
    public function isWaiting()
    {
        return $this->getState() != static::STATE_SENT;
    }

    /**
     * Mark as backed in stock
     */
    public function markAsBack()
    {
        $this->setBackDate(\XLite\Core\Converter::time());
        $this->setState(static::STATE_READY);
    }

    // }}}

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set date
     *
     * @param integer $date
     *
     * @return static
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
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
     * Set state
     *
     * @param integer $state
     *
     * @return static
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return integer
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set backDate
     *
     * @param integer $backDate
     *
     * @return static
     */
    public function setBackDate($backDate)
    {
        $this->backDate = $backDate;

        return $this;
    }

    /**
     * Get backDate
     *
     * @return integer
     */
    public function getBackDate()
    {
        return $this->backDate;
    }

    /**
     * Set sentDate
     *
     * @param integer $sentDate
     *
     * @return static
     */
    public function setSentDate($sentDate)
    {
        $this->sentDate = $sentDate;

        return $this;
    }

    /**
     * Get sentDate
     *
     * @return integer
     */
    public function getSentDate()
    {
        return $this->sentDate;
    }

    /**
     * Set startSendingDate
     *
     * @param integer $startSendingDate
     *
     * @return static
     */
    public function setStartSendingDate($startSendingDate)
    {
        $this->startSendingDate = $startSendingDate;

        return $this;
    }

    /**
     * Get startSendingDate
     *
     * @return integer
     */
    public function getStartSendingDate()
    {
        return $this->startSendingDate;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return static
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set hash
     *
     * @param string $hash
     *
     * @return static
     */
    public function setHash($hash)
    {
        $this->hash = $hash;

        return $this;
    }

    /**
     * Get hash
     *
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * Set product
     *
     * @param \XLite\Model\Product $product
     *
     * @return static
     */
    public function setProduct(\XLite\Model\Product $product = null)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * Get product
     *
     * @return \XLite\Model\Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Set profile
     *
     * @param \XLite\Model\Profile $profile
     *
     * @return static
     */
    public function setProfile(\XLite\Model\Profile $profile = null)
    {
        $this->profile = $profile;

        return $this;
    }

    /**
     * Get profile
     *
     * @return \XLite\Model\Profile
     */
    public function getProfile()
    {
        return $this->profile;
    }

    /**
     * Set language
     *
     * @param string $language
     *
     * @return static
     */
    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Get language code
     *
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Return extended product name for record
     *
     * @return string
     */
    public function getExtendedRecordProductName()
    {
        $name = '';

        if ($product = $this->getProduct()) {
            $name = $product->getName();
        }

        return $name;
    }
}
