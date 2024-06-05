<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Returns\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Return reason
 *
 * @ORM\Entity (repositoryClass="QSL\Returns\Model\Repo\ReturnReason")
 * @ORM\Table (name="return_reasons")
 */
class ReturnReason extends \XLite\Model\Base\I18n
{
    /**
     * Unique id
     *
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column         (type="integer", options={ "unsigned": true })
     */
    protected $id;

    /**
     * Position
     *
     * @var string
     *
     * @ORM\Column (type="string", length=64, nullable=true)
     */
    protected $reason;

    /**
     * Position
     *
     * @var integer
     *
     * @ORM\Column (type="integer")
     */
    protected $position = 0;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany (targetEntity="QSL\Returns\Model\OrderReturn", mappedBy="action", cascade={"all"})
     */
    protected $orderReturns;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="QSL\Returns\Model\ReturnReasonTranslation", mappedBy="owner", cascade={"all"})
     */
    protected $translations;

    /**
     * Constructor
     *
     * @param array $data Entity properties OPTIONAL
     */
    public function __construct(array $data = [])
    {
        $this->orderReturns = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

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
     * Set reason
     *
     * @param string $reason
     * @return ReturnReason
     */
    public function setReason($reason)
    {
        $this->setReasonName($reason);
        $this->reason = '';

        return $this;
    }

    /**
     * Get reason
     *
     * @return string
     */
    public function getReason()
    {
        return $this->getReasonName() ?: $this->reason;
    }

    /**
     * Set position
     *
     * @param integer $position
     * @return ReturnReason
     */
    public function setPosition($position)
    {
        $this->position = $position;
        return $this;
    }

    /**
     * Get position
     *
     * @return integer
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getOrderReturns(): \Doctrine\Common\Collections\ArrayCollection
    {
        return $this->orderReturns;
    }

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection $orderReturns
     */
    public function setOrderReturns(\Doctrine\Common\Collections\ArrayCollection $orderReturns): void
    {
        $this->orderReturns = $orderReturns;
    }

    // {{{ Translation Getters / setters

    /**
     * @return string
     */
    public function getReasonName()
    {
        return $this->getTranslationField(__FUNCTION__);
    }

    /**
     * @param string $name
     *
     * @return \XLite\Model\Base\Translation
     */
    public function setReasonName($name)
    {
        return $this->setTranslationField(__FUNCTION__, $name);
    }

    // }}}
}
