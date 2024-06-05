<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Shipping;

use Doctrine\ORM\Mapping as ORM;

/**
 * Shipping method multilingual data
 *
 * @ORM\Entity
 * @ORM\Table (name="shipping_method_translations",
 *      indexes={
 *          @ORM\Index (name="ci", columns={"code","id"}),
 *          @ORM\Index (name="id", columns={"id"})
 *      }
 * )
 */
class MethodTranslation extends \XLite\Model\Base\Translation
{
    /**
     * Shipping method name
     *
     * @var string
     *
     * @ORM\Column (type="string", length=255, nullable=false)
     */
    protected $name = '';

    /**
     * Shipping delivery time (for offline methods)
     *
     * @var string
     *
     * @ORM\Column (type="string", length=255, nullable=true)
     */
    protected $deliveryTime = '';

    /**
     * Admin description
     *
     * @var string
     *
     * @ORM\Column (type="text")
     */
    protected $adminDescription = '';

    /**
     * @var \XLite\Model\Shipping\Method
     *
     * @ORM\ManyToOne (targetEntity="XLite\Model\Shipping\Method", inversedBy="translations")
     * @ORM\JoinColumn (name="id", referencedColumnName="method_id", onDelete="CASCADE")
     */
    protected $owner;

    /**
     * Set name
     *
     * @param string $name
     * @return MethodTranslation
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set deliveryTime
     *
     * @param string $deliveryTime
     * @return MethodTranslation
     */
    public function setDeliveryTime($deliveryTime)
    {
        $this->deliveryTime = $deliveryTime;
        return $this;
    }

    /**
     * Get deliveryTime
     *
     * @return string
     */
    public function getDeliveryTime()
    {
        return $this->deliveryTime;
    }

    /**
     * Get label_id
     *
     * @return integer
     */
    public function getLabelId()
    {
        return $this->label_id;
    }

    /**
     * Set code
     *
     * @param string $code
     * @return MethodTranslation
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set adminDescription
     *
     * @param string $adminDescription
     *
     * @return MethodTranslation
     */
    public function setAdminDescription($adminDescription)
    {
        $this->adminDescription = $adminDescription;
        return $this;
    }

    /**
     * Admin description getter
     *
     * @return string
     */
    public function getAdminDescription()
    {
        return $this->adminDescription;
    }
}
