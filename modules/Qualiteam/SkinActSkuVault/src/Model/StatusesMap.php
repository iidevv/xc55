<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVault\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Payment status multilingual data
 *
 * @ORM\Entity
 * @ORM\Table (name="skuvault_statuses_map")
 */
class StatusesMap extends \XLite\Model\AEntity
{
    const DIRECTION_XC_TO_SKUVAULT = 'XS';
    const DIRECTION_SKUVAULT_TO_XC = 'SX';

    /**
     * Unique id
     *
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column         (type="integer", options={"unsigned": true})
     */
    protected $id;

    /**
     * X-Cart payment status
     *
     * @var int
     *
     * @ORM\Column (type="integer", options={"fixed": true}, nullable=true)
     */
    protected $xcartPaymentStatus;

    /**
     * X-Cart fullfilment status
     *
     * @var int
     *
     * @ORM\Column (type="integer", options={"fixed": true}, nullable=true)
     */
    protected $xcartFullfilmentStatus;

    /**
     * Direction
     *
     * @var string
     *
     * @ORM\Column (type="string", options={"fixed": true}, length=16, nullable=true)
     */
    protected $direction;

    /**
     * SkuVault checkout status
     *
     * @var string
     *
     * @ORM\Column (type="string", options={"fixed": true}, length=16, nullable=true)
     */
    protected $skuvaultCheckoutStatus;

    /**
     * SkuVault shipping status
     *
     * @var string
     *
     * @ORM\Column (type="string", options={"fixed": true}, length=16, nullable=true)
     */
    protected $skuvaultShippingStatus;

    /**
     * SkuVault sale state
     *
     * @var string
     *
     * @ORM\Column (type="string", options={"fixed": true}, length=16, nullable=true)
     */
    protected $skuvaultSaleState;

    /**
     * SkuVault payment status
     *
     * @var string
     *
     * @ORM\Column (type="string", options={"fixed": true}, length=16, nullable=true)
     */
    protected $skuvaultPaymentStatus;

    /**
     * SkuVault sale status
     *
     * @var string
     *
     * @ORM\Column (type="string", options={"fixed": true}, length=16, nullable=true)
     */
    protected $skuvaultSaleStatus;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return StatusesMap
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getXcartPaymentStatus()
    {
        return $this->xcartPaymentStatus;
    }

    /**
     * @param string $xcartPaymentStatus
     * @return StatusesMap
     */
    public function setXcartPaymentStatus($xcartPaymentStatus)
    {
        $this->xcartPaymentStatus = $xcartPaymentStatus;
        return $this;
    }

    /**
     * @return string
     */
    public function getXcartFullfilmentStatus()
    {
        return $this->xcartFullfilmentStatus;
    }

    /**
     * @param string $xcartFullfilmentStatus
     * @return StatusesMap
     */
    public function setXcartFullfilmentStatus($xcartFullfilmentStatus)
    {
        $this->xcartFullfilmentStatus = $xcartFullfilmentStatus;
        return $this;
    }

    /**
     * @return string
     */
    public function getDirection()
    {
        return $this->direction;
    }

    /**
     * @param string $direction
     * @return StatusesMap
     */
    public function setDirection($direction)
    {
        $this->direction = $direction;
        return $this;
    }

    /**
     * @return string
     */
    public function getSkuvaultCheckoutStatus()
    {
        return $this->skuvaultCheckoutStatus;
    }

    /**
     * @param string $skuvaultCheckoutStatus
     * @return StatusesMap
     */
    public function setSkuvaultCheckoutStatus($skuvaultCheckoutStatus)
    {
        $this->skuvaultCheckoutStatus = $skuvaultCheckoutStatus;
        return $this;
    }

    /**
     * @return string
     */
    public function getSkuvaultShippingStatus()
    {
        return $this->skuvaultShippingStatus;
    }

    /**
     * @param string $skuvaultShippingStatus
     * @return StatusesMap
     */
    public function setSkuvaultShippingStatus($skuvaultShippingStatus)
    {
        $this->skuvaultShippingStatus = $skuvaultShippingStatus;
        return $this;
    }

    /**
     * @return string
     */
    public function getSkuvaultSaleState()
    {
        return $this->skuvaultSaleState;
    }

    /**
     * @param string $skuvaultSaleState
     * @return StatusesMap
     */
    public function setSkuvaultSaleState($skuvaultSaleState)
    {
        $this->skuvaultSaleState = $skuvaultSaleState;
        return $this;
    }

    /**
     * @return string
     */
    public function getSkuvaultPaymentStatus()
    {
        return $this->skuvaultPaymentStatus;
    }

    /**
     * @param string $skuvaultPaymentStatus
     * @return StatusesMap
     */
    public function setSkuvaultPaymentStatus($skuvaultPaymentStatus)
    {
        $this->skuvaultPaymentStatus = $skuvaultPaymentStatus;
        return $this;
    }

    /**
     * @return string
     */
    public function getSkuvaultSaleStatus()
    {
        return $this->skuvaultSaleStatus;
    }

    /**
     * @param string $skuvaultSaleStatus
     * @return StatusesMap
     */
    public function setSkuvaultSaleStatus($skuvaultSaleStatus)
    {
        $this->skuvaultSaleStatus = $skuvaultSaleStatus;
        return $this;
    }
}
