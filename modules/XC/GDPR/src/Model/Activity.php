<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\GDPR\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table (
 *     name="gdpr_activities",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint (name="item_type", columns={"item","type"})
 *     }
 * )
 */
class Activity extends \XLite\Model\AEntity
{
    public const TYPE_MODULE   = 'module';
    public const TYPE_PAYMENT  = 'payment';
    public const TYPE_SHIPPING = 'shipping';
    public const TYPE_ADMIN    = 'admin';

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column (type="integer")
     */
    protected $id;

    /**
     * Item (activity name or short description)
     *
     * @var string
     *
     * @ORM\Column (type="string")
     */
    protected $item;

    /**
     * Item (activity name or short description)
     *
     * @var string
     *
     * @ORM\Column (type="text")
     */
    protected $details;

    /**
     * @var string
     *
     * @ORM\Column (type="string")
     */
    protected $type;

    /**
     * Timestamp
     *
     * @var string
     *
     * @ORM\Column (type="string")
     */
    protected $date;

    /**
     * Return Id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Return Item
     *
     * @return string
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * Set Item
     *
     * @param string $item
     *
     * @return $this
     */
    public function setItem($item)
    {
        $this->item = mb_substr($item, 0, 255);
        return $this;
    }

    /**
     * Return Type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set Type
     *
     * @param string $type
     *
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Return Details
     *
     * @return array
     */
    public function getDetails()
    {
        return json_decode($this->details, true) ?: [];
    }

    /**
     * Set Details
     *
     * @param array $details
     *
     * @return $this
     */
    public function setDetails(array $details)
    {
        $this->details = json_encode($details);
        return $this;
    }

    /**
     * Return Date
     *
     * @return string
     */
    public function getDate()
    {
        return (int) $this->date;
    }

    /**
     * Set Date
     *
     * @param string $date
     *
     * @return $this
     */
    public function setDate($date)
    {
        $this->date = $date;
        return $this;
    }
}
