<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Returns\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Return action
 *
 * @ORM\Entity (repositoryClass="\QSL\Returns\Model\Repo\ReturnAction")
 * @ORM\Table (name="return_actions")
 */
class ReturnAction extends \XLite\Model\Base\I18n
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
     * Action
     *
     * @var string
     *
     * @ORM\Column (type="string", length=255, nullable=true)
     */
    protected $action;

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
     * @ORM\OneToMany (targetEntity="QSL\Returns\Model\OrderReturn", mappedBy="reason", cascade={"all"})
     */
    protected $orderReturns;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="QSL\Returns\Model\ReturnActionTranslation", mappedBy="owner", cascade={"all"})
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
     * Set action
     *
     * @param string $action
     * @return ReturnAction
     */
    public function setAction($action)
    {
        $this->setActionName($action);
        $this->action = '';

        return $this;
    }

    /**
     * Get action
     *
     * @return string
     */
    public function getAction()
    {
        return $this->getActionName() ?: $this->action;
    }

    /**
     * Set position
     *
     * @param integer $position
     * @return ReturnAction
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
    public function getActionName()
    {
        return $this->getTranslationField(__FUNCTION__);
    }

    /**
     * @param string $name
     *
     * @return \XLite\Model\Base\Translation
     */
    public function setActionName($name)
    {
        return $this->setTranslationField(__FUNCTION__, $name);
    }

    // }}}
}
